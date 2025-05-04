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
            'user_id' => User::factory(),
            'referrer_id' => User::factory(),
            'referral_code_id' => ReferralCodeFactory::times(1)->create()->first()->id,
            'level' => $this->faker->numberBetween(1, 5),
        ];
    }
}
