<?php

namespace App\Domain\Product\Exceptions;

use Exception;

final class ProductNotFoundException extends Exception
{
    public static function withId(int $id): self
    {
        return new self("Product with ID {$id} not found.");
    }

    public static function withSlug(string $slug): self
    {
        return new self("Product with slug '{$slug}' not found.");
    }
}
