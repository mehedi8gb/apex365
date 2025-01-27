<?php

namespace Database\Seeders;

use App\Models\Referral;
use App\Models\User;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Seed Users with Referral Logic
        $users = User::get();

        // Seed Referrals
        $users->each(function ($user) {
            if ($user->referral_code_id) {
                Referral::create([
                    'user_id' => $user->id,
                    'referred_by' => User::where('referral_code_id', '!=', $user->referral_code_id)->inRandomOrder()->first()->id,
                ]);
            }
        });

        // Seed Transactions
        TransactionFactory::times(100)->create();

        $this->command->info('Database seeded with Referral Codes, Users, Referrals, and Transactions.');
    }
}
