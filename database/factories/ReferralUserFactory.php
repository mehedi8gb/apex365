<?php

namespace Database\Factories;

use App\Models\Referral;
use App\Models\ReferralUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReferralUser>
 */
class ReferralUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'referralId' => Referral::inRandomOrder()->first()->id,
            'user_id' => User::factory(),
        ];
    }
}
