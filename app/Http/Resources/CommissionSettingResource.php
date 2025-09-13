<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->resource->id,
            'type'       => $this->resource->type,
            'levels'     => collect($this->resource->levels)->map(function ($value, $index) {
                return [
                    'level' => $index,  // add level index
                    'value' => $value,
                ];
            })->values(),
            'created_at' => getFormatedDate($this->resource->created_at),
            'updated_at' => getFormatedDate($this->resource->updated_at),
        ];
    }
}

