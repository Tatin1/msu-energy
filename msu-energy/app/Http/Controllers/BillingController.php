<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function indexApi() {
    $rate = \App\Models\Tariff::latest()->value('rate') ?? 12.00;
    $bills = \App\Models\Building::with('billing')->get()->map(function($b) use($rate){
        $last = $b->billing->last_month_kwh ?? 0;
        $thism = $b->billing->this_month_kwh ?? 0;
        return [
            'id'=>$b->id,'code'=>$b->code,'name'=>$b->name,
            'lastMonth'=>$last,'thisMonth'=>$thism,'bill'=>($thism * $rate)
        ];
    });
    return response()->json(['rate'=>$rate,'bills'=>$bills]);
}

}
