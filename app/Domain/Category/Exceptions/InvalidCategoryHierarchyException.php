<?php

namespace App\Domain\Category\Exceptions;

use Exception;

final class InvalidCategoryHierarchyException extends Exception
{
    public static function circularReference(int $categoryId, int $parentId): self
    {
        return new self(
            "Cannot set category {$parentId} as parent of category {$categoryId}. This would create a circular reference."
        );
    }

    public static function selfReference(int $categoryId): self
    {
        return new self("Category {$categoryId} cannot be its own parent.");
    }
}
