<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdminSupportMessageResourceV1;
use App\Services\V1\SupportMessageServiceV1;
use Exception;
use Illuminate\Http\Request;

class AdminSupportMessageControllerV1 extends Controller
{
    public function __construct(protected SupportMessageServiceV1 $messageService) {}

    /**
     * @throws Exception
     */
    public function index($ticketId)
    {
        $messages = $this->messageService->getAllByTicketForAdmin($ticketId);

        return sendSuccessResponse('Support messages retrieved successfully', $messages);
    }

    public function store(Request $request, $ticketId)
    {
        $message = $this->messageService->create([
            'ticket_id'   => $ticketId,
            'sender_id'   => auth()->id(),
            'sender_type' => auth()->user()->primaryRole(),
            'message'     => $request->input('message'),
            'attachments' => $request->input('attachments', []),
        ]);

        return sendSuccessResponse('Support message created successfully', new AdminSupportMessageResourceV1($message));
    }
}
