<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReferralResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\CommissionResource;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{


    public function index(Request $request): JsonResponse
    {
        $query = Referral::query();
        $results = handleApiRequest($request, $query);

        return sendSuccessResponse('Referral records retrieved successfully', $results);
    }

    public function show($userId): JsonResponse
    {
        $user = User::with('referrals')->findOrFail($userId);
        return sendSuccessResponse('User referrals retrieved successfully', ReferralResource::collection($user->referrals));
    }

    /**
     * Get referral nodes (hierarchy) for a user
     */
    public function getReferralNodes($userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $nodes = $this->buildReferralTree($user);

        return sendSuccessResponse('Referral hierarchy retrieved successfully', $nodes);
    }

    /**
     * Get all sales (transactions) for a user
     */
    public function getUserSales($userId): JsonResponse
    {
        $transactions = Transaction::where('user_id', $userId)->get();

        if ($transactions->isEmpty()) {
            return sendErrorResponse('No sales found', 404);
        }

        return sendSuccessResponse('User sales retrieved successfully', TransactionResource::collection($transactions));
    }

    /**
     * Get commissions earned by a user
     */
    public function getUserCommissions($userId): JsonResponse
    {
        $transactions = Transaction::where('level1_referrer', $userId)
            ->orWhere('level2_referrer', $userId)
            ->orWhere('level3_referrer', $userId)
            ->get();

        if ($transactions->isEmpty()) {
            return sendErrorResponse('No commissions found', 404);
        }

        return sendSuccessResponse('User commissions retrieved successfully', CommissionResource::collection($transactions));
    }

    /**
     * Recursively build a referral tree
     */
    private function buildReferralTree(User $user)
    {
        $referrals = Referral::get();

        return $referrals->map(function ($referral) {
            return [
                'user' => new ReferralResource($referral->referrer),
                'children' => $this->buildReferralTree($referral->referrer)
            ];
        });
    }
}

