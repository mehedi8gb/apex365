<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\WithdrawStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\WithdrawRequest;
use App\Http\Resources\WithdrawResource;
use App\Models\Withdraw;
use App\Services\WithdrawService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class WithdrawControllerV2 extends Controller
{
    protected WithdrawService $withdrawService;

    public function __construct(WithdrawService $withdrawService)
    {
        $this->withdrawService = $withdrawService;
    }

    public function store(WithdrawRequest $request): JsonResponse
    {
        $user = Auth::user();

        // Convert incoming decimal to the smallest unit integer (avoid floats)
        // e.g. user sends 50.00 -> convert to 5000
        $amountInput = $request->input('amount'); // string or numeric
        $amountSmallest = (int) round(floatval($amountInput) * 100);

        try {
            $withdraw = $this->withdrawService->createWithdraw(
                $user->id,
                $amountSmallest,
                $request->input('payment_method'),
                $request->input('mobile_number')
            );

            return sendSuccessResponse('Withdraw request created successfully',
                new WithdrawResource($withdraw),
                201);

        } catch (ValidationException $e) {
           return sendErrorResponse($e, 422);
        } catch (\Exception $e) {
            // Log unexpected errors
            \Log::error('withdraw.store.error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return sendErrorResponse('Server error', 500);
        } catch (Throwable $e) {
            \Log::error('withdraw.store.throwable', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return sendErrorResponse('Server error', 500);
        }
    }
}
