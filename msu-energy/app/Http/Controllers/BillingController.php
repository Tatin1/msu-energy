<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Support\BillingSnapshot;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function indexApi(Request $request)
    {
        $buildingId = $request->integer('building_id');
        $start = $request->query('start');
        $end = $request->query('end');

        $snapshot = BillingSnapshot::build($buildingId ?: null, $start, $end);

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

    public function store(Request $request)
    {
        $buildingId = $request->integer('building_id');
        $start = $request->query('start');
        $end = $request->query('end');

        $snapshot = BillingSnapshot::build($buildingId ?: null, $start, $end);

        $saved = collect($snapshot['buildings'] ?? [])->map(function (array $building) use ($snapshot) {
            $record = Billing::updateOrCreate(
                ['building_id' => $building['id']],
                [
                    'this_month_kwh' => $building['this_month_kwh'] ?? 0,
                    'last_month_kwh' => $building['last_month_kwh'] ?? 0,
                    'total_bill' => $building['cost'] ?? 0,
                ]
            );

            return [
                'id' => $record->id,
                'building_id' => $record->building_id,
                'this_month_kwh' => (float) $record->this_month_kwh,
                'last_month_kwh' => (float) $record->last_month_kwh,
                'total_bill' => (float) $record->total_bill,
            ];
        });

        return response()->json([
            'message' => 'Billing snapshot saved.',
            'summary' => $snapshot['summary'],
            'saved' => $saved,
        ]);
    }
}
