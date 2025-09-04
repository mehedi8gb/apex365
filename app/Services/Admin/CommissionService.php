<?php
// app/Services/CommissionService.php
namespace App\Services\Admin;

use App\Models\CommissionSetting;
use App\Models\CommissionSettingHistory;
use Auth;
use Illuminate\Support\Facades\Cache;

class CommissionService
{
    protected string $cacheKey = 'commission_settings';

    public function getAll(): array
    {
        return Cache::remember($this->cacheKey, 3600 * 30, function () {
            return CommissionSetting::all()
                ->mapWithKeys(fn($item) => [$item->type => $item->levels])
                ->toArray();
        });
    }

    public function update(string $type, array $levels): CommissionSetting
    {
        $userId = Auth::id();
        $setting = CommissionSetting::where('type', $type)->first();

        $oldLevels = $setting ? $setting->levels : [];

        $setting = CommissionSetting::updateOrCreate(
            ['type' => $type],
            ['levels' => $levels]
        );

        // Save history
        CommissionSettingHistory::create([
            'commission_setting_id' => $setting->id,
            'admin_id' => $userId,
            'old_levels' => $oldLevels,
            'new_levels' => $levels,
        ]);

        Cache::forget('commission_settings'); // refresh cache

        return $setting;
    }

    public function delete(int $id): bool
    {
        CommissionSetting::deleteDeepJsonField('levels', (array)$id);

        Cache::forget('commission_settings');
        return true;
    }
}
