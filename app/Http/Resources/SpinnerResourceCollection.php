<?php

namespace App\Http\Resources;

use App\Models\SpinnerItems;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SpinnerResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $spinnerItems = SpinnerItems::find(1);
        return [
            'items' => $spinnerItems->items,
            'spinner' => SpinnerResource::collection($this->collection),
        ];
    }
}
