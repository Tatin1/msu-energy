<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'building' => ['required', 'string', 'exists:buildings,code'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'time_ed' => ['nullable', 'date_format:H:i'],
            'f' => ['nullable', 'numeric'],
            'v1' => ['nullable', 'numeric'],
            'v2' => ['nullable', 'numeric'],
            'v3' => ['nullable', 'numeric'],
            'a1' => ['nullable', 'numeric'],
            'a2' => ['nullable', 'numeric'],
            'a3' => ['nullable', 'numeric'],
            'pf1' => ['nullable', 'numeric', 'between:-1,1'],
            'pf2' => ['nullable', 'numeric', 'between:-1,1'],
            'pf3' => ['nullable', 'numeric', 'between:-1,1'],
            'kwh' => ['nullable', 'numeric'],
        ];
    }
}
