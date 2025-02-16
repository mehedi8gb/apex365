<?php

namespace App\Http\Resources;

use App\Models\Commission;
use App\Models\Leaderboard;
use App\Models\ReferralCode;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'balance' => optional($this->account)->balance,
            'nid' => $this->nid,
            'address' => $this->address,
            'referral_code' => optional($this->referralCode)->code,
            'leaderboard' => new LeaderboardResource($this->whenLoaded('leaderboard')),
        ];
    }
}



