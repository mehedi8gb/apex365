<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Database\Factories\AccountFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{

    public function run(): void
    {
        foreach (User::all() as $user) {
            AccountFactory::$userId = $user->id;
            AccountFactory::times(1)->create();
        }
    }
}
