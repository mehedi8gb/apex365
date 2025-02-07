<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    public static $userId;
    public function definition(): array
    {
        return [
            'user_id' => self::$userId,
            'balance' => rand(100, 3000), // Random initial balance
        ];
    }
}
