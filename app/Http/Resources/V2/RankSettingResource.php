<?php

namespace App\Http\Resources\V2;

use App\Services\Admin\AdminRankService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RankSettingResource extends JsonResource
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
            'name' => $this->resource->name,
            'threshold' => $this->resource->threshold,
            'coins' => (float) $this->resource->coins,
            'cover_image' => config('apex365.microservice.file_api_server') . '/data/rank-cover-image/'. $this->resource->name
        ];
    }
}
