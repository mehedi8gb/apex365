<?php

namespace App\Http\Resources;

use App\Models\Commission;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected mixed $commissions;

    public function toArray($request): array
    {               // Run paginated commissions query here, per user
        $this->commissions = Commission::with('fromUser:id,name')
            ->where('user_id', $this->id)
            ->latest()
            ->paginate(15);

        $referrer = $this->referredBy?->referrer;

        return [
            'id' => $this->id,
            'role' => $this->getRoleNames()->first(),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'balance' => $this->whenLoaded('account', fn () => $this->account->balance),
            'total_withdrawn_approved' => $this->whenLoaded('account', fn () => $this->account->total_withdrawn),
            'total_pending_withdrawal' => $this->withdraws->where('status', 'due')->sum('amount'),
            'nid' => $this->nid,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'profile_picture' => config('apex365.microservice.file_api_server').'/data/profile/'.$this->id,
            'referral_code' => $this->theReferralCode?->code,
            'account_created_at' => getFormatedDate($this->created_at),
            'referred_by' => $this->id === 1 // admin filterd
                ? [
                    'name' => 'Admin',  // fixed text for admin
                    'phone' => $this->phone
                ]
                : [
                    'name' => $referrer?->name ?? null,
                    'phone' => $referrer?->phone ?? null
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
