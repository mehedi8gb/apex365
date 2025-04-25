<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
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
        $transaction = Transaction::findOrFail($id);

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
}
