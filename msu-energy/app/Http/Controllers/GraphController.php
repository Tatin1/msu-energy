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
            'labels' => $readings->map(function ($row) {
                $timestamp = null;

                if ($row->recorded_at instanceof Carbon) {
                    $timestamp = $row->recorded_at->copy();
                } elseif (! empty($row->recorded_at)) {
                    $timestamp = Carbon::parse($row->recorded_at, config('app.timezone'));
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
