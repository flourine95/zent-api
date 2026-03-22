<?php

namespace App\App\Shipping\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateShippingFeesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // For GHTK (string names)
            'from_province' => ['required', 'string'],
            'from_district' => ['required', 'string'],
            'from_ward' => ['nullable', 'string'],
            'to_province' => ['required', 'string'],
            'to_district' => ['required', 'string'],
            'to_ward' => ['nullable', 'string'],

            // For GHN (IDs)
            'from_district_id' => ['nullable', 'integer'],
            'from_ward_code' => ['nullable', 'string'],
            'to_district_id' => ['nullable', 'integer'],
            'to_ward_code' => ['nullable', 'string'],

            // Common
            'weight' => ['required', 'integer', 'min:1'],
            'value' => ['required', 'integer', 'min:0'],
            'transport' => ['nullable', 'in:fly,road'],
        ];
    }

    public function messages(): array
    {
        return [
            'from_province.required' => 'Sender province is required.',
            'from_district.required' => 'Sender district is required.',
            'to_province.required' => 'Recipient province is required.',
            'to_district.required' => 'Recipient district is required.',
            'weight.required' => 'Weight is required.',
            'weight.integer' => 'Weight must be an integer.',
            'weight.min' => 'Weight must be greater than 0.',
            'value.required' => 'Order value is required.',
            'value.integer' => 'Order value must be an integer.',
            'value.min' => 'Order value cannot be negative.',
            'transport.in' => 'Invalid transport method.',
        ];
    }
}
