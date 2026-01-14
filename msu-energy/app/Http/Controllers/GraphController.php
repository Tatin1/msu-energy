<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\Reading;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GraphController extends Controller
{
    private array $parameters = [
        'active_power' => ['column' => 'active_power', 'label' => 'Total Active Power (kW)'],
        'reactive_power' => ['column' => 'reactive_power', 'label' => 'Total Reactive Power (kVAR)'],
        'apparent_power' => ['column' => 'apparent_power', 'label' => 'Total Apparent Power (kVA)'],
        'power_factor' => ['column' => 'power_factor', 'label' => 'Power Factor'],
        'voltage1' => ['column' => 'voltage1', 'label' => 'Voltage Phase A (V)'],
        'current1' => ['column' => 'current1', 'label' => 'Current Phase A (A)'],
    ];

    public function daily($meterId, $param, $date = null)
    {
        // $data = \App\Models\Reading::where('meter_id',$meterId)->whereDate('recorded_at',$date)->orderBy('recorded_at')->pluck($param);

        $meter = Meter::findOrFail($meterId);

        if (! isset($this->parameters[$param])) {
            abort(404, 'Unsupported parameter');
        }

        $column = $this->parameters[$param]['column'];
        $label = $this->parameters[$param]['label'];

        $targetDate = $date ? Carbon::parse($date, config('app.timezone')) : now();

        $readings = Reading::query()
            ->where('meter_id', $meter->id)
            ->whereDate('recorded_at', $targetDate->toDateString())
            ->whereNotNull($column)
            ->orderBy('recorded_at')
            ->get(['recorded_at', $column]);

        $response = [
            'labels' => $readings->map(fn ($row) => optional($row->recorded_at)->timezone(config('app.timezone'))->format('H:i'))->toArray(),
            'values' => $readings->map(fn ($row) => $row->{$column} !== null ? (float) $row->{$column} : null)->toArray(),
            'parameterLabel' => $label,
            'meter' => [
                'id' => $meter->id,
                'label' => $meter->label,
                'code' => $meter->meter_code,
            ],
        ];

        return response()->json($response);
    }
}
