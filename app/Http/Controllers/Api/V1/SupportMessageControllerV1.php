<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SupportMessageResourceV1;
use App\Services\V1\SupportMessageServiceV1;
use Exception;
use Illuminate\Http\Request;

class SupportMessageControllerV1 extends Controller
{
    public function __construct(protected SupportMessageServiceV1 $messageService) {}

    /**
     * @throws Exception
     */
    public function index($ticketId)
    {
        $messages = $this->messageService->getAllByTicket($ticketId);

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

        return sendSuccessResponse('Support message created successfully', new SupportMessageResourceV1($message));
    }
}
