<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SpinnerController;
use App\Http\Controllers\SpinnerLeaderboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WithdrawController;
use App\Http\Middleware\isAdminMiddleware;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\RefreshTokenMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    // Authentication routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Password management
    Route::post('forget', [AuthController::class, 'forget']);
    Route::post('validate', [AuthController::class, 'validateCode']);
    Route::post('reset', [AuthController::class, 'resetPassword']);

    // Token management
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware([RefreshTokenMiddleware::class]);
    Route::post('logout', [AuthController::class, 'logout'])->middleware([JwtMiddleware::class]);
});

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('me', [AuthController::class, 'me']);

    Route::get('/referrals', [ReferralController::class, 'index']); // All referrals
    Route::get('/referrals/{userId}', [ReferralController::class, 'show']); // Specific user referrals
    Route::get('/referrals/tree/{userId}', [ReferralController::class, 'getReferralNodes']); // Specific user referrals

    Route::delete('transactions/all-delete', [TransactionController::class, 'deleteMultiple'])->middleware([isAdminMiddleware::class]);
    Route::get('user/{userId}/transactions', [TransactionController::class, 'userTransactions'])->middleware([isAdminMiddleware::class]);
    Route::get('users/transactions', [TransactionController::class, 'usersTransactions'])->middleware([isAdminMiddleware::class]);
    Route::apiResource('transactions', TransactionController::class);


    Route::get('/commissions', [CommissionController::class, 'index']); // All commissions
    Route::get('/commissions/{userId}', [CommissionController::class, 'show']); // User commission earnings


    Route::get('/withdraws', [WithdrawController::class, 'index']);
    Route::post('/withdraws', [WithdrawController::class, 'store']);
    Route::post('/withdraws/{id}/approve', [WithdrawController::class, 'approve'])->middleware([isAdminMiddleware::class]);

    Route::patch('spinner-items', [SpinnerController::class, 'updateItems']);
    Route::post('spinner-items', [SpinnerController::class, 'storeItems']);
    Route::apiResource('spinner', SpinnerController::class);
    Route::apiResource('users', CustomerController::class)->middleware([isAdminMiddleware::class]);
    Route::apiResource('leaderboard', SpinnerLeaderboardController::class);


});
// role based route system has to be integrated
