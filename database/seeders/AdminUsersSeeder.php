<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Throwable;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws Throwable
     */
    public function run(): void
    {
        UserFactory::$password = '123456';

        // Create Super Admin
        $superAgent = User::factory()->create([
            'email' => 'admin@demo.com',
            'phone' => '01757575757',
        ]);
        $superAgent->assignRole('admin');
    }
}
