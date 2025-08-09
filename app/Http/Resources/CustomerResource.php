<?php

namespace App\Http\Resources;

use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    protected mixed $commissions;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $commissionsPage = (int) $request->query('commissions_page', 1);

        // Run paginated commissions query here, per user
        $this->commissions = Commission::with('fromUser:id,name')
            ->where('user_id', $this->id)
            ->latest()
            ->paginate(10, ['*'], 'commissions_page', $commissionsPage);

        return [
            'id' => $this->id,
            'role' => $this->getRoleNames()->first(),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'balance' => $this->whenLoaded('account', fn () => $this->account->balance),
            'nid' => $this->nid,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'referral_code' => $this->theReferralCode?->code,
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
