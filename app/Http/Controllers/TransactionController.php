<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Referral;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::query();
        $results = handleApiRequest($request, $query);

        return sendSuccessResponse('Transactions retrieved successfully', $results);
    }

    public function show($userId): JsonResponse
    {
        $transactions = Transaction::where('buyer_id', $userId)->get();
        return sendSuccessResponse('User transactions retrieved successfully', TransactionResource::collection($transactions));
    }
}
