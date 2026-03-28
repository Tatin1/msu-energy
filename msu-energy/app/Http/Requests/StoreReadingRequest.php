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
            'time'       => ['required', 'date'],
            'time_end'   => ['nullable', 'date', 'after_or_equal:time'],
            'f'          => ['nullable', 'numeric'],
            'v1'         => ['nullable', 'numeric'],
            'v2'         => ['nullable', 'numeric'],
            'v3'         => ['nullable', 'numeric'],
            'a1'         => ['nullable', 'numeric'],
            'a2'         => ['nullable', 'numeric'],
            'a3'         => ['nullable', 'numeric'],
            'kw1'        => ['nullable', 'numeric'],
            'kw2'        => ['nullable', 'numeric'],
            'kw3'        => ['nullable', 'numeric'],
            'pf1'        => ['nullable', 'numeric', 'between:-1,1'],
            'pf2'        => ['nullable', 'numeric', 'between:-1,1'],
            'pf3'        => ['nullable', 'numeric', 'between:-1,1'],
            'pfiii'      => ['nullable', 'numeric', 'between:-1,1'],
            'kwiii'      => ['nullable', 'numeric'],
            'kvariii'    => ['nullable', 'numeric'],
            'kvaiii'     => ['nullable', 'numeric'],
            'kwhiii'     => ['nullable', 'numeric'],
            'cost'       => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
