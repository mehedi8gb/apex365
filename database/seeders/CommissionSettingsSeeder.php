<?php

namespace Database\Seeders;

use App\Models\CommissionSetting;
use Illuminate\Database\Seeder;

class CommissionSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CommissionSetting::insert([
            [
                'type' => 'signup',
                'levels' => json_encode([
                    1 => 30,
                    2 => 80,
                    3 => 5,
                    4 => 2.5,
                    5 => 1,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'purchase',
                'levels' => json_encode([
                    1 => 30,
                    2 => 30,
                    3 => 5,
                    4 => 2.5,
                    5 => 1,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
