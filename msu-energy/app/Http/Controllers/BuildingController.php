<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index() { return response()->json(\App\Models\Building::all()); }

}
