<?php

// api/v1/client.php
use App\Http\Controllers\Api\V1\ClientSupportMessageControllerV1;
use App\Http\Controllers\Api\V1\ClientSupportTicketControllerV1;
use App\Http\Middleware\EnsureTicketOwner;
use App\Http\Middleware\JwtMiddleware;

Route::middleware([JwtMiddleware::class])->prefix('v1/client')->as('client.v1.')->group(function () {

    // Tickets
    Route::get('support/tickets', [ClientSupportTicketControllerV1::class, 'index']);
    Route::post('support/tickets', [ClientSupportTicketControllerV1::class, 'store']);

    Route::middleware([EnsureTicketOwner::class])->group(function () {
        Route::get('support/tickets/{ticket}', [ClientSupportTicketControllerV1::class, 'show']);
        Route::patch('support/tickets/{ticket}/status', [ClientSupportTicketControllerV1::class, 'updateStatus']);

        // Messages
        Route::get('support/tickets/{ticket}/messages', [ClientSupportMessageControllerV1::class, 'index']);
        Route::post('support/tickets/{ticket}/messages', [ClientSupportMessageControllerV1::class, 'store']);
    });
});
