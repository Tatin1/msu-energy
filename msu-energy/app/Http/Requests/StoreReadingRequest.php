<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meter_code' => ['required', 'string', 'exists:meters,meter_code'],
            'recorded_at' => ['required', 'date'],
            'voltage1' => ['nullable', 'numeric'],
            'voltage2' => ['nullable', 'numeric'],
            'voltage3' => ['nullable', 'numeric'],
            'current1' => ['nullable', 'numeric'],
            'current2' => ['nullable', 'numeric'],
            'current3' => ['nullable', 'numeric'],
            'power_factor' => ['nullable', 'numeric', 'between:-1,1'],
            'active_power' => ['nullable', 'numeric'],
            'reactive_power' => ['nullable', 'numeric'],
            'apparent_power' => ['nullable', 'numeric'],
            'kwh' => ['nullable', 'numeric'],
        ];
    }
}
