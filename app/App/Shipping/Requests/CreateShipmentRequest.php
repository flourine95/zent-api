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
            'provider_code.required' => 'Mã đơn vị vận chuyển là bắt buộc',
            'provider_code.in' => 'Đơn vị vận chuyển không hợp lệ',
            'order_data.required' => 'Dữ liệu đơn hàng là bắt buộc',
            'order_data.order.required' => 'Thông tin đơn hàng là bắt buộc',
            'order_data.products.required' => 'Danh sách sản phẩm là bắt buộc',
            'order_data.products.min' => 'Phải có ít nhất 1 sản phẩm',
        ];
    }
}
