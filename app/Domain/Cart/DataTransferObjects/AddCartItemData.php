<?php

namespace App\Domain\Cart\DataTransferObjects;

final readonly class AddCartItemData
{
    public function __construct(
        public string $userId,
        public string $productVariantId,
        public int $quantity,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            productVariantId: $data['product_variant_id'],
            quantity: $data['quantity'],
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'product_variant_id' => $this->productVariantId,
            'quantity' => $this->quantity,
        ];
    }
}
