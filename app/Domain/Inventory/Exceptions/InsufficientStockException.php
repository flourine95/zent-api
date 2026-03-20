<?php

namespace App\Domain\Inventory\Exceptions;

use App\Shared\Exceptions\DomainException;

final class InsufficientStockException extends DomainException
{
    public string $errorCode = 'INSUFFICIENT_STOCK';

    public static function forVariant(int $productVariantId, int $warehouseId, int $requested, int $available): self
    {
        return new self(
            "Insufficient stock for variant {$productVariantId} in warehouse {$warehouseId}. Requested: {$requested}, Available: {$available}."
        );
    }
}
