<?php

namespace App\Helpers;

use App\Enums\EarningType;
use App\Models\Account;
use App\Models\Commission;
use App\Models\Leaderboard;
use App\Models\ReferralCode;
use App\Models\ReferralUser;
use App\Models\User;
use App\Models\UserCoin;
use App\Models\Withdraw;
use App\Services\Admin\ProfileRankService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ReferralHelper
{
    private ReferralUser $referralUser;

    private array $commissions = [];

    private string $commissionType = 'signup';

    private User $currentUser;

    private const MAX_RETRIES = 3;

    private const RETRY_DELAY_MS = 100;

    /**
     * @throws Throwable
     */
    public function createReferralChain(User $user, $referrerAndCode): void
    {
        DB::beginTransaction();
        try {
            $this->currentUser = $user;
            $this->referralUser = ReferralUser::create([
                'user_id' => $user->id,
                'referrer_id' => $referrerAndCode->user->id ?? 1,
                'referral_code_id' => $referrerAndCode->id,
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateReferralChain(User $user): void
    {
        $this->currentUser = $user;
        $this->referralUser = ReferralUser::where('user_id', $user->id)->first();
    }

    /**
     * @throws Exception|Throwable
     */
    public function distributeReferralPoints(string $commissionType = 'signup'): void
    {
        if (!$this->referralUser?->referrer || !$this->referralUser?->user) {
            throw new Exception('Invalid referral user or referrer.');
        }
        $this->commissionType = $commissionType;

        DB::beginTransaction();
        try {
            $commissionAmounts = config('commissions.' . $this->commissionType);
            $currentReferrer = $this->referralUser->referrer;
            $processedUsers = [$this->currentUser->id];

            // First level commission
            $this->createCommission(1, $this->currentUser, $currentReferrer, $commissionAmounts);

            // Process higher levels
            $level = 2;
            while ($currentReferrer && $level <= count($commissionAmounts)) {
                if (! isset($commissionAmounts[$level])) {
                    throw new Exception("Commission amount not defined for level $level.");
                }

                if (in_array($currentReferrer->id, $processedUsers)) {
                    throw new Exception('Circular reference detected in referral chain.');
                }
                $processedUsers[] = $currentReferrer->id;

                $this->createCommission($level, $currentReferrer, $this->currentUser, $commissionAmounts);

                $nextReferrer = ReferralUser::where('user_id', $currentReferrer->id)->first();
                $currentReferrer = $nextReferrer?->referrer;
                $level++;
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createCommission(int $level, User $user, User $fromUser, array $amounts): void
    {
        $this->commissions[$level] = new Commission([
            'user_id' => $user->id,
            'from_user_id' => $fromUser->id,
            'level' => $level,
            'amount' => $amounts[$level],
        ]);

        $this->commissions[$level]->save();
    }

    /**
     * @throws Exception|Throwable
     */
    public function updateReferralLeaderboard(): void
    {
        $profileRankService = app(ProfileRankService::class);
        foreach ($this->commissions as $commission) {
            $retries = 0;
            $success = false;

            while (! $success && $retries < self::MAX_RETRIES) {
                DB::beginTransaction();
                try {
                    // Get commission stats
                    $stats = DB::table('commissions')
                        ->selectRaw('
                        user_id,
                        COUNT(from_user_id) AS total_nodes,
                        SUM(amount) AS total_commissions
                    ')
                        ->where('user_id', $commission->user_id)
                        ->groupBy('user_id')
                        ->first();

                    if (! $stats) {
                        DB::commit();

                        continue;
                    }

                    // Get the sum of the totalWithdrawn amount
                    $totalWithdrawn = Withdraw::where('user_id', $commission->user_id)
                        ->sum('amount');

                    // Lock and update account
                    $account = Account::where('user_id', $commission->user_id)
                        ->lockForUpdate()
                        ->first();

                    if ($account) {
                        $account->balance = $stats->total_commissions - ($totalWithdrawn ?? 0);
                        $account->total_withdrawn = (float) ($totalWithdrawn ?? 0);
                        $account->save();
                    } else {
                        Account::create([
                            'user_id' => $stats->user_id,
                            'balance' => $stats->total_commissions - ($totalWithdrawn ?? 0),
                            'total_withdrawn' => 0,
                        ]);
                    }

                    if ($this->commissionType === 'signup') {
                        // Calculate coins
                        $earnedCoins = $profileRankService->getEarnedCoins($stats->total_nodes);

                        // Calculate profile rank
                        $profileRank = $profileRankService->getRankName($stats->total_nodes);

                        // before inserting into history, check if a rank exists or not if not, then create it
                        if (!UserCoin::where('user_id', $stats->user_id)->where('rank', $profileRank)->exists()) {
                            // Insert into history
                            UserCoin::create([
                                'user_id' => $stats->user_id,
                                'coins' => $earnedCoins,
                                'reason' => EarningType::ReferralEarnings,
                                'rank' => $profileRank,
                            ]);
                        }

                        // Calculate total coins (audit history sum)
                        $totalCoins = UserCoin::where('user_id', $stats->user_id)->sum('coins');

                        // Update the leaderboard with total coins and rank
                        Leaderboard::updateOrCreate(
                            ['user_id' => $stats->user_id],
                            [
                                'total_commissions' => $stats->total_commissions,
                                'total_nodes' => $stats->total_nodes,
                                'total_earned_coins' => $totalCoins,
                                'profile_rank' => $profileRank,
                            ]
                        );
                    } else {
                        // Fallback to old leaderboard update if coins disabled
                        Leaderboard::updateOrCreate(
                            ['user_id' => $stats->user_id],
                            [
                                'total_commissions' => $stats->total_commissions,
                                'total_nodes' => $stats->total_nodes,
                            ]
                        );
                    }

                    DB::commit();
                    $success = true;
                } catch (Exception $e) {
                    DB::rollBack();
                    $retries++;

                    if ($retries >= self::MAX_RETRIES) {
                        throw $e;
                    }

                    // Add exponential backoff delay
                    usleep(self::RETRY_DELAY_MS * pow(2, $retries - 1) * 1000);
                }
            }
        }
    }

    /**
     * @throws Throwable
     */
    public function generateReferralCode(): string
    {
        DB::beginTransaction();
        try {
            $referralCode = ReferralCode::create([
                'code' => self::generateCode(),
                'type' => 'user',
                'user_id' => $this->currentUser->id,
            ])->code;
            DB::commit();

            return $referralCode;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected static function generateCode(int $length = 8, string $prefix = 'REF'): string
    {
        $code = $prefix . '-' . strtoupper(Str::random($length));

        // Optionally ensure uniqueness (safe for a small scale, refactor for scaling)
        while (ReferralCode::where('code', $code)->exists()) {
            $code = $prefix . '-' . strtoupper(Str::random($length));
        }

        return $code;
    }
}
