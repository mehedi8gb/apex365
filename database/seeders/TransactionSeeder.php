<?php

namespace Database\Seeders;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TransactionFactory::times(1)->create([
            "transactionId" => "TRX-000111-AAAJJJ",
            ]);
        TransactionFactory::times(99)->create();
    }
}
