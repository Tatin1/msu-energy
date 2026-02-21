<?php

namespace App\Support;

use App\Models\Billing;
use App\Models\Building;
use App\Models\Reading;
use App\Models\Tariff;
use Illuminate\Support\Carbon;

class BillingSnapshot
{
    public static function build(?int $buildingId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $tariffRate = (float) (Tariff::query()->value('rate') ?? 0);

        $timezone = config('app.timezone', 'UTC');
        $now = now($timezone);

        $rangeStart = $startDate
            ? Carbon::parse($startDate, $timezone)->startOfDay()
            : $now->copy()->startOfMonth();

        $rangeEnd = $endDate
            ? Carbon::parse($endDate, $timezone)->endOfDay()
            : $now->copy();

        if ($rangeStart->greaterThan($rangeEnd)) {
            [$rangeStart, $rangeEnd] = [$rangeEnd, $rangeStart];
        }

        $windowDays = $rangeStart->diffInDays($rangeEnd) + 1;
        $previousStart = $rangeStart->copy()->subDays($windowDays);
        $previousEnd = $rangeStart->copy()->subDay();

        $baseReadings = Reading::query()
            ->join('meters', 'meters.id', '=', 'readings.meter_id');

        if ($buildingId) {
            $baseReadings->where('meters.building_id', $buildingId);
        }

        $sumInRange = function (Carbon $start, Carbon $end) use ($baseReadings): float {
            return (float) (clone $baseReadings)
                ->whereBetween('readings.recorded_at', [$start, $end])
                ->sum('readings.kwh');
        };

        $avgPfInRange = function (Carbon $start, Carbon $end) use ($baseReadings): ?float {
            return (clone $baseReadings)
                ->whereBetween('readings.recorded_at', [$start, $end])
                ->whereNotNull('readings.power_factor')
                ->avg('readings.power_factor');
        };

        $summaryKwh = $sumInRange($rangeStart, $rangeEnd);
        $summaryPrevKwh = $sumInRange($previousStart, $previousEnd);
        $summaryAvgPf = $avgPfInRange($rangeStart, $rangeEnd) ?? 0;

        $summary = [
            'this_month_kwh' => round($summaryKwh, 3),
            'last_month_kwh' => round($summaryPrevKwh, 3),
            'avg_pf' => round((float) $summaryAvgPf, 4),
        ];
        $summary['total_cost'] = round($summary['this_month_kwh'] * $tariffRate, 2);

        $buildingQuery = Building::query()
            ->select('id', 'code', 'name')
            ->orderBy('code');

        if ($buildingId) {
            $buildingQuery->where('id', $buildingId);
        }

        $perBuildingCurrent = (clone $baseReadings)
            ->whereBetween('readings.recorded_at', [$rangeStart, $rangeEnd])
            ->groupBy('meters.building_id')
            ->selectRaw('meters.building_id as building_id, COALESCE(SUM(readings.kwh), 0) as total_kwh, AVG(readings.power_factor) as avg_pf')
            ->get()
            ->keyBy('building_id');

        $perBuildingPrevious = (clone $baseReadings)
            ->whereBetween('readings.recorded_at', [$previousStart, $previousEnd])
            ->groupBy('meters.building_id')
            ->selectRaw('meters.building_id as building_id, COALESCE(SUM(readings.kwh), 0) as total_kwh')
            ->get()
            ->keyBy('building_id');

        $buildingSeries = $buildingQuery
            ->get()
            ->map(function (Building $building) use ($tariffRate, $perBuildingCurrent, $perBuildingPrevious) {
                $current = $perBuildingCurrent->get($building->id);
                $previous = $perBuildingPrevious->get($building->id);

                $thisRange = $current ? (float) $current->total_kwh : 0;
                $lastRange = $previous ? (float) $previous->total_kwh : 0;
                $avgPf = $current && $current->avg_pf !== null ? (float) $current->avg_pf : null;
                $cost = $thisRange * $tariffRate;

                return [
                    'id' => $building->id,
                    'code' => $building->code,
                    'name' => $building->name,
                    'this_month_kwh' => round($thisRange, 3),
                    'last_month_kwh' => round($lastRange, 3),
                    'cost' => round($cost, 2),
                    'avg_pf' => $avgPf !== null ? round($avgPf, 4) : null,
                ];
            })
            ->values()
            ->all();

        $trendSeries = self::buildTrendSeries($baseReadings, $rangeStart, $rangeEnd, $timezone);

        return [
            'rate' => $tariffRate,
            'summary' => $summary,
            'buildings' => $buildingSeries,
            'trend' => $trendSeries,
        ];
    }

    private static function buildTrendSeries($baseReadings, Carbon $start, Carbon $end, string $timezone): array
    {
        return (clone $baseReadings)
            ->whereBetween('readings.recorded_at', [$start, $end])
            ->selectRaw('DATE(readings.recorded_at) as bucket, COALESCE(SUM(readings.kwh), 0) as total_kwh')
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->map(function ($row) use ($timezone) {
                $labelDate = Carbon::parse($row->bucket, $timezone);

                return [
                    'label' => $labelDate->format('M d'),
                    'kwh' => round((float) $row->total_kwh, 3),
                ];
            })
            ->values()
            ->all();
    }
}
