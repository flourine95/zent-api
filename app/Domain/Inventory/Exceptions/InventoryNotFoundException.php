<?php

namespace App\Domain\Inventory\Exceptions;

use Exception;

final class InventoryNotFoundException extends Exception
{
    public static function withId(int $id): self
    {
        return new self("Inventory with ID {$id} not found.");
    }
}
