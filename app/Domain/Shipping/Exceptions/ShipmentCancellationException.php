<?php

namespace App\Domain\Shipping\Exceptions;

use App\Shared\Exceptions\DomainException;

final class ShipmentCancellationException extends DomainException
{
    public string $errorCode = 'SHIPMENT_CANNOT_BE_CANCELLED';

    public static function invalidStatus(string $status): self
    {
        return new self("Shipment with status '{$status}' cannot be cancelled.");
    }
}
