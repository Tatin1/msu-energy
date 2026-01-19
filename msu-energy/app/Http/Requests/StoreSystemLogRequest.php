<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSystemLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->filled('building')) {
            $this->merge(['building' => 'SYSTEM']);
        }
    }

    public function rules(): array
    {
        return [
            'building' => ['nullable', 'string', 'max:120'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'time_ed' => ['nullable', 'date_format:H:i'],
            'total_kw' => ['nullable', 'numeric'],
            'total_kvar' => ['nullable', 'numeric'],
            'total_kva' => ['nullable', 'numeric'],
            'total_pf' => ['nullable', 'numeric', 'between:0,1'],
        ];
    }
}
