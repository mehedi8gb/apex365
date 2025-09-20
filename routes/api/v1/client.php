<?php
// api/v1/client.php
use App\Http\Controllers\Api\V1\SupportMessageControllerV1;
use App\Http\Controllers\Api\V1\SupportTicketControllerV1;
use App\Http\Middleware\JwtMiddleware;

Route::middleware([JwtMiddleware::class])->prefix('v1/client')->as('client.v1.')->group(function () {

// Tickets
    Route::get('support/tickets', [SupportTicketControllerV1::class, 'customerIndex']);
    Route::post('support/tickets', [SupportTicketControllerV1::class, 'store']);
    Route::get('support/tickets/{ticket}', [SupportTicketControllerV1::class, 'show']);

    // Messages
    Route::get('support/tickets/{ticket}/messages', [SupportMessageControllerV1::class, 'index']);
    Route::post('support/tickets/{ticket}/messages', [SupportMessageControllerV1::class, 'store']);
});

