<?php

namespace Database\Seeders;

use App\Models\AdminRankSetting;
use Illuminate\Database\Seeder;

class AdminRankSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranks = [
            [
                'name'      => 'Pro Platinum',
                'threshold' => 10,
                'coins'     => 30000,
            ],
            [
                'name'      => 'Platinum',
                'threshold' => 8,
                'coins'     => 20000,
            ],
            [
                'name'      => 'Diamond',
                'threshold' => 6,
                'coins'     => 10000,
            ],
            [
                'name'      => 'Gold',
                'threshold' => 4,
                'coins'     => 5000,
            ],
            [
                'name'      => 'Silver',
                'threshold' => 2,
                'coins'     => 2500,
            ],
            [
                'name'      => 'Bronze',
                'threshold' => 1,
                'coins'     => 1000,
            ],
        ];


        foreach ($ranks as $rank) {
            AdminRankSetting::updateOrCreate(['name' => $rank['name']], $rank);
        }
    }
}
