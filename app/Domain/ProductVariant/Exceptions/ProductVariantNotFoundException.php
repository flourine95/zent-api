<?php

namespace App\Domain\ProductVariant\Exceptions;

use App\Shared\Exceptions\DomainException;

final class ProductVariantNotFoundException extends DomainException
{
    public string $errorCode = 'PRODUCT_VARIANT_NOT_FOUND';

    public static function withId(string $id): self
    {
        return new self("Product variant with ID {$id} not found.");
    }
}
