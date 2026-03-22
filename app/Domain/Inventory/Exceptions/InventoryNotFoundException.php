<?php

namespace App\Domain\Inventory\Exceptions;

use App\Shared\Exceptions\DomainException;

final class InventoryNotFoundException extends DomainException
{
    public string $errorCode = 'INVENTORY_NOT_FOUND';

    public static function withId(string $id): self
    {
        return new self("Inventory with ID {$id} not found.");
    }
}
