<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'referrer_id' => $this->referrer_id,
            'level' => $this->level,
            'referrer' => $this->referrer->name,  // assuming relationship
        ];
    }
}
