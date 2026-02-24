<?php

namespace Database\Factories;

use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::where('id', '!=', 1)->inRandomOrder()->first()->id, // exclude admin
            'subject' => $this->faker->randomElement([
                'Unable to login to my account',
                'Payment not going through',
                'How do I reset my password?',
                'App crashes when uploading a file',
                'Refund request for my last order',
                'Need help with account verification',
                'Delivery delayed, need an update',
                'Coupon code not working',
                'Error while checking out',
                'How to update my profile information?'
            ]),
            'status'  => $this->faker->randomElement(SupportTicketStatus::cases()),
        ];
    }
}
