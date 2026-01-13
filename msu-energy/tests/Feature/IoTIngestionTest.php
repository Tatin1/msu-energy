<?php

namespace Tests\Feature;

use App\Http\Requests\StoreBuildingLogRequest;
use App\Http\Requests\StoreReadingRequest;
use App\Http\Requests\StoreSystemLogRequest;
use App\Models\Building;
use App\Models\Meter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class IoTIngestionTest extends TestCase
{
    use RefreshDatabase;

    private string $token = 'test-secret';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.iot.token' => $this->token]);
    }

    public function test_missing_token_is_rejected(): void
    {
        Route::middleware('iot.auth')->post('/__test/iot/ping-missing', fn () => response()->noContent());

        $this->postJson('/__test/iot/ping-missing')->assertStatus(401);
    }

    public function test_invalid_token_is_rejected(): void
    {
        Route::middleware('iot.auth')->post('/__test/iot/ping-invalid', fn () => response()->noContent());

        $this->postJson('/__test/iot/ping-invalid', [], ['X-IOT-TOKEN' => 'wrong-secret'])
            ->assertStatus(401);
    }

    public function test_valid_token_allows_request(): void
    {
        Route::middleware('iot.auth')->post('/__test/iot/ping-valid', fn () => response()->noContent());

        $this->postJson('/__test/iot/ping-valid', [], $this->authHeaders())
            ->assertNoContent();
    }

    public function test_reading_request_validates_meter_and_timestamp(): void
    {
        Route::middleware('iot.auth')->post('/__test/iot/readings', fn (StoreReadingRequest $request) => response()->json($request->validated()));

        $this->postJson('/__test/iot/readings', [
            'recorded_at' => now()->toISOString(),
        ], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['meter_code']);

        $building = Building::create([
            'code' => 'SCI-01',
            'name' => 'Science Building',
            'is_online' => true,
        ]);

        Meter::create([
            'building_id' => $building->id,
            'meter_code' => 'SCI-MTR-1',
            'label' => 'Panel 1',
        ]);

        $payload = [
            'meter_code' => 'SCI-MTR-1',
            'recorded_at' => now()->toISOString(),
            'voltage1' => 229.4,
            'current1' => 12.5,
            'power_factor' => 0.97,
            'kwh' => 12345.678,
        ];

        $this->postJson('/__test/iot/readings', $payload, $this->authHeaders())
            ->assertStatus(200)
            ->assertJsonFragment([
                'meter_code' => 'SCI-MTR-1',
                'kwh' => 12345.678,
            ]);
    }

    public function test_building_log_request_enforces_known_building_and_ranges(): void
    {
        Route::middleware('iot.auth')->post('/__test/iot/building-logs', fn (StoreBuildingLogRequest $request) => response()->json($request->validated()));

        $this->postJson('/__test/iot/building-logs', [
            'building' => 'UNKNOWN',
            'date' => now()->toDateString(),
        ], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['building']);

        $building = Building::create([
            'code' => 'ENG-01',
            'name' => 'Engineering',
            'is_online' => true,
        ]);

        $payload = [
            'building' => $building->code,
            'date' => now()->toDateString(),
            'pf1' => 1.25,
        ];

        $this->postJson('/__test/iot/building-logs', $payload, $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['pf1']);

        $payload['pf1'] = 0.95;
        $payload['kwh'] = 250.112;

        $this->postJson('/__test/iot/building-logs', $payload, $this->authHeaders())
            ->assertStatus(200)
            ->assertJsonFragment([
                'building' => 'ENG-01',
                'kwh' => 250.112,
            ]);
    }

    public function test_system_log_request_limits_power_factor(): void
    {
        Route::middleware('iot.auth')->post('/__test/iot/system-logs', fn (StoreSystemLogRequest $request) => response()->json($request->validated()));

        $payload = [
            'date' => now()->toDateString(),
            'total_pf' => 1.3,
        ];

        $this->postJson('/__test/iot/system-logs', $payload, $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['total_pf']);

        $payload['total_pf'] = 0.88;
        $payload['total_kw'] = 1520.12;

        $this->postJson('/__test/iot/system-logs', $payload, $this->authHeaders())
            ->assertStatus(200)
            ->assertJsonFragment([
                'total_pf' => 0.88,
                'total_kw' => 1520.12,
            ]);
    }

    private function authHeaders(): array
    {
        return ['X-IOT-TOKEN' => $this->token];
    }
}
