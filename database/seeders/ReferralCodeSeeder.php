<?php

namespace Database\Seeders;

use App\Models\Referral;
use App\Models\ReferralCode;
use Database\Factories\ReferralCodeFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


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
