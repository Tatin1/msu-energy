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
use App\Models\Tariff;
use App\Models\TransformerLog;
use App\Support\RealtimePayloads;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class IoTIngestController extends Controller
{
    public function storeReading(StoreReadingRequest $request): Response
    {
        $validated = $request->validated();
        \Log::info('Received reading payload', $validated);

        $meter = Meter::where('meter_code', $validated['meter_code'])->firstOrFail();

        $payload = $validated;
        unset($payload['meter_code']);
        $payload['time'] = Carbon::parse($payload['time']);

        if (! empty($payload['time_end'])) {
            $payload['time_end'] = Carbon::parse($payload['time_end']);
        } else {
            $payload['time_end'] = $payload['time']->copy()->addMinutes(15);
        }

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
