<?php

namespace App\Domain\Inventory\Exceptions;

use Exception;

final class InsufficientInventoryException extends Exception
{
    public static function forAdjustment(int $inventoryId, int $currentQuantity, int $adjustment): self
    {
        return new self("Insufficient inventory {$inventoryId}. Current: {$currentQuantity}, Adjustment: {$adjustment}");
    }
}
