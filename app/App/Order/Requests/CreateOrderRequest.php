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
            'total_amount' => ['required', 'numeric', 'min:0'],
            'shipping_address' => ['required', 'array'],
            'shipping_address.name' => ['required', 'string'],
            'shipping_address.phone' => ['required', 'string'],
            'shipping_address.address' => ['required', 'string'],
            'billing_address' => ['nullable', 'array'],
            'billing_address.name' => ['required_with:billing_address', 'string'],
            'billing_address.phone' => ['required_with:billing_address', 'string'],
            'billing_address.address' => ['required_with:billing_address', 'string'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'items.*.warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.subtotal' => ['required', 'numeric', 'min:0'],
            'items.*.product_snapshot' => ['required', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'total_amount.required' => 'Tổng tiền là bắt buộc.',
            'items.required' => 'Đơn hàng phải có ít nhất 1 sản phẩm.',
            'items.min' => 'Đơn hàng phải có ít nhất 1 sản phẩm.',
            'items.*.product_variant_id.exists' => 'Sản phẩm không tồn tại.',
            'items.*.warehouse_id.exists' => 'Kho không tồn tại.',
        ];
    }
}
