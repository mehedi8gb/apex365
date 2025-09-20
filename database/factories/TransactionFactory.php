<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Enums\WithdrawStatus;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'transactionId' => $this->faker->unique()->regexify('TRX-[0-9]{6}-[A-Z0-9]{5}'), // Random unique transaction ID
            'status' => $this->faker->randomElement(TransactionStatus::cases())->value,
        ];
    }
}
