<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'name' => $this->user->name,
                'phone' => $this->user->phone,
            ],
            'amount' => $this->amount . ' BDT',
            'payment_method' => $this->payment_method,
            'mobile_number' => $this->mobile_number,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
