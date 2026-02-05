<?php

namespace Database\Factories;

use App\Models\Meter;
use App\Models\Reading;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Reading>
 */
class ReadingFactory extends Factory
{
    protected $model = Reading::class;

    public function definition(): array
    {
        $meter_id = Meter::query()->inRandomOrder()->value('id');
        if(!$meter_id) {
            return [];
        }

        $recordedAt = fake()->dateTimeBetween('-2 days', 'now');

        return [
            'meter_id' => $meter_id,
            'voltage1' => fake()->randomFloat(2, 210, 250),
            'voltage2' => fake()->randomFloat(2, 210, 250),
            'voltage3' => fake()->randomFloat(2, 210, 250),
            'current1' => fake()->randomFloat(2, 30, 250),
            'current2' => fake()->randomFloat(2, 30, 250),
            'current3' => fake()->randomFloat(2, 30, 250),
            'power_factor' => fake()->randomFloat(3, 0.7, 1.0),
            'active_power' => fake()->randomFloat(3, 50, 500),
            'reactive_power' => fake()->randomFloat(3, 10, 200),
            'apparent_power' => fake()->randomFloat(3, 60, 550),
            'kwh' => fake()->randomFloat(3, 0, 1200),
            'recorded_at' => $recordedAt,
        ];
    }
}
