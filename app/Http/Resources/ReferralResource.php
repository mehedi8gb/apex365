<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
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
            'user_id' => $this->user_id,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'role' => $this->user->role,
            'account_type' => $this->user->account_type,
            'referral_code' => optional($this->user->referralCode)->code,
            'referred_by' => $this->referrer?->username,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
