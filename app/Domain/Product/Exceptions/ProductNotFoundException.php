<?php

namespace App\Domain\Product\Exceptions;

use App\Shared\Exceptions\DomainException;

final class ProductNotFoundException extends DomainException
{
    public string $errorCode = 'PRODUCT_NOT_FOUND';

    public static function withId(int $id): self
    {
        return new self("Product with ID {$id} not found.");
    }

    public static function withSlug(string $slug): self
    {
        return new self("Product with slug '{$slug}' not found.");
    }
}
