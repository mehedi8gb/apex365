<?php

namespace Database\Factories;

use App\Models\ReferralCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralCodeFactory extends Factory
{
    protected $model = ReferralCode::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('REF???')),
            'is_active' => true,
            'metadata' => json_encode(['description' => 'Referral Code']),
        ];
    }
}
