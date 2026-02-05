<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\Building;
use App\Models\Meter;
use App\Models\Reading;
use App\Models\SystemLog;
use App\Models\TransformerLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $timeSlots = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00'];

        $seriesTemplates = [
            'COE' => [
                'active_power' => [140, 145, 160, 180, 170, 165, 160],
                'reactive_power' => [50, 55, 52, 56, 60, 57, 54],
                'apparent_power' => [160, 165, 170, 180, 190, 185, 175],
            ],
            'CCS' => [
                'active_power' => [120, 130, 135, 150, 145, 140, 138],
                'reactive_power' => [30, 32, 35, 34, 33, 32, 31],
                'apparent_power' => [130, 135, 140, 145, 150, 148, 143],
            ],
            'CSM' => [
                'active_power' => [110, 115, 118, 125, 120, 118, 122],
                'reactive_power' => [25, 28, 27, 29, 30, 31, 30],
                'apparent_power' => [115, 120, 125, 130, 128, 126, 124],
            ],
            'CEBA' => [
                'active_power' => [100, 105, 110, 120, 115, 110, 108],
                'reactive_power' => [28, 30, 32, 34, 33, 31, 30],
                'apparent_power' => [110, 115, 120, 125, 123, 122, 120],
            ],
            'CED' => [
                'active_power' => [90, 95, 98, 105, 100, 98, 102],
                'reactive_power' => [22, 24, 23, 25, 26, 24, 25],
                'apparent_power' => [100, 105, 110, 112, 114, 113, 110],
            ],
            'CON' => [
                'active_power' => [80, 82, 84, 90, 88, 86, 85],
                'reactive_power' => [20, 22, 23, 24, 25, 23, 22],
                'apparent_power' => [90, 92, 95, 98, 97, 96, 94],
            ],
        ];

        $buildingPresets = [
            'COE' => ['name' => 'BLDG1: COE', 'series_key' => 'COE', 'kwh_target' => 80, 'online' => true],
            'SET' => ['name' => 'BLDG2: SET', 'series_key' => 'CCS', 'kwh_target' => 60, 'online' => true],
            'CSM' => ['name' => 'BLDG3: CSM', 'series_key' => 'CSM', 'kwh_target' => 50, 'online' => true],
            'CCS' => ['name' => 'BLDG4: CCS', 'series_key' => 'CCS', 'kwh_target' => 70, 'online' => true],
            'PRISM' => ['name' => 'BLDG5: PRISM', 'series_key' => 'CEBA', 'kwh_target' => 100, 'online' => true],
            'CEBA' => ['name' => 'College of Economics and Business Administration', 'series_key' => 'CEBA', 'kwh_target' => 55, 'online' => true],
            'CED' => ['name' => 'College of Education', 'series_key' => 'CED', 'kwh_target' => 65, 'online' => true],
            'CON' => ['name' => 'College of Nursing', 'series_key' => 'CON', 'kwh_target' => 45, 'online' => true],
        ];

        $today = Carbon::today(config('app.timezone'));

        foreach ($buildingPresets as $code => $preset) {
            $series = $seriesTemplates[$preset['series_key']] ?? $seriesTemplates['COE'];

            $building = Building::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $preset['name'],
                    'is_online' => $preset['online'],
                ]
            );

            $meter = $building->meters()->updateOrCreate(
                ['meter_code' => $code.'-MAIN'],
                ['label' => $code.' Main']
            );

            $kwhPerSlot = ($preset['kwh_target'] ?? 60) / max(count($timeSlots), 1);

            foreach ($timeSlots as $index => $slot) {
                $recordedAt = $today->copy()->setTimeFromTimeString($slot);
                $active = $series['active_power'][$index] ?? null;
                $apparent = $series['apparent_power'][$index] ?? null;
                $reactive = $series['reactive_power'][$index] ?? null;
                $powerFactor = ($active && $apparent) ? round($active / max($apparent, 1), 3) : 0.94;
                $voltage = 228 + ($index % 3);
                $current = $apparent ? round(($apparent * 1000) / max($voltage * 1.732, 1), 2) : null;

                Reading::updateOrCreate(
                    [
                        'meter_id' => $meter->id,
                        'recorded_at' => $recordedAt,
                    ],
                    [
                        'voltage1' => $voltage,
                        'voltage2' => $voltage + 1,
                        'voltage3' => $voltage - 1,
                        'current1' => $current,
                        'current2' => $current,
                        'current3' => $current,
                        'power_factor' => $powerFactor,
                        'active_power' => $active,
                        'reactive_power' => $reactive,
                        'apparent_power' => $apparent,
                        'kwh' => round($kwhPerSlot, 3),
                    ]
                );
            }

            Billing::updateOrCreate(
                ['building_id' => $building->id],
                [
                    'last_month_kwh' => ($preset['kwh_target'] ?? 50) * 4,
                    'this_month_kwh' => ($preset['kwh_target'] ?? 50) * 4.2,
                    'total_bill' => 0,
                ]
            );
        }

        $transformerSamples = [
            ['timestamp' => '2025-10-31 10:15:00', 'voltage' => 230, 'load' => 50.2, 'pf' => 0.96],
            ['timestamp' => '2025-10-31 10:17:00', 'voltage' => 228, 'load' => 48.5, 'pf' => 0.88],
            ['timestamp' => '2025-10-31 10:20:00', 'voltage' => 231, 'load' => 52.0, 'pf' => 0.78],
        ];

        foreach ($transformerSamples as $sample) {
            TransformerLog::updateOrCreate(
                ['recorded_at' => Carbon::parse($sample['timestamp'], config('app.timezone'))],
                [
                    'frequency' => 60.0,
                    'v1' => $sample['voltage'],
                    'v2' => $sample['voltage'] + 1,
                    'v3' => $sample['voltage'] - 1,
                    'a1' => $sample['load'] / 3,
                    'a2' => $sample['load'] / 3,
                    'a3' => $sample['load'] / 3,
                    'pf' => $sample['pf'],
                    'kwh' => $sample['load'],
                ]
            );
        }

        $systemSnapshots = [
            ['date' => '2025-10-31', 'time' => '09:00:00', 'time_ed' => '09:15:00', 'kw' => 300, 'kvar' => 95, 'kva' => 314, 'pf' => 0.92],
            ['date' => '2025-10-31', 'time' => '09:45:00', 'time_ed' => '10:00:00', 'kw' => 280, 'kvar' => 110, 'kva' => 300, 'pf' => 0.87],
            ['date' => '2025-10-31', 'time' => '10:00:00', 'time_ed' => '10:15:00', 'kw' => 320, 'kvar' => 140, 'kva' => 350, 'pf' => 0.82],
            ['date' => '2025-10-31', 'time' => '10:15:00', 'time_ed' => '10:30:00', 'kw' => 310, 'kvar' => 100, 'kva' => 330, 'pf' => 0.94],
        ];

        foreach ($systemSnapshots as $snapshot) {
            SystemLog::updateOrCreate(
                ['date' => $snapshot['date'], 'time' => $snapshot['time']],
                [
                    'building' => 'SYSTEM',
                    'time_ed' => $snapshot['time_ed'],
                    'total_kw' => $snapshot['kw'],
                    'total_kvar' => $snapshot['kvar'],
                    'total_kva' => $snapshot['kva'],
                    'total_pf' => $snapshot['pf'],
                ]
            );
        }
    }
}
