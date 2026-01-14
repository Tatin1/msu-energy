<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Reading;
use App\Models\SystemLog;
use App\Models\TransformerLog;
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

    // Other existing pages (map, graphs, etc.)
    public function map() { return view('pages.map'); }
    public function parameters() { return view('pages.parameters'); }
    public function billing() { return view('pages.billing'); }
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
    public function history() { return view('pages.history'); }
    public function options() { return view('pages.options'); }
    public function view() { return view('pages.view'); }
    public function help() { return view('pages.help'); }
    public function about() { return view('pages.about'); }
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
