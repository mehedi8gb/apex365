<?php

namespace App\Http\Resources;

use App\Services\Admin\AdminRankService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminRankSettingResource extends JsonResource
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
            'cover_image' => config('apex365.microservice.file_api_server') . '/data/rank-cover-image/'. $this->resource->name,
            'created_at' => getFormatedDate($this->resource->created_at),
            'updated_at' => getFormatedDate($this->resource->updated_at),
        ];
    }
}
