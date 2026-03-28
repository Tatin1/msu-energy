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
        \App\Models\Tariff::updateOrCreate(
            ['name' => 'default'],
            ['rate' => 12.00]
        );

        $this->call([
            BuildingSeeder::class,
            // DummyDataSeeder::class,
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
