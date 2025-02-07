<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Database\Factories\WithdrawFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WithdrawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WithdrawFactory::times(50)->create();
    }
}
