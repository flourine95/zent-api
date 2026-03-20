<?php

namespace App\Domain\Shipping\Exceptions;

use App\Shared\Exceptions\DomainException;

final class ShipmentNotFoundException extends DomainException
{
    public string $errorCode = 'SHIPMENT_NOT_FOUND';

    public static function forOrder(int $orderId): self
    {
        return new self("No shipment found for order ID {$orderId}.");
    }
}
