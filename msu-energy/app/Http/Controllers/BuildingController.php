<?php

namespace App\Http\Controllers;

use App\Support\BuildingStatusFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index(): JsonResponse
    {
        // return response()->json(\App\Models\Building::all());
        return response()->json(BuildingStatusFormatter::summaries());
    }

}
