<?php

namespace App\Http\Resources;

use App\Models\Commission;
use App\Models\Transaction;
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
        $commissions = Commission::with('fromUser:id,name')->where('user_id', $this->id)
            ->latest()
            ->paginate(5);

        // Get transactionIds as an array
        $transactionIds = collect($this->transactionIds);

        // Paginate transactionIds manually
        $perPage = $request->get('transactions-limit', 10); // Default to 10 per page
        $page = $request->get('transactions-page', 1);
        $total = $transactionIds->count();

        $paginatedTransactions = $transactionIds->forPage($page, $perPage)->values();

        return [
            'user' => new UserResource($this, $commissions),
            'transactionIds' => [
                'meta' => [
                    'transactions-page' => (int) $page,
                    'transactions-limit' => (int) $perPage,
                    'total' => $total,
                    'totalPage' => ceil($total / $perPage),
                ],
                'result' => $paginatedTransactions,
            ],
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

}
