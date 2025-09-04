<?php
namespace App\Services\Admin;

use App\Models\AdminRankSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

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
            ->map(fn($rank) => (object) [
                'name'      => $rank->name,
                'threshold' => $rank->threshold,
                'coins'     => (float) $rank->coins,
            ])
            ->toArray();
    }

    // Update or create ranks dynamically
    public function updateRanks(array $ranks): void
    {
        foreach ($ranks as $rank) {
            AdminRankSetting::updateOrCreate(
                [
                    'name' => $rank['name']
                ],
                [
                    'name'      => $rank['name'],
                    'threshold' => $rank['threshold'],
                    'coins'     => $rank['coins'],
                ]
            );
        }

        Cache::forget($this->cacheKey);
    }

    public function delete(int $id): bool
    {
        $rank = AdminRankSetting::find($id);

        if (!$rank) {
            return false;
        }

        $rank->delete();

        // Clear the cached ranks so the next fetch is fresh
        Cache::forget($this->cacheKey);

        return true;
    }

}
