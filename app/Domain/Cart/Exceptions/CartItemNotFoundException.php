<?php

namespace App\Domain\Cart\Exceptions;

use Exception;

final class CartItemNotFoundException extends Exception
{
    public static function withId(int $id): self
    {
        return new self("Cart item with ID {$id} not found.");
    }

    public static function forUser(int $userId, int $itemId): self
    {
        return new self("Cart item {$itemId} not found for user {$userId}.");
    }
}
