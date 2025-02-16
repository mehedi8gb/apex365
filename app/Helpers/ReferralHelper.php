<?php

namespace App\Helpers;

use App\Models\{Account, Commission, Leaderboard, ReferralCode, ReferralUser, User};
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ReferralHelper
{
    private $referralUser;
    private $commissions = [];
    private $currentUser;
    private $maxRetries = 3;
    private $retryDelay = 100; // milliseconds

    public function createReferralChain(User $user, $referrerAndCode): void
    {
        $this->currentUser = $user;

        $attempt = 1;
        while ($attempt <= $this->maxRetries) {
            try {
                DB::beginTransaction();

                $this->referralUser = ReferralUser::create([
                    'user_id' => $user->id,
                    'referrer_id' => $referrerAndCode->user->id ?? 1,
                    'referral_code_id' => $referrerAndCode->id,
                ]);

                DB::commit();
                return;
            } catch (Throwable $e) {
                DB::rollBack();

                if ($attempt === $this->maxRetries) {
                    throw $e;
                }

                usleep($this->retryDelay * 1000); // Convert to microseconds
                $attempt++;
            }
        }
    }

    public function distributeReferralPoints(): void
    {
        if (!$this->referralUser?->referrer || !$this->referralUser?->user) {
            throw new Exception('Invalid referral user or referrer.');
        }

        $attempt = 1;
        while ($attempt <= $this->maxRetries) {
            try {
                DB::beginTransaction();

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

                    $nextReferrer = ReferralUser::where('user_id', $currentReferrer->id)
                        ->lockForUpdate()
                        ->first();
                    $currentReferrer = $nextReferrer?->referrer;

                    if ($currentReferrer?->id === $this->currentUser->id) {
                        throw new Exception('Infinite loop detected in the referral chain.');
                    }

                    $level++;
                }

                DB::commit();
                return;
            } catch (Throwable $e) {
                DB::rollBack();

                if ($attempt === $this->maxRetries) {
                    throw $e;
                }

                usleep($this->retryDelay * 1000);
                $attempt++;
            }
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

    public function updateLeaderboard(): void
    {
        foreach ($this->commissions as $commission) {
            $attempt = 1;
            while ($attempt <= $this->maxRetries) {
                try {
                    DB::beginTransaction();

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
                        continue 2; // Skip to next commission
                    }

                    $withdrawn = DB::table('withdraws')
                        ->where('user_id', $commission->user_id)
                        ->where('status', 'paid')
                        ->sum('amount');

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

                    Leaderboard::updateOrCreate(
                        ['user_id' => $stats->user_id],
                        [
                            'total_commissions' => $stats->total_commissions,
                            'total_nodes' => $stats->total_nodes,
                        ]
                    );

                    DB::commit();
                    break; // Success, move to next commission
                } catch (Throwable $e) {
                    DB::rollBack();

                    if ($attempt === $this->maxRetries) {
                        throw $e;
                    }

                    usleep($this->retryDelay * 1000);
                    $attempt++;
                }
            }
        }
    }

    public function generateReferralCode(User $user): string
    {
        $attempt = 1;
        while ($attempt <= $this->maxRetries) {
            try {
                return ReferralCode::create([
                    'code' => Str::random(8),
                    'type' => 'user',
                    'user_id' => $user->id,
                ])->code;
            } catch (Throwable $e) {
                if ($attempt === $this->maxRetries) {
                    throw $e;
                }

                usleep($this->retryDelay * 1000);
                $attempt++;
            }
        }
    }
}
