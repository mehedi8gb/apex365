<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nid' => $this->nid,
            'address' => $this->address,
            'balance' => $this->account?->balance,
            'referral_code' => $this->theReferralCode?->code,
            'created_at' => $this->created_at->format('Y-m-d H:i A'),
        ];
    }
}
