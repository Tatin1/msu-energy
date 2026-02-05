<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\SystemLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\SystemLog>
 */
class SystemLogFactory extends Factory
{
    protected $model = SystemLog::class;

    public function definition(): array
    {
        $buildingCode = Building::query()->inRandomOrder()->value('code');
        if (!$buildingCode) {
            return [];
        }

        $start = fake()->dateTimeBetween('-3 days', 'now');
        $end = (clone $start)->modify('+30 minutes');

        $totalKw = fake()->randomFloat(2, 100, 2000);
        $totalKvar = fake()->randomFloat(2, 50, 1200);
        $totalKva = sqrt(($totalKw ** 2) + ($totalKvar ** 2));

        return [
            'building' => $buildingCode,
            'date' => $start->format('Y-m-d'),
            'time' => $start->format('H:i:s'),
            'time_ed' => $end->format('H:i:s'),
            'total_kw' => $totalKw,
            'total_kvar' => $totalKvar,
            'total_kva' => round($totalKva, 2),
            'total_pf' => round($totalKw / max($totalKva, 0.01), 3),
        ];
    }
}
