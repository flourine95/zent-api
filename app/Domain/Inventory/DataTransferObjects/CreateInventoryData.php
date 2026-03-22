<?php

namespace App\Domain\Inventory\DataTransferObjects;

final readonly class CreateInventoryData
{
    public function __construct(
        public string $warehouseId,
        public string $productVariantId,
        public int $quantity,
        public ?string $shelfLocation,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            warehouseId: $data['warehouse_id'],
            productVariantId: $data['product_variant_id'],
            quantity: $data['quantity'],
            shelfLocation: $data['shelf_location'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'warehouse_id' => $this->warehouseId,
            'product_variant_id' => $this->productVariantId,
            'quantity' => $this->quantity,
            'shelf_location' => $this->shelfLocation,
        ];
    }
}
