<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\BuildingLog;
use App\Models\Reading;
use App\Models\SystemLog;
use App\Models\Tariff;
use App\Models\TransformerLog;
use App\Support\BillingSnapshot;
use App\Support\BuildingStatusFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function home()
    {
        /*
         * $labels = ['COE', 'SET', 'CSM', 'CCS', 'PRISM'];
         * $values = [80, 60, 50, 70, 100];
         * $totalPower = 300000;
         * $avgPF = 0.9423;
         * $lastMonthKwh = 350160;
         * $thisMonthKwh = 352512;
         */

        $now = now();
        $currentStart = $now->copy()->startOfMonth();
        $previousStart = $currentStart->copy()->subMonth();
        $previousEnd = $currentStart->copy()->subSecond();

        $currentTotals = $this->buildingEnergyTotals($currentStart, $now);
        $previousTotals = $this->buildingEnergyTotals($previousStart, $previousEnd)
            ->keyBy('label');

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

        $lastMonthkW = $lastMonthKwh;
        $thisMonthkW = $thisMonthKwh;

        return view('pages.home', compact(
            'labels',
            'values',
            'prevValues',
            'totalPower',
            'avgPF',
            'lastMonthKwh',
            'thisMonthKwh',
            'lastMonthkW',
            'thisMonthkW'
        ));
    }

    public function apiDashboard()
    {
        $now = now();
        $currentStart = $now->copy()->startOfMonth();
        $previousStart = $currentStart->copy()->subMonth();
        $previousEnd = $currentStart->copy()->subSecond();

        $currentTotals = $this->buildingEnergyTotals($currentStart, $now);
        $previousTotals = $this->buildingEnergyTotals($previousStart, $previousEnd)
            ->keyBy('label');

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

        $buildings = collect($labels)->map(function ($label, $index) use ($values, $prevValues) {
            $current = $values[$index] ?? 0;
            $previous = $prevValues[$index] ?? 0;

            return [
                'code' => $label,
                'this_month_kwh' => $current,
                'last_month_kwh' => $previous,
                'delta_kwh' => round($current - $previous, 3),
            ];
        })->values();

        return response()->json([
            'totals' => [
                'total_power' => round($totalPower, 3),
                'avg_pf' => round($avgPF, 4),
                'this_month_kwh' => round($thisMonthKwh, 3),
                'last_month_kwh' => round($lastMonthKwh, 3),
            ],
            'buildings' => $buildings,
            'generated_at' => $now->toIso8601String(),
            'window' => [
                'current_start' => $currentStart->toIso8601String(),
                'current_end' => $now->toIso8601String(),
                'previous_start' => $previousStart->toIso8601String(),
                'previous_end' => $previousEnd->toIso8601String(),
            ],
        ]);
    }

    // Other existing pages (map, graphs, etc.)
    public function map()
    {
        // return view('pages.map');

        $buildingStatus = BuildingStatusFormatter::summaries();
        $buildingBootstrap = $buildingStatus->map(fn (array $building) => [
            'code' => $building['code'],
            'name' => $building['name'],
            'status' => $building['status'],
            'status_reason' => $building['status_reason'],
            'latest_reading_at' => $building['latest_reading_at'],
        ]);

        return view('pages.map', [
            'buildingStatus' => $buildingStatus,
            'buildingBootstrap' => $buildingBootstrap,
        ]);
    }
    public function parameters()
    {
        // return view('pages.parameters');

        $buildings = Building::query()
            ->select('id', 'code', 'name')
            ->orderBy('code')
            ->get()
            ->map(fn (Building $building) => [
                'id' => $building->id,
                'code' => $building->code,
                'name' => $building->name,
            ]);

        return view('pages.parameters', [
            'parameterBuildings' => $buildings,
        ]);
    }
    public function billing()
    {
        // return view('pages.billing');
        $snapshot = BillingSnapshot::build();

        $buildings = Building::query()
            ->select('id', 'code', 'name')
            ->orderBy('code')
            ->get()
            ->map(fn (Building $building) => [
                'id' => $building->id,
                'code' => $building->code,
                'name' => $building->name,
            ]);

        return view('pages.billing', [
            'summary' => $snapshot['summary'],
            'buildings' => $buildings,
            'chartConfig' => [
                'buildings' => $snapshot['buildings'],
                'trend' => $snapshot['trend'],
            ],
        ]);
    }
    public function tables()
    {
        // return view('pages.tables');

        $transformerRows = $this->transformerTableRows();
        $systemLogRows = $this->systemLogTableRows();

        return view('pages.tables', compact('transformerRows', 'systemLogRows'));
    }
    public function graphs()
    {
        // return view('pages.graphs');

        $buildings = Building::query()
            ->with(['meters:id,building_id,label,meter_code'])
            ->select('id', 'code', 'name')
            ->orderBy('code')
            ->get();

        $parameters = [
            ['key' => 'active_power', 'label' => 'Total Active Power (kW)'],
            ['key' => 'reactive_power', 'label' => 'Total Reactive Power (kVAR)'],
            ['key' => 'apparent_power', 'label' => 'Total Apparent Power (kVA)'],
            ['key' => 'power_factor', 'label' => 'Power Factor'],
            ['key' => 'voltage1', 'label' => 'Voltage Phase A (V)'],
            ['key' => 'current1', 'label' => 'Current Phase A (A)'],
        ];

        return view('pages.graphs', compact('buildings', 'parameters'));
    }
    public function history()
    {
        // return view('pages.history');

        $buildings = Building::query()
            ->select('id', 'code', 'name')
            ->orderBy('code')
            ->get()
            ->map(fn (Building $building) => [
                'id' => $building->id,
                'code' => $building->code,
                'name' => $building->name,
            ]);

        $buildingLogs = BuildingLog::query()
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->limit(50)
            ->get();

        $systemLogs = SystemLog::query()
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->limit(50)
            ->get();

        return view('pages.history', [
            'historyBuildings' => $buildings,
            'historyBuildingLogs' => $buildingLogs,
            'historySystemLogs' => $systemLogs,
        ]);
    }
    public function options() { return view('pages.options'); }
    public function view()
    {
        $datasets = $this->viewDatasets();

        return view('pages.view', $datasets);
    }
    public function apiView()
    {
        return response()->json($this->viewDatasets());
    }
    public function help() { return view('pages.help'); }
    public function about() { return view('pages.about'); }
    private function viewDatasets(): array
    {
        $now = now();
        $timezone = config('app.timezone', 'UTC');

        $realtimeStart = $now->copy()->subHours(23)->startOfHour();
        $realtimeReadings = Reading::query()
            ->select('active_power', 'recorded_at')
            ->whereNotNull('recorded_at')
            ->whereNotNull('active_power')
            ->whereBetween('recorded_at', [$realtimeStart, $now])
            ->orderBy('recorded_at')
            ->get();

        $realtimeBuckets = $realtimeReadings->groupBy(function (Reading $reading) use ($timezone) {
            if ($reading->recorded_at === null) {
                return null;
            }

            if (is_string($reading->recorded_at)) {
                $reading->recorded_at = \Carbon\Carbon::parse($reading->recorded_at);
            }

            return $reading->recorded_at
                ->copy()
                ->timezone($timezone)
                ->format('Y-m-d H:00:00');
        })->filter();

        $realtimeSeries = collect(range(0, 23))->map(function (int $offset) use ($realtimeStart, $realtimeBuckets, $timezone) {
            $bucketStart = $realtimeStart->copy()->addHours($offset)->timezone($timezone);
            $key = $bucketStart->format('Y-m-d H:00:00');

            $total = isset($realtimeBuckets[$key])
                ? (float) $realtimeBuckets[$key]->sum('active_power')
                : 0;

            return [
                'label' => $bucketStart->format('g A'),
                'total_kw' => round($total, 3),
            ];
        })->values()->all();

        $tariffRate = (float) (Tariff::query()->value('rate') ?? 0);
        $billingSeries = Building::query()
            ->select('id', 'code', 'name')
            ->with(['billing:id,building_id,this_month_kwh,last_month_kwh,total_bill'])
            ->orderBy('code')
            ->get()
            ->map(function (Building $building) use ($tariffRate) {
                $billing = $building->billing;
                $thisMonth = (float) ($billing->this_month_kwh ?? 0);
                $cost = $billing && $billing->total_bill !== null
                    ? (float) $billing->total_bill
                    : round($thisMonth * $tariffRate, 2);

                return [
                    'label' => $building->code,
                    'name' => $building->name,
                    'this_month_kwh' => round($thisMonth, 3),
                    'cost' => round($cost, 2),
                ];
            })
            ->filter(fn (array $series) => $series['this_month_kwh'] > 0 || $series['cost'] > 0)
            ->values()
            ->all();

        $loadWindowStart = $now->copy()->subDay();
        $rawLoadSeries = Reading::query()
            ->join('meters', 'meters.id', '=', 'readings.meter_id')
            ->join('buildings', 'buildings.id', '=', 'meters.building_id')
            ->selectRaw('buildings.code as label, COALESCE(SUM(readings.active_power), 0) as total_kw')
            ->whereNotNull('readings.recorded_at')
            ->whereBetween('readings.recorded_at', [$loadWindowStart, $now])
            ->groupBy('buildings.code')
            ->orderByDesc('total_kw')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->label,
                'total_kw' => round((float) $row->total_kw, 3),
            ]);

        $loadTotal = (float) $rawLoadSeries->sum('total_kw');
        $loadSeries = $rawLoadSeries->map(function (array $row) use ($loadTotal) {
            $percentage = $loadTotal > 0 ? round(($row['total_kw'] / $loadTotal) * 100, 2) : 0;

            return [
                'label' => $row['label'],
                'total_kw' => $row['total_kw'],
                'percentage' => $percentage,
            ];
        })->values()->all();

        $viewSummary = [
            'realtime_window' => [
                'start' => $realtimeStart->toIso8601String(),
                'end' => $now->toIso8601String(),
            ],
            'billing_period' => $now->copy()->startOfMonth()->format('F Y'),
            'load_window' => [
                'start' => $loadWindowStart->toIso8601String(),
                'end' => $now->toIso8601String(),
            ],
        ];

        return [
            'realtimeSeries' => $realtimeSeries,
            'billingSeries' => $billingSeries,
            'loadSeries' => $loadSeries,
            'viewSummary' => $viewSummary,
        ];
    }
    private function buildingEnergyTotals(Carbon $start, Carbon $end): Collection
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

    private function transformerTableRows(): Collection
    {
        return TransformerLog::query()
            ->orderByDesc('recorded_at')
            ->limit(15)
            ->get()
            ->map(function (TransformerLog $log) {
                $voltages = collect([$log->v1, $log->v2, $log->v3])->filter(static fn ($value) => $value !== null);
                $voltage = $voltages->isEmpty() ? null : (float) $voltages->avg();

                return [
                    'label' => sprintf('TX-%03d', $log->id),
                    'voltage' => $voltage,
                    'load_kw' => $log->kwh !== null ? (float) $log->kwh : null,
                    'status' => $this->transformerStatus($log->pf),
                    'timestamp' => $log->recorded_at
                        ? $log->recorded_at->timezone(config('app.timezone'))->format('Y-m-d H:i')
                        : null,
                ];
            });
    }

    private function transformerStatus(?float $pf): string
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

    private function systemLogTableRows(): Collection
    {
        return SystemLog::query()
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->limit(15)
            ->get()
            ->map(function (SystemLog $log) {
                $timestamp = null;

                if ($log->date) {
                    $timestamp = Carbon::parse(trim($log->date.' '.($log->time ?? '00:00:00')))
                        ->timezone(config('app.timezone'))
                        ->format('Y-m-d H:i');
                }

                return [
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
