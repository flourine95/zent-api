<?php

namespace App\Domain\Cart\Exceptions;

use Exception;

final class ProductVariantNotFoundException extends Exception
{
    public static function withId(int $id): self
    {
        return new self("Product variant with ID {$id} not found.");
    }
}
