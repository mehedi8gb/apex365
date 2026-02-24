<?php

namespace Database\Seeders;

use Database\Factories\SpinnerItemsFactory;
use Illuminate\Database\Seeder;

class SpinnerItemsSeeder extends Seeder
{
    public function run(): void
    {
        // Seed with predefined factory data
        SpinnerItemsFactory::times(1)->create();
    }
}
