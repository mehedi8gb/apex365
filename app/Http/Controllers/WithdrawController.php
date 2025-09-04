<?php

namespace App\Http\Controllers;

use App\Http\Resources\WithdrawResource;
use App\Models\Account;
use App\Models\Withdraw;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class WithdrawController extends Controller
{
    /**
     * @throws Exception
     */
    public function index(Request $request): JsonResponse
    {
        $withdraws = Withdraw::query();

        if (! isAdmin()) {
            $withdraws->where('user_id', auth()->id());
        }

        $results = handleApiRequest($request, $withdraws);

        return sendSuccessResponse(
            'Withdraw requests retrieved successfully',
            $results
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|in:bkash,nagad,rocket',
            'mobile_number' => 'required_if:payment_method,bkash,nagad,rocket',
        ]);

        $account = auth()->user()->account;

        if (!$account || $account->balance < $validatedData['amount']) {
            return sendErrorResponse('Insufficient balance', 400);
        }

        if ($validatedData['amount'] < 50) {
            return sendErrorResponse("Minimum withdrawal amount is 50", 400);
        }

        if ($account->balance < 10 || ($account->balance - $validatedData['amount']) < 10) {
            return sendErrorResponse('Account balance must be 10 after withdraw', 400);
        }

        $withdraw = Withdraw::create([
            'user_id' => auth()->id(),
            'amount' => $validatedData['amount'],
            'payment_method' => $validatedData['payment_method'],
            'mobile_number' => $validatedData['mobile_number'],
            'status' => 'due',
        ]);

        $account->update(['balance' => $account->balance - $validatedData['amount']]);

        return sendSuccessResponse(
            'Withdraw request created successfully',
            WithdrawResource::make($withdraw),
            201
        );
    }

    /**
     * @throws Throwable
     */
    public function approve($id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $withdraw = Withdraw::with('user')->findOrFail($id);
            if ($withdraw->status !== 'due') {
                return sendErrorResponse('Withdraw request is already processed', 400);
            }

            $withdraw->update(['status' => 'paid']);
            $withdraw->user->account->update(['total_withdrawn' => $withdraw->user->account->total_withdrawn + $withdraw->amount]);

            return sendSuccessResponse('Withdraw request approved successfully',
                WithdrawResource::make($withdraw)
            );
        });
    }
}
