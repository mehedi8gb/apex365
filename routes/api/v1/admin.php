<?php
// api/v1/admin.php
use App\Http\Controllers\Api\V1\SupportMessageControllerV1;
use App\Http\Controllers\Api\V1\SupportTicketControllerV1;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\isAdminMiddleware;

Route::middleware([JwtMiddleware::class, isAdminMiddleware::class])
    ->prefix('v1/admin')
    ->as('admin.v1.')
    ->group(function () {

        // Tickets
        Route::get('support/tickets', [SupportTicketControllerV1::class, 'adminIndex']);
        Route::get('support/tickets/{ticket}', [SupportTicketControllerV1::class, 'show']);
        Route::patch('support/tickets/{ticket}/status', [SupportTicketControllerV1::class, 'updateStatus']);

        // Messages
        Route::get('support/tickets/{ticket}/messages', [SupportMessageControllerV1::class, 'index']);
        Route::post('support/tickets/{ticket}/messages', [SupportMessageControllerV1::class, 'store']);
    });
