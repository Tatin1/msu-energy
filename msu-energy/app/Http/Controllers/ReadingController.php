<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReadingController extends Controller
{
    public function parameters($buildingCode) {
        $building = \App\Models\Building::where('code',$buildingCode)->with('meters.readings')->firstOrFail();
        return response()->json($building);
    }
    public function meterReadings($meterId) {
        return response()->json(\App\Models\Reading::where('meter_id',$meterId)->orderBy('recorded_at','desc')->limit(50)->get());
    }

}
