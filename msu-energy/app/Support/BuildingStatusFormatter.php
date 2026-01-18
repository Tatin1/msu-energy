<?php

namespace App\Support;

use App\Models\Building;
use App\Models\Reading;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BuildingStatusFormatter
{
    public static function summaries(int $onlineThresholdMinutes = 15, int $idleThresholdMinutes = 60): Collection
    {
        $buildings = Building::query()
            ->select('id', 'code', 'name', 'is_online')
            ->orderBy('code')
            ->get();

        $recency = Reading::query()
            ->selectRaw('meters.building_id, MAX(readings.recorded_at) as latest_recorded_at')
            ->join('meters', 'meters.id', '=', 'readings.meter_id')
            ->groupBy('meters.building_id')
            ->get()
            ->keyBy('building_id');

        return $buildings->map(function (Building $building) use ($recency, $onlineThresholdMinutes, $idleThresholdMinutes) {
            $latest = optional($recency->get($building->id))->latest_recorded_at;
            $latestAt = $latest ? Carbon::parse($latest) : null;
            $status = self::determineStatus($building, $latestAt, $onlineThresholdMinutes, $idleThresholdMinutes);

            return [
                'id' => $building->id,
                'code' => $building->code,
                'name' => $building->name,
                'is_online' => (bool) $building->is_online,
                'latest_reading_at' => $latestAt?->toIso8601String(),
                'status' => $status['label'],
                'status_reason' => $status['description'],
            ];
        })->values();
    }

    protected static function determineStatus(Building $building, ?Carbon $latestAt, int $onlineThresholdMinutes, int $idleThresholdMinutes): array
    {
        if (! $building->is_online) {
            return [
                'label' => 'offline',
                'description' => 'Manually set offline',
            ];
        }

        if (! $latestAt) {
            return [
                'label' => 'idle',
                'description' => 'No readings received yet',
            ];
        }

        $minutes = $latestAt->diffInMinutes(now());

        if ($minutes <= $onlineThresholdMinutes) {
            return [
                'label' => 'online',
                'description' => 'Telemetry received '.$latestAt->diffForHumans(null, true).' ago',
            ];
        }

        if ($minutes <= $idleThresholdMinutes) {
            return [
                'label' => 'idle',
                'description' => 'Last update '.$latestAt->diffForHumans(null, true).' ago',
            ];
        }

        return [
            'label' => 'offline',
            'description' => 'No updates for '.$latestAt->diffForHumans(null, true),
        ];
    }
}
