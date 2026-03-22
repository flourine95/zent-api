<?php

namespace App\Domain\Wishlist\Exceptions;

use App\Shared\Exceptions\DomainException;

final class ProductNotFoundException extends DomainException
{
    public string $errorCode = 'PRODUCT_NOT_FOUND';

    public static function withId(string $id): self
    {
        return new self("Product with ID {$id} not found.");
    }
}
