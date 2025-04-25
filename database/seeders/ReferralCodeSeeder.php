<?php

namespace Database\Seeders;

use Database\Factories\ReferralCodeFactory;
use Illuminate\Database\Seeder;


class ReferralCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Referral Codes
        ReferralCodeFactory::times(1)->create([
            'code' => "REF-12345678",
        ]);
        ReferralCodeFactory::times(99)->create();
    }
}
