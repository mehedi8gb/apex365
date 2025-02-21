<?php

namespace Database\Seeders;

use App\Models\Referral;
use App\Models\ReferralCode;
use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ReferralCodeSeeder::class,
            UsersSeeder::class,
//            ReferralSeeder::class,
            AccountSeeder::class,
            WithdrawSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
