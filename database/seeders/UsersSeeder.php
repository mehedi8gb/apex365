<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Agent
        $superAgent = User::factory()->create([
            'email' => 'superagent@example.com',
        ]);
        $superAgent->assignRole('super_agent');

        // Create Agents
        User::factory()
            ->count(5)
            ->create()
            ->each(function ($user) {
                $user->assignRole('agent');
            });

        // Create Students
        User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) {
                $user->assignRole('student');
            });

        // Create Universities
        User::factory()
            ->count(3)
            ->create()
            ->each(function ($user) {
                $user->assignRole('university');
            });

        // Create Staff
        User::factory()
            ->count(5)
            ->create()
            ->each(function ($user) {
                $user->assignRole('staff');
            });
    }
}
