<?php

namespace App\Http\Resources;

use App\Models\ReferralCode;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $commissions;

    public function __construct($resource, $commissions = null)
    {
        parent::__construct($resource);
        $this->commissions = $commissions;
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'balance' => optional($this->account)->balance,
            'nid' => $this->nid,
            'address' => $this->address,
            'referral_code' => optional($this->referralCode?->referralCode)?->code ?? ReferralCode::where('user_id', $this->id)->first()->code,
            'leaderboard' => new LeaderboardResource($this->whenLoaded('leaderboard')),
            'commissions' => CommissionResource::collection($this->commissions),
            'pagination' => [
                'total' => $this->commissions->total(),
                'per_page' => $this->commissions->perPage(),
                'current_page' => $this->commissions->currentPage(),
                'last_page' => $this->commissions->lastPage(),
                'next_page_url' => $this->commissions->nextPageUrl(),
                'prev_page_url' => $this->commissions->previousPageUrl(),
            ],
        ];
    }
}
