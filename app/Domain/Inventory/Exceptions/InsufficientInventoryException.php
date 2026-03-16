<?php

namespace App\Domain\Inventory\Exceptions;

use App\Shared\Exceptions\DomainException;

final class InsufficientInventoryException extends DomainException
{
    public string $errorCode = 'INSUFFICIENT_INVENTORY';

    public static function forAdjustment(int $inventoryId, int $currentQuantity, int $adjustment): self
    {
        return new self("Insufficient inventory {$inventoryId}. Current: {$currentQuantity}, Adjustment: {$adjustment}");
    }
}
