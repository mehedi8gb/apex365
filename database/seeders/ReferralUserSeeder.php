<?php

namespace Database\Seeders;

use App\Models\ReferralUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferralUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    ReferralUser::factory()
        ->count(50)
        ->create();
    }
}
