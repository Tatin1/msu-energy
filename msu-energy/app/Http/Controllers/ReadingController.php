<?php

namespace App\Http\Controllers;

use App\Models\Building;

class ReadingController extends Controller
{
    public function parameters($buildingCode) {
        $building = Building::where('code', $buildingCode)
            ->with('meters.readings')
            ->firstOrFail();

        $payload = $building->toArray();

        return response()->json($payload);
    }
    public function meterReadings($meterId) {
        return response()->json(\App\Models\Reading::where('meter_id',$meterId)->orderBy('recorded_at','desc')->limit(50)->get());
    }

}
