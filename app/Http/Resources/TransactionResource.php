<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->userId) {
            $user = [
                'name' => $this->user?->name,
                'phone' => $this->user?->phone,
                'referral_code' => $this->user?->theReferralCode?->code,
            ];
        }

        return [
            'id' => $this->id,
            'user' => $user ?? "not assigned",
            'transactionId' => $this->transactionId,
            'date' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
