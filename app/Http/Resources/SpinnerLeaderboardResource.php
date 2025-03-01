<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpinnerLeaderboardResource extends JsonResource
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
            'rank' => $this->rank,
            'user' => [
                'name' => $this->user->name,
                'avatar' => $this->user->avatar ?? null, // Assuming avatar is a field in the users table
            ],
            'points' => $this->points,
            'reward' => $this->reward,
            'spinner' => $this->spinner,
            'timestamp' => $this->timestamp->toIso8601String(),
        ];
    }
}
