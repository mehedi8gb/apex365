<?php

namespace App\Helpers;

use App\Models\Commission;
use App\Models\Leaderboard;
use App\Models\ReferralCode;
use App\Models\ReferralUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralHelper
{
    private static $referralUser;

    public static function createReferralChain(User $user, $referrerAndCode): void
    {
        $currentReferrer = $referrerAndCode->user ?? User::find(1); // If no referrer, assign Admin (ID 1)

        // Ensure each referral entry is stored uniquely for the user
        self::$referralUser = ReferralUser::create([
            'user_id' => $user->id,        // New user
            'referrer_id' => $currentReferrer->id, // Who referred this user
            'referral_code_id' => $referrerAndCode->id, // Referral code id
        ]);
    }

    /**
     * @throws \Exception
     */
    public static function distributeReferralPoints(): void
    {
        // Ensure the referral user and their referrer exist
        if (! self::$referralUser || ! self::$referralUser->referrer || ! self::$referralUser->user) {
            throw new \Exception('Invalid referral user or referrer.');
        }

        // Points distribution per level
        $commissionAmounts = config('commissions.levels'); // Commission amounts for levels 1 to 4
        $maxLevel = count($commissionAmounts); // Dynamically determine the max level
        $level = 1; // Start from level 1
        $currentReferrer = self::$referralUser->referrer; // The first referrer in the chain
        $currentUser = self::$referralUser->user; // The user who triggered the referral

        $firstCommission = Commission::create([
            'user_id' => $currentUser->id, // The signed-up user getting commission
            'from_user_id' => $currentReferrer->id, // The referrer
            'level' => $level, // The level of the referral
            'amount' => $commissionAmounts[$level], // The commission amount
        ]);

        $level = 2;

        // Traverse the referral chain up to the max level
        while ($currentReferrer && $level <= $maxLevel) {
            // Ensure the commission amount exists for the current level
            if (! isset($commissionAmounts[$level])) {
                throw new \Exception("Commission amount not defined for level $level.");
            }
            $amount = $commissionAmounts[$level]; // Get the commission amount for the current level

            // Create a commission record for the current referrer
            $secondCommission = Commission::create([
                'user_id' => $currentReferrer->id, // The referrer receiving the commission
                'from_user_id' => $currentUser->id, // The user who triggered the commission
                'level' => $level, // The level of the referral
                'amount' => $amount, // The commission amount
            ]);

            // Move to the next referrer in the chain
            $nextReferrer = ReferralUser::where('user_id', $currentReferrer->id)->first();
            $currentReferrer = $nextReferrer->referrer ?? null; // The next referrer in the chain

            $level++;

            // Prevent infinite loops by breaking if the same referrer is encountered again
            if ($currentReferrer && $currentReferrer->id === $currentUser->id) {
                throw new \Exception('Infinite loop detected in the referral chain.');
            }
        }
    }

    public static function updateLeaderboard(User $user): void
    {
        // Fetch the referral chain (up to 3 levels)
        $referralUsers = ReferralUser::where('user_id', $user->id)
            ->orderBy('level')
            ->limit(3) // Ensure max 3 levels
            ->get();

        // Points distribution per level
        $pointsDistribution = [30, 20, 10];

        foreach ($referralUsers as $referralUser) {
            $level = $referralUser->level;
            $referrerId = $referralUser->referrer_id; // Referrer should get points
            $points = $pointsDistribution[$level - 1];

            // Update leaderboard (insert or update points)
            Leaderboard::updateOrCreate(
                ['user_id' => $referrerId],
                [
                    'total_commission' => DB::raw("IFNULL(total_commission, 0) + {$points}"),
                    'total_nodes' => DB::raw('IFNULL(total_nodes, 0) + 1'),
                ]
            );
        }
    }

    private static function generateReferralCode($user)
    {
        $referralCode = ReferralCode::create([
            'code' => Str::random(8),
            'type' => 'user',
            'user_id' => $user->id,
        ]);

        return $referralCode->code;
    }
}
