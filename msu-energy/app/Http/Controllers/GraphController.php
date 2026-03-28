<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\Reading;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GraphController extends Controller
{
    private array $parameters = [
        'kwiii'   => ['column' => 'kwiii',   'label' => 'Total Active Power (kW)'],
        'kvariii' => ['column' => 'kvariii', 'label' => 'Total Reactive Power (kVAR)'],
        'kvaiii'  => ['column' => 'kvaiii',  'label' => 'Total Apparent Power (kVA)'],
        'pfiii'   => ['column' => 'pfiii',   'label' => 'Power Factor'],
        'v1'      => ['column' => 'v1',      'label' => 'Voltage Phase A (V)'],
        'a1'      => ['column' => 'a1',      'label' => 'Current Phase A (A)'],
    ];

    public function daily($meterId, $param, $date = null)
    {
        $meter = Meter::findOrFail($meterId);

        if (! isset($this->parameters[$param])) {
            abort(404, 'Unsupported parameter');
        }

        $column = $this->parameters[$param]['column'];
        $label = $this->parameters[$param]['label'];

        $targetDate = $date ? Carbon::parse($date, config('app.timezone')) : now();

        $readings = Reading::query()
            ->where('meter_id', $meter->id)
            ->whereDate('time', $targetDate->toDateString())
            ->whereNotNull($column)
            ->orderBy('time')
            ->get(['time', $column]);

        $response = [
            'labels' => $readings->map(function ($row) {
                $timestamp = null;

                if ($row->time instanceof Carbon) {
                    $timestamp = $row->time->copy();
                } elseif (! empty($row->time)) {
                    $timestamp = Carbon::parse($row->time, config('app.timezone'));
                }

                if (! $timestamp) {
                    return null;
                }

                return $timestamp->timezone(config('app.timezone'))->format('H:i');
            })->toArray(),
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
