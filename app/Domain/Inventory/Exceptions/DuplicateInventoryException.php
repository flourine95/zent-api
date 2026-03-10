<?php

namespace App\Domain\Inventory\Exceptions;

use Exception;

final class DuplicateInventoryException extends Exception
{
    public static function forWarehouseAndVariant(int $warehouseId, int $productVariantId): self
    {
        return new self("Inventory already exists for warehouse {$warehouseId} and product variant {$productVariantId}.");
    }
}
