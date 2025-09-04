<?php

use App\Http\Controllers\Admin\AdminRankSettingController;
use App\Http\Controllers\Admin\CommissionSettingController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WithdrawController;
use App\Http\Middleware\isAdminMiddleware;
use App\Http\Middleware\JwtMiddleware;

Route::middleware([JwtMiddleware::class, isAdminMiddleware::class])->prefix('v2/admin')->as('admin.')->group(function () {
    Route::get('commissions', [CommissionSettingController::class, 'index']);
    Route::put('commissions/{type}', [CommissionSettingController::class, 'update']);
    Route::get('commissions-history', [CommissionSettingController::class, 'commissionsHistory']);
    Route::post('withdraws/{id}/approve', [WithdrawController::class, 'approve'])->middleware([isAdminMiddleware::class]);
    Route::apiResource('users', CustomerController::class)->middleware([isAdminMiddleware::class]);

    Route::get('rank-settings', [AdminRankSettingController::class, 'index']);
    Route::put('rank-settings', [AdminRankSettingController::class, 'update']);
    Route::delete('rank-settings', [AdminRankSettingController::class, 'delete']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
