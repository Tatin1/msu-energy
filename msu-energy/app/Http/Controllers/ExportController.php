<?php

namespace App\Http\Controllers;

use App\Models\Reading;
use App\Models\SystemLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    /**
     * Export Building Data
     */
    public function exportBuilding(Request $request)
    {
        $filename = "building_data_" . now()->format('Y_m_d_His') . ".csv";
        $headers = ['Content-Type' => 'text/csv'];

        $validated = $request->validate([
            'building' => 'nullable|string|max:120',
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_rows' => 'nullable|integer|min:1|max:5000',
        ]);

        $maxRows = $validated['max_rows'] ?? 1000;

        $query = Reading::query()
            ->with(['meter.building:id,code'])
            ->whereNotNull('time')
            ->orderByDesc('time');

        if (!empty($validated['building'])) {
            $query->whereHas('meter.building', function ($buildingQuery) use ($validated) {
                $buildingQuery->where('code', $validated['building']);
            });
        }

        if (!empty($validated['date'])) {
            $query->whereDate('time', Carbon::parse($validated['date'])->toDateString());
        } else {
            if (!empty($validated['start_date'])) {
                $query->whereDate('time', '>=', Carbon::parse($validated['start_date'])->toDateString());
            }
            if (!empty($validated['end_date'])) {
                $query->whereDate('time', '<=', Carbon::parse($validated['end_date'])->toDateString());
            }
        }

        $buildingData = $query->limit($maxRows)->get();

        if ($buildingData->isEmpty()) {
            $buildingData = collect();
        }

        $rows = $buildingData->map(function ($row) {
            if ($row instanceof Reading) {
                $time = $row->time ? Carbon::parse($row->time) : null;
                $timeEnd = $row->time_end ? Carbon::parse($row->time_end) : $time?->copy()->addMinutes(15);

                return [
                    'id' => $row->id,
                    'meter' => $row->meter?->meter_code,
                    'date' => $time?->toDateString(),
                    'time' => $time?->format('H:i:s'),
                    'time_end' => $timeEnd?->format('H:i:s'),
                    'time_ed' => $timeEnd?->format('H:i:s'),
                    'f' => $row->f,
                    'v1' => $row->v1,
                    'v2' => $row->v2,
                    'v3' => $row->v3,
                    'a1' => $row->a1,
                    'a2' => $row->a2,
                    'a3' => $row->a3,
                    'kw1' => $row->kw1,
                    'kw2' => $row->kw2,
                    'kw3' => $row->kw3,
                    'pf1' => $row->pf1,
                    'pf2' => $row->pf2,
                    'pf3' => $row->pf3,
                    'kwiii' => $row->kwiii,
                    'kvaiii' => $row->kvaiii,
                    'kvariii' => $row->kvariii,
                    'pfiii' => $row->pfiii,
                    'kwhiii' => $row->kwhiii,
                    'cost' => $row->cost,
                ];
            }

            $payload = (array) $row;

            return [
                'id' => $payload['id'] ?? null,
                'meter' => $payload['meter'] ?? null,
                'date' => $payload['date'] ?? null,
                'time' => $payload['time'] ?? null,
                'time_end' => $payload['time_end'] ?? null,
                'time_ed' => $payload['time_ed'] ?? null,
                'f' => $payload['f'] ?? null,
                'v1' => $payload['v1'] ?? null,
                'v2' => $payload['v2'] ?? null,
                'v3' => $payload['v3'] ?? null,
                'a1' => $payload['a1'] ?? null,
                'a2' => $payload['a2'] ?? null,
                'a3' => $payload['a3'] ?? null,
                'kw1' => $payload['kw1'] ?? null,
                'kw2' => $payload['kw2'] ?? null,
                'kw3' => $payload['kw3'] ?? null,
                'pf1' => $payload['pf1'] ?? null,
                'pf2' => $payload['pf2'] ?? null,
                'pf3' => $payload['pf3'] ?? null,
                'kwiii' => $payload['kwiii'] ?? null,
                'kvaiii' => $payload['kvaiii'] ?? null,
                'kvariii' => $payload['kvariii'] ?? null,
                'pfiii' => $payload['pfiii'] ?? null,
                'kwhiii' => $payload['kwhiii'] ?? ($payload['kwh'] ?? null),
                'cost' => $payload['cost'] ?? null,
            ];
        });

        // === Stream CSV ===
        $callback = function() use ($rows) {
            $file = fopen('php://output', 'w');
            $this->writeCsvRow($file, ['ID','METER','DATE','TIME','TIMEed','F','V1','V2','V3','A1','A2','A3','KW1','KW2','KW3','PF1','PF2','PF3','KWIII','KVAIII','KVARIII','PFIII','KWHIII','COST']);
            foreach ($rows as $row) {
                $this->writeCsvRow($file, [
                    $row['id'],
                    $row['meter'],
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
                    $row['kw1'],
                    $row['kw2'],
                    $row['kw3'],
                    $row['pf1'],
                    $row['pf2'],
                    $row['pf3'],
                    $row['kwiii'],
                    $row['kvaiii'],
                    $row['kvariii'],
                    $row['pfiii'],
                    $row['kwhiii'] ?? null,
                    $row['cost'],
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, array_merge($headers, [
            "Content-Disposition" => "attachment; filename=$filename"
        ]));
    }


    /**
     * Export System Data
     */
    public function exportSystem(Request $request)
    {
        $filename = "system_data_" . now()->format('Y_m_d_His') . ".csv";
        $headers = ['Content-Type' => 'text/csv'];

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
            $systemData = collect();
        }

        $rows = $systemData->map(function ($row) {
            $payload = $row instanceof SystemLog ? $row->toArray() : (array) $row;

            return [
                'id' => $payload['id'] ?? null,
                'building' => $payload['building'] ?? null,
                'meter' => $payload['meter'] ?? ($payload['building'] ?? null),
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
            $this->writeCsvRow($file, ['ID','METER','DATE','TIME','TIMEed','TOTAL_KW','TOTAL_KVAR','TOTAL_KVA','TOTAL_PF']);
            foreach ($rows as $row) {
                $this->writeCsvRow($file, [
                    $row['id'],
                    $row['meter'],
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

    private function writeCsvRow($file, array $row): void
    {
        fputcsv($file, array_map(fn ($value) => $this->sanitizeCsvCell($value), $row));
    }

    private function sanitizeCsvCell($value)
    {
        if ($value === null) {
            return null;
        }

        $stringValue = (string) $value;
        if ($stringValue !== '' && preg_match('/^[=+\-@]/', $stringValue) === 1) {
            return "'".$stringValue;
        }

        return $value;
    }
}
