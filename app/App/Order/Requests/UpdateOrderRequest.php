<?php

namespace App\App\Order\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:pending,processing,completed,cancelled'],
            'payment_status' => ['required', 'string', 'in:unpaid,paid,refunded'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.in' => 'Invalid payment status.',
        ];
    }
}
