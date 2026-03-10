<?php

namespace App\Domain\ProductVariant\Exceptions;

use Exception;

final class ProductNotFoundException extends Exception
{
    public static function withIdentifier(string $identifier): self
    {
        return new self("Product with identifier '{$identifier}' not found.");
    }
}
