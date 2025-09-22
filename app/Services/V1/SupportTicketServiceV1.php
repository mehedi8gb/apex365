<?php

namespace App\Services\V1;

use App\Http\Resources\V1\AdminSupportTicketResourceV1;
use App\Http\Resources\V1\SupportTicketResourceV1;
use App\Models\SupportTicket;
use Exception;

class SupportTicketServiceV1
{
    public function create(array $data): SupportTicket
    {
        return SupportTicket::create($data);
    }

    /**
     * @throws Exception
     */
    public function getAllForAdmin(): array
    {
        $query = SupportTicket::query();
        return handleApiRequest(request(), $query, ['messages', 'user:id,name,phone'], AdminSupportTicketResourceV1::class);
    }

    /**
     * @throws Exception
     */
    public function getAllForCustomer($userId): array
    {
        $query = SupportTicket::query();
        $query->where('user_id', $userId);

        return handleApiRequest(request(), $query, ['messages'],SupportTicketResourceV1::class);
    }

    public function find(int $ticketId): SupportTicket
    {
        return SupportTicket::with('messages')->findOrFail($ticketId);
    }

    public function updateStatus(int $ticketId, string $status): SupportTicket
    {
        $ticket = $this->find($ticketId);
        $ticket->status = $status;
        $ticket->save();

        return $ticket;
    }
}
