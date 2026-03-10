<?php

namespace App\Domain\Inventory\DataTransferObjects;

final readonly class UpdateInventoryData
{
    public function __construct(
        public int $id,
        public int $quantity,
        public ?string $shelfLocation,
    ) {}

    public static function fromArray(int $id, array $data): self
    {
        return new self(
            id: $id,
            quantity: $data['quantity'],
            shelfLocation: $data['shelf_location'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
            'shelf_location' => $this->shelfLocation,
        ];
    }
}
