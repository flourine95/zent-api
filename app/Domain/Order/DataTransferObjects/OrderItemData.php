<?php

namespace App\Domain\Order\DataTransferObjects;

final readonly class OrderItemData
{
    public function __construct(
        public int $productVariantId,
        public int $warehouseId,
        public int $quantity,
        public float $price,
        public float $subtotal,
        public array $productSnapshot,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productVariantId: $data['product_variant_id'],
            warehouseId: $data['warehouse_id'],
            quantity: $data['quantity'],
            price: $data['price'],
            subtotal: $data['subtotal'],
            productSnapshot: $data['product_snapshot'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'product_variant_id' => $this->productVariantId,
            'warehouse_id' => $this->warehouseId,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal,
            'product_snapshot' => $this->productSnapshot,
        ];
    }
}
