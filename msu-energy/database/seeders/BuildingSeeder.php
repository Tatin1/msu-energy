<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Building;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = [
            'COE' => ['name' => 'College of Engineering', 'online' => true],
            'SET' => ['name' => 'School of Engineering Technology', 'online' => true],
            'CSM' => ['name' => 'College of Science and Mathematics', 'online' => true],
            'ADMIN' => ['name' => 'Administration', 'online' => true],
            'PRISM' => ['name' => 'Premier Research Institute of Science and Mathematics', 'online' => true],
        ];

        foreach ($buildings as $code => $details) {
            Building::create([
                'code' => $code,
                'name' => $details['name'],
                'is_online' => $details['online'],
            ]);
        }

        // temporary meters for each building
        foreach (Building::all() as $building) {
            $building->meters()->create([
                'meter_code' => $building->code . '-MTR-1',
                'label' => 'Main Panel - ' . $building->code,
            ]);
        }
    }
}
