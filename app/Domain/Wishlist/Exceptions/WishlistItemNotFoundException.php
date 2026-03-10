<?php

namespace App\Domain\Wishlist\Exceptions;

use Exception;

final class WishlistItemNotFoundException extends Exception
{
    public static function forProduct(int $productId): self
    {
        return new self("Product {$productId} not found in wishlist.");
    }
}
