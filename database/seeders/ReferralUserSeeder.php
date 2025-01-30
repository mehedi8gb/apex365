<?php

namespace Database\Seeders;

use App\Models\ReferralUser;
use Database\Factories\ReferralUserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferralUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReferralUserFactory::times(50)->create();
    }
}
