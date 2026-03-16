<?php

namespace App\Domain\Inventory\Exceptions;

use App\Shared\Exceptions\DomainException;

final class DuplicateInventoryException extends DomainException
{
    public string $errorCode = 'DUPLICATE_INVENTORY';

    public static function forWarehouseAndVariant(int $warehouseId, int $productVariantId): self
    {
        return new self("Inventory already exists for warehouse {$warehouseId} and product variant {$productVariantId}.");
    }
}
