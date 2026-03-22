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
            'title.required' => 'Title is required.',
            'image.required' => 'Image is required.',
            'position.required' => 'Position is required.',
            'position.in' => 'Invalid position.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
        ];
    }
}
