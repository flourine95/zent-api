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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'code' => ['required', 'string', 'max:255', 'unique:orders,code'],
            'status' => ['string', 'in:pending,processing,completed,cancelled'],
            'payment_status' => ['string', 'in:unpaid,paid,refunded'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'shipping_address' => ['required', 'array'],
            'shipping_address.name' => ['required', 'string'],
            'shipping_address.phone' => ['required', 'string'],
            'shipping_address.address' => ['required', 'string'],
            'billing_address' => ['required', 'array'],
            'billing_address.name' => ['required', 'string'],
            'billing_address.phone' => ['required', 'string'],
            'billing_address.address' => ['required', 'string'],
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
            'user_id.required' => 'User ID là bắt buộc.',
            'user_id.exists' => 'User không tồn tại.',
            'code.required' => 'Mã đơn hàng là bắt buộc.',
            'code.unique' => 'Mã đơn hàng đã tồn tại.',
            'total_amount.required' => 'Tổng tiền là bắt buộc.',
            'items.required' => 'Đơn hàng phải có ít nhất 1 sản phẩm.',
            'items.min' => 'Đơn hàng phải có ít nhất 1 sản phẩm.',
        ];
    }
}
