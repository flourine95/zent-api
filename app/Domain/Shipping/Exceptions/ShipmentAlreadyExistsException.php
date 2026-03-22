<?php

namespace App\Domain\Shipping\Exceptions;

use App\Shared\Exceptions\DomainException;

final class ShipmentAlreadyExistsException extends DomainException
{
    public string $errorCode = 'SHIPMENT_ALREADY_EXISTS';

    public static function forOrder(string $orderId): self
    {
        return new self("A shipment already exists for order ID {$orderId}.");
    }
}
