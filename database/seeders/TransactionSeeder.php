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
        TransactionFactory::times(100)->create();
    }
}
