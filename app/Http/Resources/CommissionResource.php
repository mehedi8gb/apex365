<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return [
//            'user_id' => $this->id,
//            'username' => $this->username,
//            'email' => $this->email,
//            'total_commission_earned' => $this->transactions->sum(function ($transaction) {
//                return $transaction->level_1_commission +
//                    $transaction->level_2_commission +
//                    $transaction->level_3_commission;
//            }),
//            'transactions_count' => $this->transactions->count(),
//        ];

        return [
            'user_id' => $this->user_id,
            'from_user_id' => $this->from_user_id,
            'amount' => $this->amount,
            'level' => $this->level,
        ];
    }
}
