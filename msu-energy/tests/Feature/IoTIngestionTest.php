<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Meter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IoTIngestionTest extends TestCase
{
    use RefreshDatabase;

    private string $token = 'test-secret';

    private Building $building;

    private Meter $meter;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.iot.token' => $this->token]);

        $this->building = Building::create([
            'code' => 'ENG-01',
            'name' => 'Engineering',
            'is_online' => true,
        ]);

        $this->meter = Meter::create([
            'building_id' => $this->building->id,
            'meter_code' => 'ENG-MTR-1',
            'label' => 'Main Panel',
        ]);
    }

    public function test_missing_token_is_rejected(): void
    {
        $this->postJson('/api/iot/readings', $this->validReadingPayload())
            ->assertStatus(401);
    }

    public function test_invalid_token_is_rejected(): void
    {
        $this->postJson('/api/iot/readings', $this->validReadingPayload(), ['X-IOT-TOKEN' => 'wrong-secret'])
            ->assertStatus(401);
    }

    public function test_reading_endpoint_requires_meter_code(): void
    {
        $payload = $this->validReadingPayload();
        unset($payload['meter_code']);

        $this->postJson('/api/iot/readings', $payload, $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['meter_code']);
    }

    public function test_reading_endpoint_persists_payload(): void
    {
        $payload = $this->validReadingPayload();

        $this->postJson('/api/iot/readings', $payload, $this->authHeaders())
            ->assertNoContent();

        $this->assertDatabaseHas('readings', [
            'meter_id' => $this->meter->id,
            'kwh' => $payload['kwh'],
        ]);
    }

    public function test_building_log_endpoint_validates_and_persists(): void
    {
        $today = now()->toDateString();

        $this->postJson('/api/iot/building-logs', [
            'building' => 'UNKNOWN',
            'date' => $today,
        ], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['building']);

        $payload = [
            'building' => $this->building->code,
            'date' => $today,
            'pf1' => 1.2,
        ];

        $this->postJson('/api/iot/building-logs', $payload, $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['pf1']);

        $payload['pf1'] = 0.96;
        $payload['kwh'] = 250.112;

        $this->postJson('/api/iot/building-logs', $payload, $this->authHeaders())
            ->assertNoContent();

        $this->assertDatabaseHas('building_logs', [
            'building' => $this->building->code,
            'kwh' => 250.112,
        ]);
    }

    public function test_system_log_endpoint_enforces_power_factor_range(): void
    {
        $payload = [
            'date' => now()->toDateString(),
            'total_pf' => 1.3,
        ];

        $this->postJson('/api/iot/system-logs', $payload, $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['total_pf']);

        $payload['total_pf'] = 0.89;
        $payload['total_kw'] = 1500.25;

        $this->postJson('/api/iot/system-logs', $payload, $this->authHeaders())
            ->assertNoContent();

        $this->assertDatabaseHas('system_logs', [
            'date' => $payload['date'],
            'total_pf' => 0.89,
        ]);
    }

    public function test_transformer_log_endpoint_persists_payload(): void
    {
        $payload = [
            'recorded_at' => now()->toIso8601String(),
            'frequency' => 60.02,
            'v1' => 230.12,
            'a1' => 12.5,
            'pf' => 0.94,
            'kwh' => 15.25,
        ];

        $this->postJson('/api/iot/transformer-logs', $payload, $this->authHeaders())
            ->assertNoContent();

        $this->assertDatabaseHas('transformer_logs', [
            'pf' => 0.94,
            'frequency' => 60.02,
        ]);
    }

    private function validReadingPayload(): array
    {
        return [
            'meter_code' => $this->meter->meter_code,
            'recorded_at' => now()->toIso8601String(),
            'voltage1' => 229.4,
            'current1' => 12.5,
            'power_factor' => 0.97,
            'kwh' => 12345.678,
        ];
    }

    private function authHeaders(): array
    {
        return ['X-IOT-TOKEN' => $this->token];
    }
}
