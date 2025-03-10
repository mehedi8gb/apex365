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
        return [
            'id' => $this->id,
            'user' => [
                'name' => $this->user?->name,
                'phone' => $this->user?->phone,
            ],
            'transactionId' => $this->transactionId,
            'date' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
