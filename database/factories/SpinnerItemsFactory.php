<?php

namespace Database\Factories;

use App\Models\SpinnerItems;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpinnerItemsFactory extends Factory
{
    protected $model = SpinnerItems::class;

    public function definition(): array
    {
        return [
            'items' => [
                [
                    "value" => "10",
                    "rotation_point" => 0,
                ],
                [
                    "value" => "20",
                    "rotation_point" => 1,
                ],
                [
                    "value" => "50",
                    "rotation_point" => 2,
                ],
                [
                    "value" => "300",
                    "rotation_point" => 3,
                ],
                [
                    "value" => "100",
                    "rotation_point" => 4,
                ],
                [
                    "value" => "200",
                    "rotation_point" => 5,
                ],
                [
                    "value" => "350",
                    "rotation_point" => 6,
                ],
                [
                    "value" => "400",
                    "rotation_point" => 7,
                ],
            ]
        ];
    }
}
