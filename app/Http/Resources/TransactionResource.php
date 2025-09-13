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
        if ($this->resource->userId) {
            $user = [
                'id' => $this->resource->user?->id,
                'name' => $this->resource->user?->name,
                'phone' => $this->resource->user?->phone,
                'referral_code' => $this->resource->user?->theReferralCode?->code,
            ];
        }

        return [
            'id' => $this->resource->id,
            'is_assigned' => (bool)$this->resource->userId,
            'user' => $user ?? "not assigned",
            'transactionId' => $this->resource->transactionId,
            'status' => $this->resource->status->value,
            'date' => getFormatedDate($this->resource->created_at),
        ];
    }
}
