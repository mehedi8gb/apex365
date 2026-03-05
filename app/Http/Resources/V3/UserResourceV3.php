<?php

namespace App\Http\Resources\V3;

use App\Enums\WithdrawStatus;
use App\Helpers\ResourceHelpers;
use App\Http\Resources\LeaderboardResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResourceV3 extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'user' => [
                'id' => $this->resource->id,
                'role' => $this->resource->getRoleNames()->first(),
                'name' => $this->resource->name,
                'email' => $this->resource->email,
                'phone' => $this->resource->phone,
                'balance' => $this->whenLoaded('account', fn() => $this->resource->account->balance),
                'total_withdrawn_approved' => $this->whenLoaded('account', fn() => $this->resource->account->total_withdrawn),
                'total_pending_withdrawal' => $this->resource->withdraws->where('status', WithdrawStatus::Pending->value)->sum('amount'),
                'total_suspended_withdrawal' => $this->resource->withdraws->where('status', WithdrawStatus::Suspended->value)->sum('amount'),
                'nid' => $this->resource->nid,
                'address' => $this->resource->address,
                'date_of_birth' => $this->resource->date_of_birth?->format('Y-m-d'),
                'profile_picture' => config('apex365.microservice.file_api_server') . '/data/profile/' . $this->resource->id,
                'referral_code' => $this->resource->theReferralCode?->code,
                'referred_by_chain' => ResourceHelpers::buildReferralChain($this->resource),
                'leaderboard' => new LeaderboardResource($this->whenLoaded('leaderboard')),
                'account_created_at' => getFormatedDate($this->resource->created_at),
            ]
        ];
    }
}
