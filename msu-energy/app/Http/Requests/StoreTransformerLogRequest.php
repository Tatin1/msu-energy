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
            'frequency' => ['nullable', 'numeric'],
            'v1' => ['nullable', 'numeric'],
            'v2' => ['nullable', 'numeric'],
            'v3' => ['nullable', 'numeric'],
            'a1' => ['nullable', 'numeric'],
            'a2' => ['nullable', 'numeric'],
            'a3' => ['nullable', 'numeric'],
            'pf' => ['nullable', 'numeric', 'between:-1,1'],
            'kwh' => ['nullable', 'numeric'],
        ];
    }
}
