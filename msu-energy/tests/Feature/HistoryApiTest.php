<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Meter;
use App\Models\Reading;
use App\Models\SystemLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HistoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_building_history_endpoint_filters_by_building_and_date(): void
    {
        $building = Building::create([
            'code' => 'COE',
            'name' => 'College of Engineering',
            'is_online' => true,
        ]);

        $otherBuilding = Building::create([
            'code' => 'SET',
            'name' => 'School of Engineering Technology',
            'is_online' => true,
        ]);

        $meter = Meter::create([
            'building_id' => $building->id,
            'meter_code' => 'COE-MTR-1',
            'label' => 'Main Panel',
        ]);

        $otherMeter = Meter::create([
            'building_id' => $otherBuilding->id,
            'meter_code' => 'SET-MTR-1',
            'label' => 'Main Panel',
        ]);

        Reading::create([
            'meter_id' => $meter->id,
            'time' => '2025-11-01 08:15:00',
            'time_end' => '2025-11-01 08:30:00',
            'f' => 60,
            'v1' => 230,
            'v2' => 231,
            'v3' => 229,
            'a1' => 12.5,
            'a2' => 12.5,
            'a3' => 12.5,
            'pf1' => 0.95,
            'pf2' => 0.95,
            'pf3' => 0.95,
            'kwhiii' => 100.123,
        ]);

        Reading::create([
            'meter_id' => $otherMeter->id,
            'time' => '2025-11-02 09:15:00',
            'time_end' => '2025-11-02 09:30:00',
            'kwhiii' => 80.000,
        ]);

        $this->getJson('/api/history/building-logs?building=COE&date=2025-11-01')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'building' => 'COE',
                'date' => '2025-11-01',
                'time' => '08:15:00',
                'time_ed' => '08:30:00',
            ]);
    }

    public function test_system_history_endpoint_honors_per_page_and_filters(): void
    {
        $start = Carbon::create(2025, 11, 2, 10, 0, 0);

        foreach (range(0, 59) as $index) {
            SystemLog::create([
                'building' => $index % 2 === 0 ? 'COE' : 'SET',
                'date' => $start->copy()->subMinutes($index * 15)->toDateString(),
                'time' => $start->copy()->subMinutes($index * 15)->format('H:i:s'),
                'time_ed' => $start->copy()->subMinutes(($index * 15) - 15)->format('H:i:s'),
                'total_kw' => 200 + $index,
                'total_kvar' => 100 + $index,
                'total_kva' => 220 + $index,
                'total_pf' => 0.90,
            ]);
        }

        $this->getJson('/api/history/system-logs?building=COE&per_page=25')
            ->assertOk()
            ->assertJsonCount(25, 'data')
            ->assertJsonPath('meta.per_page', 25)
            ->assertJsonPath('meta.total', 30);
    }
}
