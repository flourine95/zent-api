<?php

namespace App\Domain\Cart\Exceptions;

use App\Shared\Exceptions\DomainException;

final class CartItemNotFoundException extends DomainException
{
    public string $errorCode = 'CART_ITEM_NOT_FOUND';

    public static function withId(int $id): self
    {
        return new self("Cart item with ID {$id} not found.");
    }

    public static function forUser(int $userId, int $itemId): self
    {
        return new self("Cart item {$itemId} not found for user {$userId}.");
    }
}
