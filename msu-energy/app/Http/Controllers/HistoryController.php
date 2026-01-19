<?php

namespace App\Http\Controllers;

use App\Models\BuildingLog;
use App\Models\SystemLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function buildingLogs(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'building' => 'nullable|string|max:120',
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:200',
        ]);

        $perPage = $validated['per_page'] ?? 50;

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

        $paginator = $query->paginate($perPage);

        $data = collect($paginator->items())->map(function (BuildingLog $log) {
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
                'pf1' => $log->pf1,
                'pf2' => $log->pf2,
                'pf3' => $log->pf3,
                'kwh' => $log->kwh,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ]);
    }

    public function systemLogs(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'building' => 'nullable|string|max:120',
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:200',
        ]);

        $perPage = $validated['per_page'] ?? 50;

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

        $paginator = $query->paginate($perPage);

        $data = collect($paginator->items())->map(function (SystemLog $log) {
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
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ]);
    }
}
