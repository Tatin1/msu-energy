<?php

namespace Database\Factories;

use App\Models\Meter;
use App\Models\Tariff;
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
        $meterId = Meter::query()->inRandomOrder()->value('id');
        $recordedAt = fake()->dateTimeBetween('-3 days', 'now');
        $kw1 = fake()->randomFloat(3, 20, 220);
        $kw2 = fake()->randomFloat(3, 20, 220);
        $kw3 = fake()->randomFloat(3, 20, 220);
        $kwiii = round($kw1 + $kw2 + $kw3, 3);
        $kvariii = fake()->randomFloat(3, 30, 260);
        $kvaiii = round(sqrt(($kwiii ** 2) + ($kvariii ** 2)), 3);
        $pf1 = fake()->randomFloat(3, 0.75, 1.0);
        $pf2 = fake()->randomFloat(3, 0.75, 1.0);
        $pf3 = fake()->randomFloat(3, 0.75, 1.0);
        $pfiii = round(($pf1 + $pf2 + $pf3) / 3, 3);
        $kwh = fake()->randomFloat(3, 100, 2000);
        $tariffRate = (float) (Tariff::query()->value('rate') ?? 0);

        return [
            'meter_id' => $meterId,
            'date' => $recordedAt->format('Y-m-d'),
            'time' => $recordedAt->format('H:i:s'),
            'time_ed' => (clone $recordedAt)->modify('+15 minutes')->format('H:i:s'),
            'recorded_at' => $recordedAt,
            'frequency' => fake()->randomFloat(2, 59.5, 60.5),
            'v1' => fake()->randomFloat(2, 11.0, 13.8),
            'v2' => fake()->randomFloat(2, 11.0, 13.8),
            'v3' => fake()->randomFloat(2, 11.0, 13.8),
            'a1' => fake()->randomFloat(2, 100, 600),
            'a2' => fake()->randomFloat(2, 100, 600),
            'a3' => fake()->randomFloat(2, 100, 600),
            'kw1' => $kw1,
            'kw2' => $kw2,
            'kw3' => $kw3,
            'pf1' => $pf1,
            'pf2' => $pf2,
            'pf3' => $pf3,
            'kwiii' => $kwiii,
            'kvaiii' => $kvaiii,
            'kvariii' => $kvariii,
            'pfiii' => $pfiii,
            'kwh' => $kwh,
            'cost' => round($kwh * $tariffRate, 2),
        ];
    }
}
