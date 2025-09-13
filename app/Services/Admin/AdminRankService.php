<?php

namespace App\Services\Admin;

use App\Models\AdminRankSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AdminRankService
{
    protected string $cacheKey = 'admin_rank_settings';

    // Fetch all ranks from cache or DB
    public function all(): Collection
    {
        return Cache::remember($this->cacheKey, 3600 * 30, function () {
            return AdminRankSetting::orderByDesc('threshold')->get();
        });
    }

    // Fetch ranks formatted as objects for ProfileRankService
    public function allAsObjects(): array
    {
        return $this->all()
            ->map(fn ($rank) => (object) [
                'name' => $rank->name,
                'threshold' => $rank->threshold,
                'coins' => (float) $rank->coins,
            ])
            ->toArray();
    }

    // Update or create ranks dynamically
    public function updateRanks(array $ranks): array
    {
        $results = [];

        foreach ($ranks as $rank) {
            if (! empty($rank['id'])) {
                // Update existing by ID
                $updated = AdminRankSetting::where('id', $rank['id'])->update($rank);
                $results[] = AdminRankSetting::find($rank['id']); // return updated model
            } elseif (! empty($rank['name'])) {
                // Update existing by unique name
                $updated = AdminRankSetting::where('name', $rank['name'])->update($rank);

                if (! $updated) {
                    // Create new if name not exists
                    $existing = AdminRankSetting::create([
                        'name' => $rank['name'],
                        'threshold' => $rank['threshold'] ?? 1,
                        'coins' => $rank['coins'] ?? 0.0,
                    ]);
                    $results[] = $existing;
                } else {
                    $results[] = AdminRankSetting::where('name', $rank['name'])->first();
                }
            }
        }

        // Clear cache after updates
        Cache::forget($this->cacheKey);

        return $results; // return all updated/created ranks
    }

    public function delete(int $id): bool
    {
        $rank = AdminRankSetting::find($id);

        if (! $rank) {
            return false;
        }

        $rank->delete();

        // Clear the cached ranks so the next fetch is fresh
        Cache::forget($this->cacheKey);

        return true;
    }
}
