<?php

namespace App\Http\Controllers;

use App\Models\BuildingLog;
use App\Models\SystemLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    /**
     * Export Building Data (IoT-ready + dummy fallback)
     */
    public function exportBuilding(Request $request)
    {
        $filename = "building_data_" . now()->format('Y_m_d_His') . ".csv";
        $headers = ['Content-Type' => 'text/csv'];

        /*
        try {
            $buildingData = DB::table('building_logs')
                ->select('id', 'date', 'time', 'time_ed', 'f', 'v1', 'v2', 'v3', 'a1', 'a2', 'a3', 'pf1', 'pf2', 'pf3', 'kwh')
                ->whereDate('date', Carbon::today())
                ->get();
        } catch (\Exception $e) {
            $buildingData = collect([]);
        }

        if ($buildingData->isEmpty()) {
            $buildingData = collect([
                (object)[]
            ]);
        }
        */

        $validated = $request->validate([
            'building' => 'nullable|string|max:120',
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_rows' => 'nullable|integer|min:1|max:5000',
        ]);

        $maxRows = $validated['max_rows'] ?? 1000;

        $query = BuildingLog::query()
            ->orderByDesc('date')
            ->orderByDesc('time');

        if (!empty($validated['building'])) {
            $query->where('building', $validated['building']);
        }

        if (!empty($validated['date'])) {
            $query->whereDate('date', Carbon::parse($validated['date'])->toDateString());
        } else {
            if (!empty($validated['start_date'])) {
                $query->whereDate('date', '>=', Carbon::parse($validated['start_date'])->toDateString());
            }
            if (!empty($validated['end_date'])) {
                $query->whereDate('date', '<=', Carbon::parse($validated['end_date'])->toDateString());
            }
        }

        $buildingData = $query->limit($maxRows)->get();

        if ($buildingData->isEmpty()) {
            $buildingData = collect([
                (object) [
                    'id' => 1,
                    'building' => 'SAMPLE',
                    'date' => '2025-10-25',
                    'time' => '08:15',
                    'time_ed' => '08:30',
                    'f' => 60,
                    'v1' => 230,
                    'v2' => 228,
                    'v3' => 231,
                    'a1' => 12.4,
                    'a2' => 11.8,
                    'a3' => 13.0,
                    'pf1' => 0.92,
                    'pf2' => 0.94,
                    'pf3' => 0.91,
                    'kwh' => 128.3,
                ],
            ]);
        }

        $rows = $buildingData->map(function ($row) {
            $payload = $row instanceof BuildingLog ? $row->toArray() : (array) $row;

            return [
                'id' => $payload['id'] ?? null,
                'building' => $payload['building'] ?? null,
                'date' => $payload['date'] ?? null,
                'time' => $payload['time'] ?? null,
                'time_ed' => $payload['time_ed'] ?? null,
                'f' => $payload['f'] ?? null,
                'v1' => $payload['v1'] ?? null,
                'v2' => $payload['v2'] ?? null,
                'v3' => $payload['v3'] ?? null,
                'a1' => $payload['a1'] ?? null,
                'a2' => $payload['a2'] ?? null,
                'a3' => $payload['a3'] ?? null,
                'pf1' => $payload['pf1'] ?? null,
                'pf2' => $payload['pf2'] ?? null,
                'pf3' => $payload['pf3'] ?? null,
                'kwh' => $payload['kwh'] ?? null,
            ];
        });

        // === Stream CSV ===
        $callback = function() use ($rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID','BUILDING','DATE','TIME','TIMEed','F','V1','V2','V3','A1','A2','A3','PF1','PF2','PF3','kWh']);
            foreach ($rows as $row) {
                fputcsv($file, [
                    $row['id'],
                    $row['building'],
                    $row['date'],
                    $row['time'],
                    $row['time_ed'],
                    $row['f'],
                    $row['v1'],
                    $row['v2'],
                    $row['v3'],
                    $row['a1'],
                    $row['a2'],
                    $row['a3'],
                    $row['pf1'],
                    $row['pf2'],
                    $row['pf3'],
                    $row['kwh'],
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, array_merge($headers, [
            "Content-Disposition" => "attachment; filename=$filename"
        ]));
    }


    /**
     * Export System Data (IoT-ready + dummy fallback)
     */
    public function exportSystem(Request $request)
    {
        $filename = "system_data_" . now()->format('Y_m_d_His') . ".csv";
        $headers = ['Content-Type' => 'text/csv'];

        /*
        try {
            $systemData = DB::table('system_logs')
                ->select('id', 'date', 'time', 'time_ed', 'total_kw', 'total_kvar', 'total_kva', 'total_pf')
                ->whereDate('date', Carbon::today())
                ->get();
        } catch (\Exception $e) {
            $systemData = collect([]);
        }

        if ($systemData->isEmpty()) {
            $systemData = collect([
                (object) []
            ]);
        }
        */

        $validated = $request->validate([
            'building' => 'nullable|string|max:120',
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_rows' => 'nullable|integer|min:1|max:5000',
        ]);

        $maxRows = $validated['max_rows'] ?? 1000;

        $query = SystemLog::query()
            ->orderByDesc('date')
            ->orderByDesc('time');

        if (!empty($validated['building'])) {
            $query->where('building', $validated['building']);
        }

        if (!empty($validated['date'])) {
            $query->whereDate('date', Carbon::parse($validated['date'])->toDateString());
        } else {
            if (!empty($validated['start_date'])) {
                $query->whereDate('date', '>=', Carbon::parse($validated['start_date'])->toDateString());
            }
            if (!empty($validated['end_date'])) {
                $query->whereDate('date', '<=', Carbon::parse($validated['end_date'])->toDateString());
            }
        }

        $systemData = $query->limit($maxRows)->get();

        if ($systemData->isEmpty()) {
            $systemData = collect([
                (object) [
                    'id' => 1,
                    'building' => 'SYSTEM',
                    'date' => '2025-10-25',
                    'time' => '08:15',
                    'time_ed' => '08:30',
                    'total_kw' => 420,
                    'total_kvar' => 180,
                    'total_kva' => 460,
                    'total_pf' => 0.92,
                ],
            ]);
        }

        $rows = $systemData->map(function ($row) {
            $payload = $row instanceof SystemLog ? $row->toArray() : (array) $row;

            return [
                'id' => $payload['id'] ?? null,
                'building' => $payload['building'] ?? null,
                'date' => $payload['date'] ?? null,
                'time' => $payload['time'] ?? null,
                'time_ed' => $payload['time_ed'] ?? null,
                'total_kw' => $payload['total_kw'] ?? null,
                'total_kvar' => $payload['total_kvar'] ?? null,
                'total_kva' => $payload['total_kva'] ?? null,
                'total_pf' => $payload['total_pf'] ?? null,
            ];
        });

        // === Stream CSV ===
        $callback = function() use ($rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID','BUILDING','DATE','TIME','TIMEed','TOTAL_KW','TOTAL_KVAR','TOTAL_KVA','TOTAL_PF']);
            foreach ($rows as $row) {
                fputcsv($file, [
                    $row['id'],
                    $row['building'],
                    $row['date'],
                    $row['time'],
                    $row['time_ed'],
                    $row['total_kw'],
                    $row['total_kvar'],
                    $row['total_kva'],
                    $row['total_pf'],
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, array_merge($headers, [
            "Content-Disposition" => "attachment; filename=$filename"
        ]));
    }
}
