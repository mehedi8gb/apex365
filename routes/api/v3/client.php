<?php

use App\Http\Controllers\Api\V3\AuthControllerV3;
use App\Http\Middleware\JwtMiddleware;

Route::middleware([JwtMiddleware::class])->prefix('v3/client')->as('client.v3.')->group(function () {
    Route::get('me', [AuthControllerV3::class, 'me']);
});
