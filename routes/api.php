<?php

use App\Http\Controllers\Admin\CommissionSettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SpinnerController;
use App\Http\Controllers\SpinnerLeaderboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserShopController;
use App\Http\Controllers\WithdrawController;
use App\Http\Middleware\isAdminMiddleware;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\RefreshTokenMiddleware;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/v2/admin.php';

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

    Route::delete('transactions/all-delete', [TransactionController::class, 'deleteMultiple'])->middleware([isAdminMiddleware::class]);
    Route::post('transactions/apply-commissions', [TransactionController::class, 'ApplyCommissions'])->middleware([isAdminMiddleware::class]);
    Route::get('transactions/user/{userId}', [TransactionController::class, 'userTransactions'])->middleware([isAdminMiddleware::class]);
    Route::get('transactions/users', [TransactionController::class, 'usersTransactions'])->middleware([isAdminMiddleware::class]);
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

    Route::post('/shop-details', [UserShopController::class, 'updateShopDetails']);
    Route::get('/shop-details', [UserShopController::class, 'getShopDetails']);
});
