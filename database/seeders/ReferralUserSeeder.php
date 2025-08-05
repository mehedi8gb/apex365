<?php

namespace Database\Seeders;

use App\Helpers\ReferralHelper;
use App\Models\ReferralCode;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReferralUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws \Throwable
     */
    public function run(): void
    {
        // Get all user IDs
        $userIds = User::where('id', '!=', 1)->pluck('id')->toArray();

        // Shuffle to randomize the order
        shuffle($userIds);

        // Number of referral users you want to create
        $count = 20;

        // Prevent overflow: do not exceed available user count
        $totalToCreate = min($count, count($userIds));

        for ($i = 0; $i < $totalToCreate; $i++) {
            $userId = array_pop($userIds); // Get and remove one userId

            // find unique referral code for the user
            $referrerAndCode = ReferralCode::first();
            $user = User::find($userId);

            // In your registration method:
            $referralHelper = new ReferralHelper;

            // Use the same method calls, just on the instance
            $referralHelper->createReferralChain($user, $referrerAndCode);
            $referralHelper->distributeReferralPoints();
            $referralHelper->updateReferralLeaderboard();
            $referralHelper->generateReferralCode($user);
        }
    }
}
