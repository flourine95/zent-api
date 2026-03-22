<?php

namespace App\App\Wishlist\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddWishlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'Product does not exist.',
        ];
    }
}
