<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\Commission;
use App\Models\Leaderboard;
use App\Models\ReferralCode;
use App\Models\ReferralUser;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralHelper
{
    private $referralUser;
    private $commissions = [];
    private $currentUser;
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MS = 100;

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

    /**
     * @throws Exception
     */
    public function distributeReferralPoints(): void
    {
        if (!$this->referralUser?->referrer || !$this->referralUser?->user) {
            throw new Exception('Invalid referral user or referrer.');
        }

        DB::beginTransaction();
        try {
            $commissionAmounts = config('commissions.levels');
            $currentReferrer = $this->referralUser->referrer;
            $processedUsers = [$this->currentUser->id];

            // First level commission
            $this->createCommission(1, $this->currentUser, $currentReferrer, $commissionAmounts);

            // Process higher levels
            $level = 2;
            while ($currentReferrer && $level <= count($commissionAmounts)) {
                if (!isset($commissionAmounts[$level])) {
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
        $this->commissions[$level] = Commission::create([
            'user_id' => $user->id,
            'from_user_id' => $fromUser->id,
            'level' => $level,
            'amount' => $amounts[$level],
        ]);
    }

    /**
     * @throws Exception
     */
    public function updateLeaderboard(): void
    {
        foreach ($this->commissions as $commission) {
            $retries = 0;
            $success = false;

            while (!$success && $retries < self::MAX_RETRIES) {
                try {
                    DB::beginTransaction();

                    // Calculate stats with a shared lock
                    $stats = DB::table('commissions')
                        ->selectRaw('
                            user_id,
                            COUNT(from_user_id) AS total_nodes,
                            SUM(amount) AS total_commissions
                        ')
                        ->where('user_id', $commission->user_id)
                        ->groupBy('user_id')
                        ->sharedLock()
                        ->first();

                    if (!$stats) {
                        DB::commit();
                        continue;
                    }

                    // Get withdrawn amount with a shared lock
                    $withdrawn = DB::table('withdraws')
                        ->where('user_id', $commission->user_id)
                        ->where('status', 'paid')
                        ->sharedLock()
                        ->sum('amount');

                    // Use INSERT ... ON DUPLICATE KEY UPDATE for atomic operation
                    DB::table('accounts')->insertOrUpdate(
                        [
                            'user_id' => $stats->user_id,
                            'balance' => $stats->total_commissions - ($withdrawn ?? 0),
                        ],
                        ['balance']
                    );

                    // Update leaderboard using insertOrUpdate for atomicity
                    DB::table('leaderboards')->insertOrUpdate(
                        [
                            'user_id' => $stats->user_id,
                            'total_commissions' => $stats->total_commissions,
                            'total_nodes' => $stats->total_nodes,
                        ],
                        ['total_commissions', 'total_nodes']
                    );

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

    public function generateReferralCode(User $user): string
    {
        DB::beginTransaction();
        try {
            $referralCode = ReferralCode::create([
                'code' => Str::random(8),
                'type' => 'user',
                'user_id' => $user->id,
            ])->code;
            DB::commit();

            return $referralCode;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
