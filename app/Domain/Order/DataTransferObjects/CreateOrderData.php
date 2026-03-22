<?php

namespace App\Domain\Order\DataTransferObjects;

final readonly class CreateOrderData
{
    public function __construct(
        public string $userId,
        public string $code,
        public ?string $addressId,
        public ?string $notes,
    ) {}

    public static function fromRequest(string $userId, string $code, array $validated): self
    {
        return new self(
            userId: $userId,
            code: $code,
            addressId: $validated['address_id'] ?? null,
            notes: $validated['notes'] ?? null,
        );
    }
}
