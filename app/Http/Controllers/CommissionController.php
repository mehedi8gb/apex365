<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommissionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::query();
        $results = handleApiRequest($request, $query);

        return sendSuccessResponse('Commission retrieved successfully', CommissionResource::collection($results));
    }

    public function show($userId): JsonResponse
    {
        $commissions = Transaction::where(function ($query) use ($userId) {
            $query->where('referrer_level_1', $userId)
                ->orWhere('referrer_level_2', $userId)
                ->orWhere('referrer_level_3', $userId);
        })->get();

        return sendSuccessResponse('User commissions retrieved successfully', CommissionResource::collection($commissions));
    }
}
