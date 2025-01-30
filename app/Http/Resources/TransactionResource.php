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
//        return [
//            'id' => $this->id,
//            'buyer' => [
//                'id' => $this->buyer->id,
//                'username' => $this->buyer->username,
//                'email' => $this->buyer->email,
//            ],
//            'referrer_level_1' => optional($this->referrerLevel1)->username,
//            'referrer_level_2' => optional($this->referrerLevel2)->username,
//            'referrer_level_3' => optional($this->referrerLevel3)->username,
//            'purchase_amount' => $this->purchase_amount,
//            'level_1_commission' => $this->level_1_commission,
//            'level_2_commission' => $this->level_2_commission,
//            'level_3_commission' => $this->level_3_commission,
//            'created_at' => $this->created_at->toDateTimeString(),
//        ];

        return [
            'id' => $this->id,
            'purchase_amount' => $this->purchase_amount,
            'referral_level_1' => $this->referral_level_1,
            'referral_level_2' => $this->referral_level_2,
            'referral_level_3' => $this->referral_level_3,
            'total_commission' => $this->total_commission,
            'created_at' => $this->created_at->toIso8601String(),
            'status' => $this->status,
        ];
    }
}
