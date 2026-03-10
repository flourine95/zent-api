<?php

namespace App\Domain\Wishlist\Exceptions;

use Exception;

final class ProductNotFoundException extends Exception
{
    public static function withId(int $id): self
    {
        return new self("Product with ID {$id} not found.");
    }
}
