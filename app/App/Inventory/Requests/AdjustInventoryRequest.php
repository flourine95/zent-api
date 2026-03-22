<?php

namespace App\App\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'adjustment' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'adjustment.required' => 'Adjustment quantity is required.',
            'reason.required' => 'Adjustment reason is required.',
        ];
    }
}
