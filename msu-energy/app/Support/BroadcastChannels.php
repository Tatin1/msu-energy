<?php

namespace App\Support;

/**
 * Central registry for broadcast channel names so backend + frontend stay in sync.
 */
class BroadcastChannels
{
    public const DASHBOARD_METRICS = 'dashboard.metrics';
    public const TRANSFORMER_OVERVIEW = 'transformers.overview';
    public const SYSTEM_LOGS = 'system.logs';
    public const BUILDING_LOGS = 'building.logs';

    public static function dashboardMetrics(): string
    {
        return self::DASHBOARD_METRICS;
    }

    public static function transformerOverview(): string
    {
        return self::TRANSFORMER_OVERVIEW;
    }

    public static function systemLogs(): string
    {
        return self::SYSTEM_LOGS;
    }

    public static function buildingLogs(): string
    {
        return self::BUILDING_LOGS;
    }
}
