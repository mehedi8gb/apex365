<?php

namespace App\Http\Middleware;

use App\Models\SupportTicket;
use Closure;
use Exception;
use Illuminate\Http\Request;

class EnsureTicketOwner
{
    /**
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $ticketId = (int) $request->route('ticket'); // route param {ticket}
        $ticket = SupportTicket::findOrCustomFail($ticketId);

        if ($ticket->user_id != auth()->id()) {
            return sendErrorResponse("Unauthorized: You're not authorized to interact this", 403);
        }

        return $next($request);
    }
}
