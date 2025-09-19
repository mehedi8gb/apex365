<?php

use App\Http\Controllers\Api\V2\AuthControllerV2;
use App\Http\Controllers\Api\V2\WithdrawControllerV2;
use App\Http\Middleware\JwtMiddleware;

Route::middleware([JwtMiddleware::class])->prefix('v2/client')->as('client.')->group(function () {
    Route::post('withdraws', [WithdrawControllerV2::class, 'store']);
    Route::get('me', [AuthControllerV2::class, 'me']);

});
