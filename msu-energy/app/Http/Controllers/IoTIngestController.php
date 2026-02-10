<?php

namespace App\Http\Controllers;

use App\Events\BuildingLogRecorded;
use App\Events\ReadingIngested;
use App\Events\SystemLogRecorded;
use App\Events\TransformerLogRecorded;
use App\Http\Requests\StoreBuildingLogRequest;
use App\Http\Requests\StoreReadingRequest;
use App\Http\Requests\StoreSystemLogRequest;
use App\Http\Requests\StoreTransformerLogRequest;
use App\Models\BuildingLog;
use App\Models\Meter;
use App\Models\SystemLog;
use App\Models\TransformerLog;
use App\Support\RealtimePayloads;
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
        $payload['recorded_at'] = Carbon::parse($payload['recorded_at']);

        $meter->readings()->create($payload);

        event(new ReadingIngested(RealtimePayloads::dashboardMetrics()));

        return response()->noContent();
    }

    public function storeBuildingLog(StoreBuildingLogRequest $request): Response
    {
        BuildingLog::create($request->validated());

        event(new BuildingLogRecorded(RealtimePayloads::buildingLogTable()));

        return response()->noContent();
    }

    public function storeSystemLog(StoreSystemLogRequest $request): Response
    {
        SystemLog::create($request->validated());

        event(new SystemLogRecorded(RealtimePayloads::systemLogTable()));

        return response()->noContent();
    }

    public function storeTransformerLog(StoreTransformerLogRequest $request): Response
    {
        $payload = $request->validated();

        if (! empty($payload['recorded_at'])) {
            $payload['recorded_at'] = Carbon::parse($payload['recorded_at']);
        }

        TransformerLog::create($payload);

        event(new TransformerLogRecorded(RealtimePayloads::transformerTable()));

        return response()->noContent();
    }
}
