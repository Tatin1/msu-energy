<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        // 🧱 Create sample building, meters, readings, and billing data
        $names = [
            ['code'=>'COE','name'=>'BLDG1: COE'],
            ['code'=>'SET','name'=>'BLDG2: SET'],
            ['code'=>'CSM','name'=>'BLDG3: CSM'],
            ['code'=>'CCS','name'=>'BLDG4: CCS'],
            ['code'=>'PRISM','name'=>'BLDG5: PRISM'],
        ];

        foreach($names as $n){
            $b = \App\Models\Building::create([
                'code'=>$n['code'],
                'name'=>$n['name'],
                'is_online'=>rand(0,10)>1
            ]);

            // ⚙️ Create 2 meters per building
            for($i=1;$i<=2;$i++){
                $m = $b->meters()->create([
                    'meter_code'=>$b->code.'-'.$i,
                    'label'=>$b->code.'-'.$i
                ]);

                // 📊 Generate 12 dummy hourly readings
                for($h=0;$h<12;$h++){
                    $m->readings()->create([
                        'voltage1'=>230+rand(-4,6),
                        'voltage2'=>231+rand(-4,6),
                        'voltage3'=>229+rand(-5,5),
                        'current1'=>28+rand(-3,5),
                        'current2'=>29+rand(-3,5),
                        'current3'=>30+rand(-3,5),
                        'power_factor'=>round(0.9 + rand(-10,10)/1000,3),
                        'active_power'=>round(50 + rand(-10,30),3),
                        'reactive_power'=>round(20 + rand(-5,10),3),
                        'apparent_power'=>round(60 + rand(-10,35),3),
                        'kwh'=>round(25000 + rand(-3000,5000),3),
                        'recorded_at'=>now()->subHours(12-$h)
                    ]);
                }
            }

            // 🧾 Create billing record
            \App\Models\Billing::create([
                'building_id'=>$b->id,
                'last_month_kwh'=>rand(28000,36000),
                'this_month_kwh'=>rand(26000,38000),
                'total_bill'=>0
            ]);
        }

        // 💡 Default Tariff
        \App\Models\Tariff::create([
            'name'=>'default',
            'rate'=>12.00
        ]);

        // 👤 Default Admin User (login credentials)
        User::updateOrCreate(
            ['email' => 'admin@msuiit.edu.ph'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
