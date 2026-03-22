<?php

namespace App\App\Address\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:255'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line_1' => ['required', 'string'],
            'address_line_2' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:2'],
            'is_default' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_name.required' => 'Recipient name is required.',
            'phone.required' => 'Phone number is required.',
            'address_line_1.required' => 'Address is required.',
            'city.required' => 'City is required.',
            'postal_code.required' => 'Postal code is required.',
        ];
    }
}
