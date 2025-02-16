<?php

namespace App\Http\Resources;

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
            'referral_code' => optional($this->referralCode?->referralCode)?->code ?? $this->referralCode?->code,
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
