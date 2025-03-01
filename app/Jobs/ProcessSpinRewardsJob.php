<?php

namespace App\Jobs;

use App\Models\Spinner;
use App\Models\SpinnerItems;
use App\Models\SpinnerLeaderboard;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSpinRewardsJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct()
    {
        // Constructor logic (if any)
    }

    public function handle(): void
    {
        $todaySpinTime = now()->setSeconds(0);
        $currentTime = now()->setSeconds(0);

        // Fetch the latest spinner data
        $latestSpin = Spinner::whereRaw('TIME(spin_time) >= ?', [$currentTime])
            ->orderByRaw('TIME(spin_time) ASC') // Order by time (HH:MM:SS)
            ->first();

        if ($latestSpin) {
            $todaySpinTime->setTime(
                $latestSpin->spin_time->hour,
                $latestSpin->spin_time->minute,
            );
        }

        if ($currentTime->format('H:i') == $todaySpinTime->format('H:i') && $latestSpin) {
            // Select a random user
            $user = User::inRandomOrder()->first();

            // Assign a reward based on the rotation point
            $reward = $this->getRewardForRotationPoint($latestSpin->rotation_point);

            $user->account->update([
                'balance' => $user->account->balance + $reward,
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

            Log::warning('Reward assigned to user: '.$user->id);
        } else {
            Log::warning('No spinner data found.');
        }

        Log::warning('ProcessSpinRewardsJob executed successfully currentTime: '.$currentTime->format('H:i').' todaySpinTime: '.$todaySpinTime->format('H:i'));
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
        return SpinnerLeaderboard::where('user_id', $userId)->count() + 1;
    }

    protected function calculatePoints($rotationPoint): float|int
    {
        // Implement logic to calculate points based on rotation point
        return $rotationPoint; // Example logic
    }
}
