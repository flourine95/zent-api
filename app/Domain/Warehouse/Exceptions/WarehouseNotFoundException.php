<?php

namespace App\Domain\Warehouse\Exceptions;

use App\Shared\Exceptions\DomainException;

final class WarehouseNotFoundException extends DomainException
{
    public string $errorCode = 'WAREHOUSE_NOT_FOUND';

    public static function withId(string $id): self
    {
        return new self("Warehouse with ID {$id} not found.");
    }
}
