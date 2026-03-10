<?php

namespace App\Domain\Wishlist\DataTransferObjects;

final readonly class AddWishlistData
{
    public function __construct(
        public int $userId,
        public int $productId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            productId: $data['product_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'product_id' => $this->productId,
        ];
    }
}
