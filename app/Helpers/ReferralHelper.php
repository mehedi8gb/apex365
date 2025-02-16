<?php

namespace App\Helpers;

use App\Models\{Account, Commission, Leaderboard, ReferralCode, ReferralUser, User};
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralHelper
{
    private $referralUser;
    private $commissions = [];
    private $currentUser;

    public function createReferralChain(User $user, $referrerAndCode): void
    {
        $this->currentUser = $user;
        $this->referralUser = ReferralUser::create([
            'user_id' => $user->id,
            'referrer_id' => $referrerAndCode->user->id ?? 1,
            'referral_code_id' => $referrerAndCode->id,
        ]);
    }

    public function distributeReferralPoints(): void
    {
        if (!$this->referralUser?->referrer || !$this->referralUser?->user) {
            throw new Exception('Invalid referral user or referrer.');
        }

        DB::beginTransaction();
        try {
            $commissionAmounts = config('commissions.levels');
            $currentReferrer = $this->referralUser->referrer;

            // First level commission
            $this->createCommission(1, $this->currentUser, $currentReferrer, $commissionAmounts);

            // Process higher levels
            $level = 2;
            while ($currentReferrer && $level <= count($commissionAmounts)) {
                if (!isset($commissionAmounts[$level])) {
                    throw new Exception("Commission amount not defined for level $level.");
                }

                $this->createCommission($level, $currentReferrer, $this->currentUser, $commissionAmounts);

                $nextReferrer = ReferralUser::where('user_id', $currentReferrer->id)->first();
                $currentReferrer = $nextReferrer?->referrer;

                if ($currentReferrer?->id === $this->currentUser->id) {
                    throw new Exception('Infinite loop detected in the referral chain.');
                }

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

                if (!$stats) {
                    DB::commit();
                    continue;
                }

                // Get withdrawn amount
                $withdrawn = DB::table('withdraws')
                    ->where('user_id', $commission->user_id)
                    ->where('status', 'paid')
                    ->sum('amount');

                // Lock and update account
                $account = Account::where('user_id', $commission->user_id)
                    ->lockForUpdate()
                    ->first();

                if ($account) {
                    $account->balance = $stats->total_commissions - ($withdrawn ?? 0);
                    $account->save();
                } else {
                    Account::create([
                        'user_id' => $stats->user_id,
                        'balance' => $stats->total_commissions - ($withdrawn ?? 0),
                    ]);
                }

                // Update leaderboard
                Leaderboard::updateOrCreate(
                    ['user_id' => $stats->user_id],
                    [
                        'total_commissions' => $stats->total_commissions,
                        'total_nodes' => $stats->total_nodes,
                    ]
                );

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

    public function generateReferralCode(User $user): string
    {
        return ReferralCode::create([
            'code' => Str::random(8),
            'type' => 'user',
            'user_id' => $user->id,
        ])->code;
    }
}
