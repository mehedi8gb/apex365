<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpinnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $todaySpinTime = now();
        $todaySpinTime->setTime(
            $this->spin_time->hour,
            $this->spin_time->minute,
            $this->spin_time->second
        )->format('Y-m-d H:i:s');

        return [
            'id' => $this->id,
            'rotation_point' => $this->rotation_point,

            // Format time with AM/PM
            'spin_time' => $this->spin_time->format('h:i:s A'),

            // Set today's date but keep the time from spin_time
            'spin_time_with_today_date' => $todaySpinTime,

            // Convert time to milliseconds without date
            'spin_time_in_ms' => $todaySpinTime->timestamp * 1000,

            'created_at' => $this->created_at,
        ];
    }
}
