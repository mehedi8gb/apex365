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
//        $leaderboard = Leaderboard::where('user_id', $this->id)->first();
//        $commissions = Commission::where('user_id', $this->id)->get();
        $referralCode = ReferralCode::where('user_id', $this->id)->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nid' => $this->nid,
            'address' => $this->address,
            'referral_code' => $referralCode->code,
//            'referral_chain' => ReferralUserResource::collection($this->referralUsers),  // assuming relationship
            'leaderboard' => new LeaderboardResource($this->leaderboard),  // assuming relationship
            'commissions' => CommissionResource::collection($this->commissions),
        ];
    }
}

