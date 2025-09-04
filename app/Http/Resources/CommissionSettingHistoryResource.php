<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionSettingHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'changed_by' => [
                'name' => $this->admin->name,
                'phone' => $this->admin->phone,
            ],
            'new_levels' => $this->new_levels,
            'old_levels' => $this->old_levels,
            'commission_setting' => $this->commissionSetting,
        ];
    }
}
