<?php

namespace Database\Factories;

use App\Enums\WithdrawStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'mobile_number' => $this->faker->phoneNumber,
            'payment_method' => $this->faker->randomElement(['bkash', 'nagad', 'rocket']),
            'status' => $this->faker->randomElement(WithdrawStatus::cases())->value,
        ];
    }
}
