<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminSupportMessageResourceV1 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'sender' => [
                'id' => $this->resource->sender->id,
                'name' => $this->resource->sender->name,
                'phone' => $this->resource->sender->phone,
            ],
            'sender_type' => $this->resource->sender_type,
            'message' => $this->resource->message,
            'attachments' => $this->resource->attachments,
            'created_at' => getFormatedDate($this->resource->created_at),
        ];
    }
}
