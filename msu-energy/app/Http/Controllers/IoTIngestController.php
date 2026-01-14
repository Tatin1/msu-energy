<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuildingLogRequest;
use App\Http\Requests\StoreReadingRequest;
use App\Http\Requests\StoreSystemLogRequest;
use App\Models\BuildingLog;
use App\Models\Meter;
use App\Models\SystemLog;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class IoTIngestController extends Controller
{
    public function storeReading(StoreReadingRequest $request): Response
    {
        $validated = $request->validated();

        $meter = Meter::where('meter_code', $validated['meter_code'])->firstOrFail();

        $payload = $validated;
        unset($payload['meter_code']);
        // $payload['recorded_at'] = Carbon::parse($payload['recorded_at']);
        $payload['recorded_at'] = Carbon::parse(
            $payload['recorded_at'] ?? now(),
            config('app.timezone')
        );

        $meter->readings()->create($payload);

        return response()->noContent();
    }

    public function storeBuildingLog(StoreBuildingLogRequest $request): Response
    {
        BuildingLog::create($request->validated());

        return response()->noContent();
    }

    public function storeSystemLog(StoreSystemLogRequest $request): Response
    {
        SystemLog::create($request->validated());

        return response()->noContent();
    }
}
