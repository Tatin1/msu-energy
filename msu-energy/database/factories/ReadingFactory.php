<?php

namespace Database\Factories;

use App\Models\Meter;
use App\Models\Reading;
use App\Models\Tariff;
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

        // $recordedAt = fake()->dateTimeBetween('-2 days', 'now');
        $recordedAt = now()->startOfDay()->addHours(fake()->numberBetween(0, 23))->addMinutes(fake()->numberBetween(0, 59));
        $kw1 = fake()->randomFloat(3, 10, 180);
        $kw2 = fake()->randomFloat(3, 10, 180);
        $kw3 = fake()->randomFloat(3, 10, 180);
        $activePower = round($kw1 + $kw2 + $kw3, 3);
        $reactivePower = fake()->randomFloat(3, 10, 200);
        $apparentPower = round(sqrt(($activePower ** 2) + ($reactivePower ** 2)), 3);
        $pf1 = fake()->randomFloat(3, 0.7, 1.0);
        $pf2 = fake()->randomFloat(3, 0.7, 1.0);
        $pf3 = fake()->randomFloat(3, 0.7, 1.0);
        $powerFactor = round(($pf1 + $pf2 + $pf3) / 3, 3);
        $kwh = fake()->randomFloat(3, 0, 1200);
        $tariffRate = (float) (Tariff::query()->value('rate') ?? 0);

        return [
            'meter_id' => $meter_id,
            'f' => fake()->randomFloat(2, 59.5, 60.5),
            'v1' => fake()->randomFloat(2, 210, 250),
            'v2' => fake()->randomFloat(2, 210, 250),
            'v3' => fake()->randomFloat(2, 210, 250),
            'a1' => fake()->randomFloat(2, 30, 250),
            'a2' => fake()->randomFloat(2, 30, 250),
            'a3' => fake()->randomFloat(2, 30, 250),
            'kw1' => $kw1,
            'kw2' => $kw2,
            'kw3' => $kw3,
            'pf1' => $pf1,
            'pf2' => $pf2,
            'pf3' => $pf3,
            'pfiii' => $powerFactor,
            'kwiii' => $activePower,
            'kvariii' => $reactivePower,
            'kvaiii' => $apparentPower,
            'kwhiii' => $kwh,
            'cost' => round($kwh * $tariffRate, 2),
            'time' => $recordedAt,
            'time_end' => $recordedAt->copy()->addMinutes(15),
        ];
    }
}
