<?php

namespace App\Support;

use App\Models\BuildingLog;
use App\Models\Reading;
use App\Models\SystemLog;
use App\Models\TransformerLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RealtimePayloads
{
    public static function dashboardMetrics(): array
    {
        $now = now();
        $currentStart = $now->copy()->startOfMonth();
        $previousStart = $currentStart->copy()->subMonth();
        $previousEnd = $currentStart->copy()->subSecond();

        $currentTotals = self::buildingEnergyTotals($currentStart, $now);
        $previousTotals = self::buildingEnergyTotals($previousStart, $previousEnd)->keyBy('label');

        $labels = $currentTotals->pluck('label')->toArray();
        $values = $currentTotals->pluck('total_kwh')->map(fn ($value) => round($value, 3))->toArray();
        $prevValues = array_map(
            fn ($label) => round($previousTotals->get($label)->total_kwh ?? 0, 3),
            $labels
        );

        $fifteenMinutesAgo = $now->copy()->subMinutes(15);
        $totalPower = (float) Reading::query()
            ->whereNotNull('active_power')
            ->whereBetween('recorded_at', [$fifteenMinutesAgo, $now])
            ->sum('active_power');

        $avgPF = (float) Reading::query()
            ->whereNotNull('power_factor')
            ->avg('power_factor');

        $lastMonthKwh = (float) Reading::query()
            ->whereBetween('recorded_at', [$previousStart, $previousEnd])
            ->sum('kwh');

        $thisMonthKwh = (float) Reading::query()
            ->whereBetween('recorded_at', [$currentStart, $now])
            ->sum('kwh');

        return [
            'chart' => [
                'labels' => $labels,
                'current' => $values,
                'previous' => $prevValues,
            ],
            'totals' => [
                'total_power' => round($totalPower, 3),
                'avg_pf' => round($avgPF, 4),
                'this_month_kwh' => round($thisMonthKwh, 3),
                'last_month_kwh' => round($lastMonthKwh, 3),
            ],
            'building_status' => BuildingStatusFormatter::summaries(),
            'generated_at' => $now->toIso8601String(),
        ];
    }

    public static function transformerTable(): array
    {
        return self::transformerTableRows()->toArray();
    }

    public static function systemLogTable(): array
    {
        return self::systemLogTableRows()->toArray();
    }

    public static function buildingLogTable(): array
    {
        $timezone = config('app.timezone');

        return BuildingLog::query()
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->limit(15)
            ->get()
            ->map(function (BuildingLog $log) use ($timezone) {
                $timestamp = null;

                if ($log->date) {
                    $timestamp = Carbon::parse(trim($log->date.' '.($log->time ?? '00:00:00')))
                        ->timezone($timezone)
                        ->format('Y-m-d H:i');
                }

                return [
                    'id' => $log->id,
                    'building' => $log->building,
                    'date' => $log->date,
                    'time' => $log->time,
                    'time_ed' => $log->time_ed,
                    'f' => $log->f,
                    'v1' => $log->v1,
                    'v2' => $log->v2,
                    'v3' => $log->v3,
                    'a1' => $log->a1,
                    'a2' => $log->a2,
                    'a3' => $log->a3,
                    'kwh' => $log->kwh,
                    'pf1' => $log->pf1,
                    'pf2' => $log->pf2,
                    'pf3' => $log->pf3,
                    'timestamp' => $timestamp,
                ];
            })
            ->toArray();
    }

    private static function buildingEnergyTotals(Carbon $start, Carbon $end): Collection
    {
        return Reading::query()
            ->join('meters', 'meters.id', '=', 'readings.meter_id')
            ->join('buildings', 'buildings.id', '=', 'meters.building_id')
            ->selectRaw('buildings.code as label, COALESCE(SUM(readings.kwh), 0) as total_kwh')
            ->whereNotNull('readings.recorded_at')
            ->whereBetween('readings.recorded_at', [$start, $end])
            ->groupBy('buildings.code')
            ->orderByDesc('total_kwh')
            ->limit(5)
            ->get();
    }

    private static function transformerTableRows(): Collection
    {
        $timezone = config('app.timezone');

        return TransformerLog::query()
            ->orderByDesc('recorded_at')
            ->limit(15)
            ->get()
            ->map(function (TransformerLog $log) use ($timezone) {
                $voltages = collect([$log->v1, $log->v2, $log->v3])->filter(static fn ($value) => $value !== null);
                $voltage = $voltages->isEmpty() ? null : (float) $voltages->avg();

                return [
                    'label' => sprintf('TX-%03d', $log->id),
                    'voltage' => $voltage,
                    'load_kw' => $log->kwh !== null ? (float) $log->kwh : null,
                    'status' => self::transformerStatus($log->pf),
                    'timestamp' => $log->recorded_at
                        ? $log->recorded_at->timezone($timezone)->format('Y-m-d H:i')
                        : null,
                ];
            });
    }

    private static function transformerStatus(?float $pf): string
    {
        if ($pf === null) {
            return 'Unknown';
        }

        if ($pf < 0.8) {
            return 'Critical';
        }

        if ($pf < 0.9) {
            return 'Warning';
        }

        return 'Normal';
    }

    private static function systemLogTableRows(): Collection
    {
        $timezone = config('app.timezone');

        return SystemLog::query()
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->limit(15)
            ->get()
            ->map(function (SystemLog $log) use ($timezone) {
                $timestamp = null;

                if ($log->date) {
                    $timestamp = Carbon::parse(trim($log->date.' '.($log->time ?? '00:00:00')))
                        ->timezone($timezone)
                        ->format('Y-m-d H:i');
                }

                return [
                    'id' => $log->id,
                    'building' => $log->building,
                    'date' => $log->date,
                    'time' => $log->time,
                    'time_ed' => $log->time_ed,
                    'total_kw' => $log->total_kw,
                    'total_kvar' => $log->total_kvar,
                    'total_kva' => $log->total_kva,
                    'total_pf' => $log->total_pf,
                    'timestamp' => $timestamp,
                ];
            });
    }
}
