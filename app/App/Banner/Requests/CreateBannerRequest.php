<?php

namespace App\App\Banner\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'string', 'max:255'],
            'link' => ['nullable', 'url', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'string', 'in:home_hero,home_secondary,category_top,product_detail'],
            'order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'image.required' => 'Hình ảnh là bắt buộc.',
            'position.required' => 'Vị trí là bắt buộc.',
            'position.in' => 'Vị trí không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ];
    }
}
