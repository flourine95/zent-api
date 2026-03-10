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
            'from_province' => ['required', 'string'],
            'from_district' => ['required', 'string'],
            'to_province' => ['required', 'string'],
            'to_district' => ['required', 'string'],
            'weight' => ['required', 'integer', 'min:1'],
            'value' => ['required', 'integer', 'min:0'],
            'transport' => ['nullable', 'in:fly,road'],
        ];
    }

    public function messages(): array
    {
        return [
            'from_province.required' => 'Tỉnh/thành gửi hàng là bắt buộc',
            'from_district.required' => 'Quận/huyện gửi hàng là bắt buộc',
            'to_province.required' => 'Tỉnh/thành nhận hàng là bắt buộc',
            'to_district.required' => 'Quận/huyện nhận hàng là bắt buộc',
            'weight.required' => 'Trọng lượng là bắt buộc',
            'weight.integer' => 'Trọng lượng phải là số nguyên',
            'weight.min' => 'Trọng lượng phải lớn hơn 0',
            'value.required' => 'Giá trị đơn hàng là bắt buộc',
            'value.integer' => 'Giá trị đơn hàng phải là số nguyên',
            'value.min' => 'Giá trị đơn hàng không được âm',
            'transport.in' => 'Phương thức vận chuyển không hợp lệ',
        ];
    }
}
