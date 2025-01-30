<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
//            'user_id' => $this->user_id,
            'total_commission' => $this->total_commission ?? 30,
            'total_nodes' => $this->total_nodes ?? 1,
        ];
    }
}
