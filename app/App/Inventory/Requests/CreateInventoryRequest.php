<?php

namespace App\App\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'shelf_location' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'warehouse_id.required' => 'Kho là bắt buộc.',
            'warehouse_id.exists' => 'Kho không tồn tại.',
            'product_variant_id.required' => 'Biến thể sản phẩm là bắt buộc.',
            'product_variant_id.exists' => 'Biến thể sản phẩm không tồn tại.',
            'quantity.required' => 'Số lượng là bắt buộc.',
            'quantity.min' => 'Số lượng không được âm.',
        ];
    }
}
