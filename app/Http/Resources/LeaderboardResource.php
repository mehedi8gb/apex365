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
            'total_commissions' => (float) $this->total_commissions ?? 0,
            'total_nodes' => $this->total_nodes ?? 1,
            'total_earned_coins' => $this->total_earned_coins ?? 0,
            'profile_rank' => $this->profile_rank ?? 'N/A',
        ];
    }
}
