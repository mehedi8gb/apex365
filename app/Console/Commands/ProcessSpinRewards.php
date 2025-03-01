<?php

namespace App\Console\Commands;

use App\Models\Spinner;
use App\Models\SpinnerItems;
use App\Models\SpinnerLeaderboard;
use App\Models\User;
use Illuminate\Console\Command;

class ProcessSpinRewards extends Command
{
    protected $signature = 'spin:process-rewards';

    protected $description = 'Process spin rewards and update leaderboard';

    public function handle(): void
    {
        $todaySpinTime = now();
        $currentTime = now();

        // Fetch the latest spinner data
        $latestSpin = Spinner::whereRaw("TIME(spin_time) >= ?", [$currentTime])
            ->orderByRaw("TIME(spin_time) ASC") // Order by time (HH:MM:SS)
            ->first();

        $todaySpinTime->setTime(
            $latestSpin->spin_time->hour,
            $latestSpin->spin_time->minute,
            $latestSpin->spin_time->second
        );

        if ($currentTime == $todaySpinTime) {
            // Select a random user
            $user = User::inRandomOrder()->first();

            // Assign a reward based on the rotation point
            $reward = $this->getRewardForRotationPoint($latestSpin->rotation_point);

            $user->account->update([
                'balance' => $user->account->balance + $reward
            ]);

            // Update the leaderboard
            SpinnerLeaderboard::create([
                'user_id' => $user->id,
                'spin_id' => $latestSpin->id,
                'rank' => $this->calculateRank($user->id), // Implement rank calculation logic
                'points' => $this->calculatePoints($latestSpin->rotation_point), // Implement points calculation logic
                'reward' => $reward,
                'timestamp' => now(),
            ]);


            $this->info("Reward assigned to user: {$user->id} for spin: {$latestSpin->id}");
        } else {
            $this->error('No spinner data found.');
        }
    }

    protected function getRewardForRotationPoint($rotationPoint)
    {
        // Define rewards based on rotation points
        $rewards = SpinnerItems::find(1);
        $rewards = $rewards->items;
        $value = null;
        foreach ($rewards as $reward) {
            if ($reward['rotation_point'] == $rotationPoint) {
                $value = $reward['value'];
            }
        }

        return $value;
    }

    protected function calculateRank($userId): int
    {
        // Implement logic to calculate the user's rank
        // For example, fetch the current rank based on points
        return SpinnerLeaderboard::where('user_id', $userId)->count() + 1;
    }

    protected function calculatePoints($rotationPoint): float|int
    {
        // Implement logic to calculate points based on rotation point
        // For example, assign points based on rotation point
        return $rotationPoint; // Example logic
    }
}
