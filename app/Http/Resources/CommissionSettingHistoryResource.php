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
            'id' => $this->resource->id,
            'changed_by' => [
                'name' => $this->resource->admin->name,
                'phone' => $this->resource->admin->phone,
            ],
            'new_levels' => $this->resource->new_levels,
            'old_levels' => $this->resource->old_levels,
            'commission_setting' => $this->resource->commissionSetting,
        ];
    }
}
