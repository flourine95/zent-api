<?php

namespace App\Domain\Order\DataTransferObjects;

final readonly class CreateOrderData
{
    public function __construct(
        public string $userId,
        public ?string $addressId,
        public ?string $notes,
    ) {}

    public static function fromRequest(string $userId, array $validated): self
    {
        return new self(
            userId: $userId,
            addressId: $validated['address_id'] ?? null,
            notes: $validated['notes'] ?? null,
        );
    }
}
