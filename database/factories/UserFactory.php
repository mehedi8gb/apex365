<?php

namespace Database\Factories;

use App\Models\ReferralCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    public static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->userName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt(self::$password),
            'role' => 'customer',
            'nid' => rand(1000000000, 9999999999),
            'address' => $this->faker->address,
            'account_type' => 'new', // Default to new
            'referral_code_id' => ReferralCode::inRandomOrder()->first()->id,
            'metadata' => json_encode(['profile' => 'basic']),
        ];
    }
}
