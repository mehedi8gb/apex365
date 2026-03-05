<?php

// app/Services/CommissionService.php

namespace App\Services\Admin;

use App\Models\CommissionSetting;
use App\Models\CommissionSettingHistory;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class CommissionService
{
    protected string $cacheKey = 'commission_settings';

    public function getAll(): array
    {
        return Cache::remember($this->cacheKey, 3600 * 24 * 30, function () {
            return CommissionSetting::all()
                ->mapWithKeys(fn ($item) => [$item->type => $item->levels])
                ->toArray();
        });
    }

    /**
     * @throws Throwable
     */
    public function update(string $type, array $levels, ?int $adminId = null): CommissionSetting
    {
        $adminId = $adminId ?? Auth::id();

        return DB::transaction(function () use ($type, $levels, $adminId) {
            $setting = CommissionSetting::where('type', $type)->first();

            if (!$setting) {
                throw new ModelNotFoundException("CommissionSetting not found for type: {$type}");
            }

            $oldLevels = $setting->levels;

            $setting->update(['levels' => $levels]);

            CommissionSettingHistory::create([
                'commission_setting_id' => $setting->id,
                'admin_id' => $adminId,
                'old_levels' => $oldLevels,
                'new_levels' => $levels,
            ]);

            Cache::forget('commission_settings');

            return $setting->fresh();
        });
    }

    public function getAllStructured(): array
    {
        // Get raw commission values from service
        $rawResults = $this->getAll();

        $result = [];
        $id = 1;

        foreach ($rawResults as $type => $levels) {
            $levelData = [];

            foreach ($levels as $level => $value) {
                $levelData[] = [
                    'level' => (int) $level,
                    'value' => $value,
                ];
            }

            $result[] = [
                'id' => $id,
                'type' => $type,
                'levels' => $levelData
            ];

            $id++;
        }

        return $result;
    }
}
