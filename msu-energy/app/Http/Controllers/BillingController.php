<?php

namespace App\Http\Controllers;

use App\Support\BillingSnapshot;

class BillingController extends Controller
{
    public function indexApi()
    {
        $snapshot = BillingSnapshot::build();

        $legacyBills = collect($snapshot['buildings'] ?? [])->map(function (array $building) {
            return [
                'id' => $building['id'],
                'code' => $building['code'],
                'name' => $building['name'],
                'lastMonth' => $building['last_month_kwh'] ?? 0,
                'thisMonth' => $building['this_month_kwh'] ?? 0,
                'bill' => $building['cost'] ?? 0,
            ];
        })->values();

        return response()->json([
            'rate' => $snapshot['rate'],
            'summary' => $snapshot['summary'],
            'buildings' => $snapshot['buildings'],
            'trend' => $snapshot['trend'],
            'bills' => $legacyBills,
        ]);
    }
}
