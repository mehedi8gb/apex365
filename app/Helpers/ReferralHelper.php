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
    private array $commissions = [];

    public function createReferralChain(User $user, $referrerAndCode): void
    {
        $currentReferrer = $referrerAndCode->user ?? User::find(1); // If no referrer, assign Admin (ID 1)

        // Ensure each referral entry is stored uniquely for the user
        $this->referralUser = ReferralUser::create([
            'user_id' => $user->id,        // New user
            'referrer_id' => $currentReferrer->id, // Who referred this user
            'referral_code_id' => $referrerAndCode->id, // Referral code id
        ]);
    }

    /**
     * @throws Exception
     */
    public function distributeReferralPoints(): void
    {
        // Ensure the referral user and their referrer exist
        if (!$this->referralUser || !$this->referralUser->referrer || !$this->referralUser->user) {
            throw new Exception('Invalid referral user or referrer.');
        }

        // Points distribution per level
        $commissionAmounts = config('commissions.levels'); // Commission amounts for levels 1 to 4
        $maxLevel = count($commissionAmounts); // Dynamically determine the max level
        $level = 1; // Start from level 1
        $currentReferrer = $this->referralUser->referrer; // The first referrer in the chain
        $currentUser = $this->referralUser->user; // The user who triggered the referral

        $this->commissions[$level] = Commission::create([
            'user_id' => $currentUser->id, // The signed-up user getting commission
            'from_user_id' => $currentReferrer->id, // The referrer
            'level' => $level, // The level of the referral
            'amount' => $commissionAmounts[$level], // The commission amount
        ]);

        $level = 2;

        // Traverse the referral chain up to the max level
        while ($currentReferrer && $level <= $maxLevel) {
            // Ensure the commission amount exists for the current level
            if (!isset($commissionAmounts[$level])) {
                throw new Exception("Commission amount not defined for level $level.");
            }

            // Create a commission record for the current referrer
            $this->commissions[$level] = Commission::create([
                'user_id' => $currentReferrer->id, // The referrer receiving the commission
                'from_user_id' => $currentUser->id, // The user who triggered the commission
                'level' => $level, // The level of the referral
                'amount' => $commissionAmounts[$level], // The commission amount
            ]);

            // Move to the next referrer in the chain
            $nextReferrer = ReferralUser::where('user_id', $currentReferrer->id)->first();
            $currentReferrer = $nextReferrer->referrer ?? null; // The next referrer in the chain

            $level++;

            // Prevent infinite loops by breaking if the same referrer is encountered again
            if ($currentReferrer && $currentReferrer->id === $currentUser->id) {
                throw new Exception('Infinite loop detected in the referral chain.');
            }
        }
    }

    public function updateLeaderboard(): void
    {
        foreach ($this->commissions as $commission) {
            $commissionData = DB::table('commissions')
                ->selectRaw('user_id, COUNT(from_user_id) AS total_nodes, SUM(amount) AS total_commissions')
                ->where('user_id', $commission->user_id)
                ->groupBy('user_id')
                ->first();

            $withdrawnAmount = DB::table('withdraws')
                ->selectRaw('SUM(amount) AS total_withdrawn')
                ->where('user_id', $commission->user_id)
                ->where('status', 'paid')
                ->groupBy('user_id')
                ->first();

            if ($commissionData) {
                Account::updateOrCreate([
                    'user_id' => $commissionData->user_id,
                ], [
                    'balance' => $commissionData->total_commissions - ($withdrawnAmount->total_withdrawn ?? 0),
                ]);

                // Insert or update the leaderboard entry
                Leaderboard::updateOrCreate(
                    ['user_id' => $commissionData->user_id], // Find existing record by user_id
                    [
                        'total_commissions' => $commissionData->total_commissions ?? 0,
                        'total_nodes' => $commissionData->total_nodes ?? 0,
                    ]
                );
            }
        }
    }

    public function generateReferralCode(User $user): string
    {
        $referralCode = ReferralCode::create([
            'code' => Str::random(8),
            'type' => 'user',
            'user_id' => $user->id,
        ]);

        return $referralCode->code;
    }
}
