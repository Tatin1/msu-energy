<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\BuildingLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\BuildingLog>
 */
class BuildingLogFactory extends Factory
{
    protected $model = BuildingLog::class;

    public function definition(): array
    {
        $buildingCode = Building::query()->inRandomOrder()->value('code');
        if (!$buildingCode) {
            return [];
        }

        $start = fake()->dateTimeBetween('-7 days', '-1 day');
        $end = (clone $start)->modify('+1 hour');

        return [
            'building' => $buildingCode,
            'date' => $start->format('Y-m-d'),
            'time' => $start->format('H:i:s'),
            'time_ed' => $end->format('H:i:s'),
            'f' => fake()->randomFloat(2, 59.5, 60.5),
            'v1' => fake()->randomFloat(2, 210, 250),
            'v2' => fake()->randomFloat(2, 210, 250),
            'v3' => fake()->randomFloat(2, 210, 250),
            'a1' => fake()->randomFloat(2, 30, 200),
            'a2' => fake()->randomFloat(2, 30, 200),
            'a3' => fake()->randomFloat(2, 30, 200),
            'pf1' => fake()->randomFloat(3, 0.7, 1.0),
            'pf2' => fake()->randomFloat(3, 0.7, 1.0),
            'pf3' => fake()->randomFloat(3, 0.7, 1.0),
            'kwh' => fake()->randomFloat(3, 0, 1000),
        ];
    }
}
