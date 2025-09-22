<?php
// api/v1/admin.php
use App\Http\Controllers\Api\V1\AdminSupportMessageControllerV1;
use App\Http\Controllers\Api\V1\AdminSupportTicketControllerV1;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\isAdminMiddleware;

Route::middleware([JwtMiddleware::class, isAdminMiddleware::class])
    ->prefix('v1/admin')
    ->as('admin.v1.')
    ->group(function () {

        // Tickets
        Route::get('support/tickets', [AdminSupportTicketControllerV1::class, 'index']);
        Route::get('support/tickets/{ticket}', [AdminSupportTicketControllerV1::class, 'show']);
        Route::patch('support/tickets/{ticket}/status', [AdminSupportTicketControllerV1::class, 'updateStatus']);

        // Messages
        Route::get('support/tickets/{ticket}/messages', [AdminSupportMessageControllerV1::class, 'index']);
        Route::post('support/tickets/{ticket}/messages', [AdminSupportMessageControllerV1::class, 'store']);
    });
