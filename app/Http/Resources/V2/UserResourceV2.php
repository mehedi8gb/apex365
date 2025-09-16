<?php

namespace App\Http\Resources\V2;

use App\Enums\WithdrawStatus;
use App\Http\Resources\CommissionResource;
use App\Http\Resources\LeaderboardResource;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResourceV2 extends JsonResource
{
    protected mixed $commissions;

    public function toArray($request): array
    {
        // Run paginated commissions query here, per user
        $this->commissions = Commission::with('fromUser:id,name')
            ->where('user_id', $this->resource->id)
            ->latest()
            ->get();

        // Get max referral depth from config (signup only)
        $maxLevel = config('commissions.signup') ? count(config('commissions.signup')) : 5;

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
            'nid' => $this->resource->nid,
            'address' => $this->resource->address,
            'date_of_birth' => $this->resource->date_of_birth?->format('Y-m-d'),
            'profile_picture' => config('apex365.microservice.file_api_server').'/data/profile/'.$this->resource->id,
            'referral_code' => $this->resource->theReferralCode?->code,
            'account_created_at' => getFormatedDate($this->resource->created_at),
            'referred_by_chain' => $this->buildReferralChain($this->resource, $maxLevel),
            'leaderboard' => new LeaderboardResource($this->whenLoaded('leaderboard')),
            'commissions_count' => $this->commissions->count(),
            'commissions' => CommissionResource::collection($this->commissions),
        ];
    }

    /**
     * Recursively build referral chain up to $maxLevel
     */
    protected function buildReferralChain(User $user, int $maxLevel, int $currentLevel = 1): array
    {
        // Stop if max level reached, no referrer
        if ($currentLevel > $maxLevel || ! $user->referredBy) return [];


        $referrer = $user->referredBy->referrer;

        if (! $referrer) {
            return [];
        }

        return [
            'level' => $currentLevel,
            'name' => $referrer->name,
            'phone' => $referrer->phone,
            'referred_by' => $this->buildReferralChain($referrer, $maxLevel, $currentLevel + 1),
        ];
    }
}
