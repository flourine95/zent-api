<?php

namespace App\Domain\Order\DataTransferObjects;

final readonly class CreateOrderData
{
    public function __construct(
        public int $userId,
        public string $code,
        public string $status,
        public string $paymentStatus,
        public float $totalAmount,
        public array $shippingAddress,
        public array $billingAddress,
        public ?string $notes,
        public array $items, // Array of OrderItemData
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            code: $data['code'],
            status: $data['status'] ?? 'pending',
            paymentStatus: $data['payment_status'] ?? 'unpaid',
            totalAmount: $data['total_amount'],
            shippingAddress: $data['shipping_address'],
            billingAddress: $data['billing_address'],
            notes: $data['notes'] ?? null,
            items: $data['items'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'code' => $this->code,
            'status' => $this->status,
            'payment_status' => $this->paymentStatus,
            'total_amount' => $this->totalAmount,
            'shipping_address' => $this->shippingAddress,
            'billing_address' => $this->billingAddress,
            'notes' => $this->notes,
        ];
    }
}
