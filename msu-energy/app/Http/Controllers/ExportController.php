<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Export Building Data (IoT-ready + dummy fallback)
     */
    public function exportBuilding(Request $request)
    {
        $filename = "building_data_" . now()->format('Y_m_d_His') . ".csv";
        $headers = ['Content-Type' => 'text/csv'];

        // === IoT-Ready Query Section ===
        // Once connected, replace 'dummy' with your actual table name, e.g. 'building_logs'
        try {
            $buildingData = DB::table('building_logs')
                ->select('id', 'date', 'time', 'time_ed', 'f', 'v1', 'v2', 'v3', 'a1', 'a2', 'a3', 'pf1', 'pf2', 'pf3', 'kwh')
                ->whereDate('date', Carbon::today()) // example filter
                ->get();
        } catch (\Exception $e) {
            // If IoT DB or table doesn't exist yet, fallback to dummy
            $buildingData = collect([]);
        }

        // === Dummy Data Fallback ===
        if ($buildingData->isEmpty()) {
            $buildingData = collect([
                (object)[
                    'id' => 1,
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
                ]
            ]);
        }

        // === Stream CSV ===
        $callback = function() use ($buildingData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID','DATE','TIME','TIMEed','F','V1','V2','V3','A1','A2','A3','PF1','PF2','PF3','kWh']);
            foreach ($buildingData as $row) {
                fputcsv($file, (array)$row);
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

        // === IoT-Ready Query Section ===
        // Replace with your real IoT table later, e.g. 'system_logs'
        try {
            $systemData = DB::table('system_logs')
                ->select('id', 'date', 'time', 'time_ed', 'total_kw', 'total_kvar', 'total_kva', 'total_pf')
                ->whereDate('date', Carbon::today())
                ->get();
        } catch (\Exception $e) {
            $systemData = collect([]);
        }

        // === Dummy Data Fallback ===
        if ($systemData->isEmpty()) {
            $systemData = collect([
                (object)[
                    'id' => 1,
                    'date' => '2025-10-25',
                    'time' => '08:15',
                    'time_ed' => '08:30',
                    'total_kw' => 420,
                    'total_kvar' => 180,
                    'total_kva' => 460,
                    'total_pf' => 0.92,
                ]
            ]);
        }

        // === Stream CSV ===
        $callback = function() use ($systemData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID','DATE','TIME','TIMEed','TOTAL_KW','TOTAL_KVAR','TOTAL_KVA','TOTAL_PF']);
            foreach ($systemData as $row) {
                fputcsv($file, (array)$row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, array_merge($headers, [
            "Content-Disposition" => "attachment; filename=$filename"
        ]));
    }
}
