<?php

namespace App\Http\Controllers;

use App\Helpers\ReferralHelper;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserTransactionsIdResource;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * List all transactions
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::query();
        $results = handleApiRequest($request, $query);

        return sendSuccessResponse('Transactions retrieved successfully', $results);
    }

    /**
     * Show a specific transaction
     */
    public function show($id): JsonResponse
    {
        $transaction = Transaction::find($id);

        return sendSuccessResponse('Transaction retrieved successfully', new TransactionResource($transaction));
    }

    /**
     * Create a new transaction
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'transactions' => 'required|array',
            'transactions.*.transactionId' => 'required|string|unique:transactions,transactionId',
            'transactions.*.date' => 'required|date',
        ]);

        $transactions = collect($request->transactions)->map(function ($trx) {
            return [
                'transactionId' => $trx['transactionId'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        Transaction::insert($transactions); // Bulk insert for performance

        return sendSuccessResponse('Transactions generated successfully');
    }

    /**
     * Create a new transaction
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'required|array',
        ]);

        $transaction = Transaction::find($id);
        $transaction->update($validated);

        return sendSuccessResponse('Transaction created successfully', new TransactionResource($transaction));
    }

    /**
     * Delete a transaction
     */
    public function destroy($id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return sendSuccessResponse('Transaction deleted successfully');
    }

    /**
     * delete multiple transaction
     */
    public function deleteMultiple(Request $request): JsonResponse
    {
        request()->validate([
            'transactionIds' => 'required|array|min:1',
            'transactionIds.*' => 'required|string|exists:transactions,transactionId',
        ]);

        Transaction::whereIn('transactionId', request()->transactionIds)->delete();

        return sendSuccessResponse('Transactions deleted successfully');
    }

    /**
     * Get all transactions for a specific user
     */
    public function userTransactions($userId): JsonResponse
    {
        $transactions = Transaction::with('user')->where('userId', $userId)->get();

        return sendSuccessResponse('Transactions retrieved successfully', UserTransactionsIdResource::collection($transactions));
    }

    /**
     * Get all transactions for all users
     */
    public function usersTransactions(): JsonResponse
    {
        $transactions = Transaction::with('user')->whereNotNull('userId')->get();

        return sendSuccessResponse('Transactions retrieved successfully', UserTransactionsIdResource::collection($transactions));
    }

    /**
     * Apply commissions to single transaction's user
     *
     * @throws Exception
     */
    public function ApplyCommissions(Request $request): JsonResponse
    {
        $request->validate([
            'transactionId' => 'required|string|exists:transactions,transactionId',
            'userId' => 'required|integer|exists:users,id',
        ]);

        $transaction = Transaction::where('transactionId', $request->transactionId)->first();

        if (! isset($transaction->userId)) {

            $referralHelper = new ReferralHelper;

            // Use the same method calls, just on the instance
            $referralHelper->updateReferralChain($request->userId);
            $referralHelper->distributeReferralPoints();
            $referralHelper->updateReferralLeaderboard();

            $transaction->update(['userId' => $request->userId]);

            return sendSuccessResponse('Commissions applied successfully');
        }

        return sendErrorResponse('Commissions already applied', 422);
    }
}
