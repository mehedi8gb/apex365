<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            CommissionSettingsSeeder::class,
            AdminRankSettingSeeder::class,
            TransactionSeeder::class,
            ReferralCodeSeeder::class,
            AdminUsersSeeder::class,
        ]);
    }
}
