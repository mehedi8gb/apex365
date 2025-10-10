<?php

use App\Http\Controllers\Api\V2\AuthControllerV2;
use App\Http\Controllers\Api\V2\WithdrawControllerV2;
use App\Http\Controllers\ClientRankSettingController;
use App\Http\Middleware\JwtMiddleware;

Route::middleware([JwtMiddleware::class])->prefix('v2/client')->as('client.v2.')->group(function () {
    Route::post('withdraws', [WithdrawControllerV2::class, 'store']);
    Route::get('me', [AuthControllerV2::class, 'me']);
    Route::get('rank-settings', [ClientRankSettingController::class, 'index']);
});
