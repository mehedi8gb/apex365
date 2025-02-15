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
        );

        // Convert 24-hour time to milliseconds
        $spinTimeInMs = ($todaySpinTime->hour * 3600000) + // Hours to ms
            ($todaySpinTime->minute * 60000) + // Minutes to ms
            ($todaySpinTime->second * 1000);  // Seconds to ms
        // Get the complete milliseconds including the date
        $spinTimeInMs = $todaySpinTime->timestamp * 1000;

        return [
            'id' => $this->id,
            'rotation_point' => $this->rotation_point,

            // 12-hour format with AM/PM
            'spin_time' => $todaySpinTime->format('h:i:s A'),

            // 24-hour format
            'spin_time_24h' => $todaySpinTime->format('H:i:s'),

            // Set today's date but keep the time from spin_time
            'spin_time_with_today_date' => $todaySpinTime->format('Y-m-d H:i:s'),

            // Convert time to milliseconds based on 24-hour format
            'spin_time_in_ms' => $spinTimeInMs,

            'created_at' => $this->created_at,
        ];
    }
}
