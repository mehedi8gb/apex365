<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientReferralProfilesResourceV1 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $u = $this->resource->user; // the referred user

        return [
            'id'              => $this->resource->id,
            'user_id'         => $u->id,
            'name'            => $u->name,
            'profile_picture' => getProfileImageUrl($u->id),
        ];
    }
}
