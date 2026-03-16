<?php

namespace App\Domain\ProductVariant\Exceptions;

use App\Shared\Exceptions\DomainException;

final class ProductNotFoundException extends DomainException
{
    public string $errorCode = 'PRODUCT_NOT_FOUND';

    public static function withIdentifier(string $identifier): self
    {
        return new self("Product with identifier '{$identifier}' not found.");
    }
}
