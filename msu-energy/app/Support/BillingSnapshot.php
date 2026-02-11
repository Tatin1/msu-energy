<?php

namespace App\Support;

use App\Models\Billing;
use App\Models\Building;
use App\Models\Reading;
use App\Models\Tariff;

class BillingSnapshot
{
    public static function build(): array
    {
        $tariffRate = (float) (Tariff::query()->value('rate') ?? 0);

        $summary = [
            'this_month_kwh' => (float) Billing::query()->sum('this_month_kwh'),
            'last_month_kwh' => (float) Billing::query()->sum('last_month_kwh'),
            'avg_pf' => (float) (Reading::query()->whereNotNull('power_factor')->avg('power_factor') ?? 0),
        ];
        $summary['total_cost'] = round($summary['this_month_kwh'] * $tariffRate, 2);

        $readingStats = Reading::query()
            ->selectRaw('meters.building_id, AVG(readings.power_factor) as avg_pf')
            ->join('meters', 'meters.id', '=', 'readings.meter_id')
            ->groupBy('meters.building_id')
            ->get()
            ->keyBy('building_id');

        $buildingSeries = Building::query()
            ->select('id', 'code', 'name')
            ->with(['billing:id,building_id,this_month_kwh,last_month_kwh,total_bill'])
            ->orderBy('code')
            ->get()
            ->map(function (Building $building) use ($tariffRate, $readingStats) {
                $billing = $building->billing;
                $avgPf = optional($readingStats->get($building->id))->avg_pf;

                $thisMonth = (float) ($billing->this_month_kwh ?? 0);
                $lastMonth = (float) ($billing->last_month_kwh ?? 0);
                $cost = $billing && $billing->total_bill
                    ? (float) $billing->total_bill
                    : $thisMonth * $tariffRate;

                return [
                    'id' => $building->id,
                    'code' => $building->code,
                    'name' => $building->name,
                    'this_month_kwh' => round($thisMonth, 3),
                    'last_month_kwh' => round($lastMonth, 3),
                    'cost' => round($cost, 2),
                    'avg_pf' => $avgPf !== null ? round((float) $avgPf, 4) : null,
                ];
            })
            ->values()
            ->all();

        $trendSeries = self::buildTrendSeries();

        return [
            'rate' => $tariffRate,
            'summary' => $summary,
            'buildings' => $buildingSeries,
            'trend' => $trendSeries,
        ];
    }

    private static function buildTrendSeries(): array
    {
        $now = now();

        return collect(range(6, 0))->map(function (int $offset) use ($now) {
            $date = $now->copy()->subDays($offset)->startOfDay();
            $end = $date->copy()->endOfDay();

            $total = (float) Reading::query()
                ->whereBetween('recorded_at', [$date, $end])
                ->sum('kwh');

            return [
                'label' => $date->format('M d'),
                'kwh' => round($total, 3),
            ];
        })->values()->all();
    }
}
