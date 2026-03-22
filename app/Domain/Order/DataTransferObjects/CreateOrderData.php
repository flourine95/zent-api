<?php

namespace App\Domain\Order\DataTransferObjects;

final readonly class CreateOrderData
{
    public function __construct(
        public string $orderId,
        public string $userId,
        public ?string $addressId,
        public ?string $billingAddressId,
        public ?string $notes,
    ) {}

    public static function fromRequest(string $orderId, string $userId, array $validated): self
    {
        return new self(
            orderId: $orderId,
            userId: $userId,
            addressId: $validated['address_id'] ?? null,
            billingAddressId: $validated['billing_address_id'] ?? null,
            notes: $validated['notes'] ?? null,
        );
    }
}
