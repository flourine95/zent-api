<?php

namespace App\Domain\Order\DataTransferObjects;

final readonly class CreateOrderData
{
    public function __construct(
        public int $userId,
        public string $code,
        public float $totalAmount,
        public array $shippingAddress,
        public array $billingAddress,
        public ?string $notes,
        public array $items,
    ) {}

    public static function fromRequest(int $userId, string $code, array $validated): self
    {
        return new self(
            userId: $userId,
            code: $code,
            totalAmount: $validated['total_amount'],
            shippingAddress: $validated['shipping_address'],
            billingAddress: $validated['billing_address'] ?? $validated['shipping_address'],
            notes: $validated['notes'] ?? null,
            items: $validated['items'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'code' => $this->code,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_amount' => $this->totalAmount,
            'shipping_address' => $this->shippingAddress,
            'billing_address' => $this->billingAddress,
            'notes' => $this->notes,
        ];
    }
}
