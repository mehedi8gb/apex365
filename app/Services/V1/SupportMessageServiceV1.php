<?php

namespace App\Services\V1;

use App\Http\Resources\V1\AdminSupportMessageResourceV1;
use App\Http\Resources\V1\ClientSupportMessageResourceV1;
use App\Models\SupportMessage;
use Exception;

class SupportMessageServiceV1
{
    public function create(array $data): SupportMessage
    {
        return SupportMessage::create($data);
    }

    /**
     * @throws Exception
     */
    public function getAllByTicket(int $ticketId): array
    {
        $query = SupportMessage::query();
        $query->where('ticket_id', $ticketId);

        return handleApiRequest(request(), $query, [], ClientSupportMessageResourceV1::class);
    }

    /**
     * @throws Exception
     */
    public function getAllByTicketForAdmin(int $ticketId): array
    {
        $query = SupportMessage::query();
        $query->where('ticket_id', $ticketId);

        return handleApiRequest(request(), $query, [], AdminSupportMessageResourceV1::class);
    }
}
