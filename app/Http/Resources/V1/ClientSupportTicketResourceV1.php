<?php

namespace App\Http\Resources\V1;

use App\Helpers\ResourceHelpers;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientSupportTicketResourceV1 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $messagePage = (int) $request->query('message_page', 1);
        $messagePerPage = (int) $request->query('per_page', 15);

        // Get paginated messages (default 15 per page)
        $messages = SupportMessage::where('ticket_id', $this->resource->id)
            ->latest()
            ->paginate($messagePerPage, ['*'],'message_page', $messagePage);

        return [
            'id'        => $this->resource->id,
            'subject'   => $this->resource->subject,
            'status'    => $this->resource->status,
            'created_at'=> getFormatedDate($this->resource->created_at),
            'messages'  => ClientSupportMessageResourceV1::collection($messages),
            'messages_pagination' => ResourceHelpers::paginationMeta($messages),
        ];
    }
}
