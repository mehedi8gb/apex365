<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpinnerResource extends JsonResource
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
            'rotation_point' => $this->rotation_point,
            'spin_time' => $this->spin_time->format('Y-m-d H:i:s'),
            'spin_time_in_ms' => $this->spin_time->timestamp * 1000,
            'created_at' => $this->created_at,
        ];
    }
}
