<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\TransformerLog;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    public function parameters($buildingCode) {
        $building = Building::where('code', $buildingCode)
            ->with('meters.readings')
            ->firstOrFail();

        $meterIds = $building->meters->pluck('id')->filter()->values();
        $latestTransformer = null;

        if ($meterIds->isNotEmpty()) {
            $latestTransformer = TransformerLog::query()
                ->whereIn('meter_id', $meterIds)
                ->whereNotNull('recorded_at')
                ->orderByDesc('recorded_at')
                ->first();
        }

        $payload = $building->toArray();
        $payload['transformer_latest'] = $latestTransformer ? [
            'recorded_at' => optional($latestTransformer->recorded_at)->toIso8601String(),
            'frequency' => $latestTransformer->frequency,
        ] : null;

        return response()->json($payload);
    }
    public function meterReadings($meterId) {
        return response()->json(\App\Models\Reading::where('meter_id',$meterId)->orderBy('recorded_at','desc')->limit(50)->get());
    }

}
