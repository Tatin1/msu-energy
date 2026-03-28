<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransformerLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recorded_at' => ['required', 'date'],
            'meter_id' => ['nullable', 'integer', 'exists:meters,id'],
            'date' => ['nullable', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'time_ed' => ['nullable', 'date_format:H:i'],
            'frequency' => ['nullable', 'numeric'],
            'v1' => ['nullable', 'numeric'],
            'v2' => ['nullable', 'numeric'],
            'v3' => ['nullable', 'numeric'],
            'a1' => ['nullable', 'numeric'],
            'a2' => ['nullable', 'numeric'],
            'a3' => ['nullable', 'numeric'],
            'kw1' => ['nullable', 'numeric'],
            'kw2' => ['nullable', 'numeric'],
            'kw3' => ['nullable', 'numeric'],
            'pf1' => ['nullable', 'numeric', 'between:-1,1'],
            'pf2' => ['nullable', 'numeric', 'between:-1,1'],
            'pf3' => ['nullable', 'numeric', 'between:-1,1'],
            'kwiii' => ['nullable', 'numeric'],
            'kvaiii' => ['nullable', 'numeric'],
            'kvariii' => ['nullable', 'numeric'],
            'pfiii' => ['nullable', 'numeric', 'between:-1,1'],
            'kwh' => ['nullable', 'numeric'],
            'cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
