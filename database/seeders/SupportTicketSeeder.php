<?php

namespace Database\Seeders;

use Database\Factories\SupportMessageFactory;
use Database\Factories\SupportTicketFactory;
use Illuminate\Database\Seeder;

class SupportTicketSeeder extends Seeder
{
    public function run(): void
    {
        // Create 30 tickets each with random messages
        SupportTicketFactory::times(30)
            ->create()
            ->each(function ($ticket) {
                // For each ticket, generate 20-50 messages alternating between customer and admin
                $messagesCount = rand(50, 1000);

                for ($i = 0; $i < $messagesCount; $i++) {
                    $isCustomer = $i % 2 === 0; // alternate

                    SupportMessageFactory::times(1)->create([
                        'ticket_id'   => $ticket->id,
                        'sender_id'   => $isCustomer ? $ticket->user_id : 1,
                        'sender_type' => $isCustomer ? 'customer' : 'admin',
                    ]);
                }
            });
    }
}
