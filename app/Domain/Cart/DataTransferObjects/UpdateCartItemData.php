<?php

namespace App\Domain\Cart\DataTransferObjects;

final readonly class UpdateCartItemData
{
    public function __construct(
        public string $userId,
        public string $cartItemId,
        public int $quantity,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            cartItemId: $data['cart_item_id'],
            quantity: $data['quantity'],
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'cart_item_id' => $this->cartItemId,
            'quantity' => $this->quantity,
        ];
    }
}
