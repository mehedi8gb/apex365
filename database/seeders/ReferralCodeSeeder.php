<?php

namespace Database\Seeders;

use App\Models\Referral;
use App\Models\ReferralCode;
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
        ReferralCode::factory()->create([
            'code' => "REF-12345678",
        ]);
        ReferralCode::factory(100)->create();
    }
}
