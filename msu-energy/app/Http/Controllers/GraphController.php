<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GraphController extends Controller
{
    public function daily($meterId, $param, $date=null) {
        $date = $date ?: date('Y-m-d');
        $data = \App\Models\Reading::where('meter_id',$meterId)->whereDate('recorded_at',$date)->orderBy('recorded_at')->pluck($param);
        return response()->json($data);
    }

}
