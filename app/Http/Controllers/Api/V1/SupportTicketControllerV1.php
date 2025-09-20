<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SupportTicketResourceV1;
use App\Services\V1\SupportTicketServiceV1;
use Exception;
use Illuminate\Http\Request;

class SupportTicketControllerV1 extends Controller
{
    public function __construct(protected SupportTicketServiceV1 $ticketService)
    {
    }

    /**
     * @throws Exception
     */
    public function adminIndex()
    {
        $tickets = $this->ticketService->getAllForAdmin();

        return sendSuccessResponse('Support tickets retrieved successfully', $tickets);
    }

    /**
     * @throws Exception
     */
    public function customerIndex()
    {
        $tickets = $this->ticketService->getAllForCustomer(auth()->id());

        return sendSuccessResponse('Support tickets retrieved successfully', $tickets);
    }

    public function store(Request $request)
    {
        $ticket = $this->ticketService->create([
            'user_id' => auth()->id(),
            'subject' => $request->input('subject'),
            'status' => SupportTicketStatus::PENDING
        ]);

        return sendSuccessResponse("Support ticket created successfully", new SupportTicketResourceV1($ticket));
    }

    public function show($ticketId)
    {
        $ticket = $this->ticketService->find($ticketId);
        return sendSuccessResponse("Support ticket retrieved successfully", new SupportTicketResourceV1($ticket));
    }

    public function updateStatus(Request $request, $ticketId)
    {
        $status = $request->input('status'); // e.g., 'open' or 'closed'
        $ticket = $this->ticketService->updateStatus($ticketId, $status);

        return sendSuccessResponse("Support ticket status updated successfully", new SupportTicketResourceV1($ticket));
    }
}
