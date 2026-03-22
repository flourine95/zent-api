<?php

namespace App\App\Order\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => ['nullable', 'uuid', 'exists:addresses,id'],
            'billing_address_id' => ['nullable', 'uuid', 'exists:addresses,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.exists' => 'Shipping address not found.',
            'billing_address_id.exists' => 'Billing address not found.',
        ];
    }
}
