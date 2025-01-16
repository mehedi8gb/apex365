<?php

namespace Database\Seeders;

use App\Models\Referral;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Predefined UUID for testing
        $testUuid = '123e4567-e89b-12d3-a456-426614174000';

        // Create the referral with the predefined UUID
        Referral::create([
            'referralId' => $testUuid,
        ]);

        // Generate 99 random unique referral codes
        for ($i = 0; $i < 99; $i++) {
            Referral::create([
                'referralId' => Str::uuid(),
            ]);
        }
    }
}
