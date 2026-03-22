<?php

namespace App\Domain\Category\Exceptions;

use App\Shared\Exceptions\DomainException;

final class CategoryNotFoundException extends DomainException
{
    public string $errorCode = 'CATEGORY_NOT_FOUND';

    public static function withId(string $id): self
    {
        return new self("Category with ID {$id} not found.");
    }

    public static function withSlug(string $slug): self
    {
        return new self("Category with slug '{$slug}' not found.");
    }
}
