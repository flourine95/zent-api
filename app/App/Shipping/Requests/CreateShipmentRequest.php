<?php

namespace App\App\Shipping\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_code' => ['required', 'string', 'in:ghtk,ghn'],
            'order_data' => ['required', 'array'],
            'order_data.order' => ['required', 'array'],
            'order_data.products' => ['required', 'array', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'provider_code.required' => 'Shipping provider code is required.',
            'provider_code.in' => 'Invalid shipping provider.',
            'order_data.required' => 'Order data is required.',
            'order_data.order.required' => 'Order information is required.',
            'order_data.products.required' => 'Product list is required.',
            'order_data.products.min' => 'At least 1 product is required.',
        ];
    }
}
