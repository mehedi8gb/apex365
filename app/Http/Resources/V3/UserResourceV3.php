<?php

namespace App\Http\Resources\V3;

use App\Enums\WithdrawStatus;
use App\Http\Resources\CommissionResource;
use App\Http\Resources\LeaderboardResource;
use App\Models\Commission;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResourceV3 extends JsonResource
{
    protected mixed $purchaseCommissions;
    protected mixed $signupCommissions;

    public function toArray($request): array
    {
        $signupCommissionsPage = (int) $request->query('signup_commissions_page', 1);
        $purchaseCommissionsPage = (int) $request->query('purchase_commissions_page', 1);

        // Run paginated commissions query here, per user
        $this->purchaseCommissions = Commission::with('fromUser:id,name')
            ->where('user_id', $this->resource->id)
            ->ofType('purchase')
            ->latest()
            ->paginate(15, ['*'], 'commissions_page', $purchaseCommissionsPage);

        $this->signupCommissions = Commission::with(['fromUser:id,name', 'commissionSetting:id,type'])
            ->where('user_id', $this->resource->id)
            ->where(function ($query) {
                $query->whereHas('commissionSetting', function ($q) {
                    $q->where('type', 'signup');
                })->orWhereNull('commission_type_id'); // include legacy rows
            })
            ->latest()
            ->paginate(15,  ['*'], 'commissions_page', $signupCommissionsPage);

        $referrer = $this->resource->referredBy?->referrer;

        return [
            'id' => $this->resource->id,
            'role' => $this->resource->getRoleNames()->first(),
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'balance' => $this->whenLoaded('account', fn () => $this->resource->account->balance),
            'total_withdrawn_approved' => $this->whenLoaded('account', fn () => $this->resource->account->total_withdrawn),
            'total_pending_withdrawal' => $this->resource->withdraws->where('status', WithdrawStatus::Pending->value)->sum('amount'),
            'total_suspended_withdrawal' => $this->resource->withdraws->where('status', WithdrawStatus::Suspended->value)->sum('amount'),
            'nid' => $this->resource->nid,
            'address' => $this->resource->address,
            'date_of_birth' => $this->resource->date_of_birth?->format('Y-m-d'),
            'profile_picture' => config('apex365.microservice.file_api_server').'/data/profile/'.$this->resource->id,
            'referral_code' => $this->resource->theReferralCode?->code,
            'account_created_at' => getFormatedDate($this->resource->created_at),
            'referred_by' => $this->resource->id === 1 // admin filterd
                ? [
                    'name' => 'Admin',  // fixed text for admin
                    'phone' => $this->resource->phone,
                ]
                : [
                    'name' => $referrer?->name ?? null,
                    'phone' => $referrer?->phone ?? null,
                ],
            'leaderboard' => new LeaderboardResource($this->whenLoaded('leaderboard')),
            'purchase_commissions' => CommissionResource::collection($this->purchaseCommissions),
            'purchase_commissions_count' => $this->purchaseCommissions->count(),
            'purchase_commissions_pagination' => $this->when($this->purchaseCommissions, function () {
                return [
                    'total' => $this->purchaseCommissions->total(),
                    'per_page' => $this->purchaseCommissions->perPage(),
                    'current_page' => $this->purchaseCommissions->currentPage(),
                    'last_page' => $this->purchaseCommissions->lastPage(),
                ];
            }),
            'signup_commissions' => CommissionResource::collection($this->signupCommissions),
            'signup_commissions_count' => $this->signupCommissions->count(),
            'signup_commissions_pagination' => $this->when($this->signupCommissions, function () {
                return [
                    'total' => $this->signupCommissions->total(),
                    'per_page' => $this->signupCommissions->perPage(),
                    'current_page' => $this->signupCommissions->currentPage(),
                    'last_page' => $this->signupCommissions->lastPage(),
                ];
            }),
        ];
    }
}
