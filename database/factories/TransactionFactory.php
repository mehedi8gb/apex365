<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $buyer = User::inRandomOrder()->first();
        $referral1 = $buyer->directReferrals()->first();
        $referral2 = $referral1 ? $referral1->referrer : null;
        $referral3 = $referral2 ? $referral2->referrer : null;

        return [
            'buyer_id' => $buyer->id,
            'referrer_level_1' => $referral1->id ?? null,
            'referrer_level_2' => $referral2->id ?? null,
            'referrer_level_3' => $referral3->id ?? null,
            'purchase_amount' => 100, // Example purchase amount
            'level_1_commission' => 30,
            'level_2_commission' => 20,
            'level_3_commission' => 10,
            'metadata' => json_encode(['product' => 'Item']),
        ];
    }
}
