<?php

namespace App\Http\Resources;

use App\Enums\WithdrawStatus;
use App\Models\Commission;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected mixed $commissions;

    public function toArray($request): array
    {
        // Run paginated commissions query here, per user
        $this->commissions = Commission::with('fromUser:id,name')
            ->where('user_id', $this->resource->id)
            ->latest()
            ->paginate(15);

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
            'commissions' => CommissionResource::collection($this->commissions),
            'pagination' => $this->when($this->commissions, function () {
                return [
                    'total' => $this->commissions->total(),
                    'per_page' => $this->commissions->perPage(),
                    'current_page' => $this->commissions->currentPage(),
                    'last_page' => $this->commissions->lastPage(),
                ];
            }),
        ];
    }
}
