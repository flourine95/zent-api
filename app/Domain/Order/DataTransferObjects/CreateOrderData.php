<?php

namespace App\Domain\Order\DataTransferObjects;

final readonly class CreateOrderData
{
    public function __construct(
        public int $userId,
        public string $code,
        public ?int $addressId,
        public ?string $notes,
    ) {}

    public static function fromRequest(int $userId, string $code, array $validated): self
    {
        return new self(
            userId: $userId,
            code: $code,
            addressId: $validated['address_id'] ?? null,
            notes: $validated['notes'] ?? null,
        );
    }
}
