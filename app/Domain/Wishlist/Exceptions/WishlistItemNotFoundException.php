<?php

namespace App\Domain\Wishlist\Exceptions;

use App\Shared\Exceptions\DomainException;

final class WishlistItemNotFoundException extends DomainException
{
    public string $errorCode = 'WISHLIST_ITEM_NOT_FOUND';

    public static function forProduct(int $productId): self
    {
        return new self("Product {$productId} not found in wishlist.");
    }
}
