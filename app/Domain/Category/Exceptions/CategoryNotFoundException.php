<?php

namespace App\Domain\Category\Exceptions;

use Exception;

final class CategoryNotFoundException extends Exception
{
    public static function withId(int $id): self
    {
        return new self("Category with ID {$id} not found.");
    }

    public static function withSlug(string $slug): self
    {
        return new self("Category with slug '{$slug}' not found.");
    }
}
