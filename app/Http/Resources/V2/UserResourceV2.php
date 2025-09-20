<?php

namespace App\Http\Resources\V2;

use App\Enums\WithdrawStatus;
use App\Helpers\ResourceHelpers;
use App\Http\Resources\CommissionResource;
use App\Http\Resources\LeaderboardResource;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * mark as abandoned after v3 is stable
 * Class UserResourceV2
 * @package App\Http\Resources\V2
 */
class UserResourceV2 extends JsonResource
{
    protected mixed $purchaseCommissions;
    protected mixed $signupCommissions;

    public function toArray($request): array
    {
        // Run paginated commissions query here, per user
        $this->purchaseCommissions = Commission::with('fromUser:id,name')
            ->where('user_id', $this->resource->id)
            ->ofType('purchase')
            ->latest()
            ->get();

        $this->signupCommissions = Commission::with(['fromUser:id,name', 'commissionSetting:id,type'])
            ->where('user_id', $this->resource->id)
            ->where(function ($query) {
                $query->whereHas('commissionSetting', function ($q) {
                    $q->where('type', 'signup');
                })->orWhereNull('commission_type_id'); // include legacy rows
            })
            ->latest()
            ->get();

        return [
            'id' => $this->resource->id,
            'role' => $this->resource->getRoleNames()->first(),
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'status' => $this->resource->status,
            'phone' => $this->resource->phone,
            'balance' => $this->resource?->account?->balance ?? "0.00",
            'total_withdrawn_approved' => $this->resource?->account?->total_withdrawn ?? 0.00,
            'total_pending_withdrawal' => $this->resource->withdraws->where('status', WithdrawStatus::Pending->value)->sum('amount'),
            'total_suspended_withdrawal' => $this->resource->withdraws->where('status', WithdrawStatus::Suspended->value)->sum('amount'),
            'nid' => $this->resource->nid,
            'address' => $this->resource->address,
            'date_of_birth' => $this->resource->date_of_birth?->format('Y-m-d'),
            'profile_picture' => config('apex365.microservice.file_api_server').'/data/profile/'.$this->resource->id,
            'referral_code' => $this->resource->theReferralCode?->code,
            'account_created_at' => getFormatedDate($this->resource->created_at),
            'referred_by_chain' => ResourceHelpers::buildReferralChain($this->resource),
            'leaderboard' => new LeaderboardResource($this->whenLoaded('leaderboard')),
            'purchase_commissions' => CommissionResource::collection($this->purchaseCommissions),
            'purchase_commissions_count' => $this->purchaseCommissions->count(),
            'signup_commissions' => CommissionResource::collection($this->signupCommissions),
            'signup_commissions_count' => $this->signupCommissions->count(),
        ];
    }
}
