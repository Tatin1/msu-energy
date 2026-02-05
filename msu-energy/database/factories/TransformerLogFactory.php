<?php

namespace Database\Factories;

use App\Models\TransformerLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\TransformerLog>
 */
class TransformerLogFactory extends Factory
{
    protected $model = TransformerLog::class;

    public function definition(): array
    {
        return [
            'recorded_at' => fake()->dateTimeBetween('-3 days', 'now'),
            'frequency' => fake()->randomFloat(2, 59.5, 60.5),
            'v1' => fake()->randomFloat(2, 11.0, 13.8),
            'v2' => fake()->randomFloat(2, 11.0, 13.8),
            'v3' => fake()->randomFloat(2, 11.0, 13.8),
            'a1' => fake()->randomFloat(2, 100, 600),
            'a2' => fake()->randomFloat(2, 100, 600),
            'a3' => fake()->randomFloat(2, 100, 600),
            'pf' => fake()->randomFloat(3, 0.75, 1.0),
            'kwh' => fake()->randomFloat(3, 100, 2000),
        ];
    }
}
