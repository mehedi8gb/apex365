<?php

use App\Http\Controllers\Admin\AdminRankSettingController;
use App\Http\Controllers\Admin\CommissionSettingController;
use App\Http\Controllers\Api\V2\DashboardControllerV2;
use App\Http\Controllers\Api\V2\TransactionControllerV2;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WithdrawController;
use App\Http\Middleware\isAdminMiddleware;
use App\Http\Middleware\JwtMiddleware;

Route::middleware([JwtMiddleware::class, isAdminMiddleware::class])->prefix('v2/admin')->as('admin.v2.')->group(function () {
    Route::get('commissions', [CommissionSettingController::class, 'index']);
    Route::put('commissions/{type}', [CommissionSettingController::class, 'update']);
    Route::get('commissions-history', [CommissionSettingController::class, 'commissionsHistory']);
    Route::post('withdraws/{id}/approve', [WithdrawController::class, 'approve']);
    Route::post('withdraws/{id}/suspend', [WithdrawController::class, 'suspend']);
    Route::apiResource('users', CustomerController::class);

    Route::get('rank-settings', [AdminRankSettingController::class, 'index']);
    Route::put('rank-settings', [AdminRankSettingController::class, 'update']);
    Route::delete('rank-settings', [AdminRankSettingController::class, 'delete']);
    Route::get('transactions/user/{userId}', [TransactionControllerV2::class, 'userTransactions'])->middleware([isAdminMiddleware::class]);
    Route::get('transactions/users', [TransactionControllerV2::class, 'usersTransactions'])->middleware([isAdminMiddleware::class]);
    Route::apiResource('transactions', TransactionControllerV2::class);
    Route::get('/dashboard', [DashboardControllerV2::class, 'index']);
});
