<?php

namespace App\Http\Resources;

use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTransactionsIdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $commissions = Commission::with('fromUser:id,name')->where('user_id', $this->user->id)
            ->latest()
            ->paginate(5);

        return [
            'id' => $this->id,
            'user' => new UserResource($this->user, $commissions),
            'transactionId' => $this->transactionId,
            'date' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
