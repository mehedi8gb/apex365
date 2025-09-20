<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResourceV1 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->resource->id,
            'subject'   => $this->resource->subject,
            'status'    => $this->resource->status,
            'created_at'=> getFormatedDate($this->resource->created_at),
            'messages'  => SupportMessageResourceV1::collection($this->whenLoaded('messages')),
        ];
    }
}
