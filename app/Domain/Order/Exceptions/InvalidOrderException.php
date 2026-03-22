<?php

namespace App\Domain\Order\Exceptions;

use App\Shared\Exceptions\DomainException;

final class InvalidOrderException extends DomainException
{
    public string $errorCode = 'INVALID_ORDER';

    public static function noItems(): self
    {
        return new self('Order must have at least one item.');
    }

    public static function emptyCart(): self
    {
        $e = new self('Cannot place an order with an empty cart.');
        $e->errorCode = 'EMPTY_CART';

        return $e;
    }

    public static function noShippingAddress(): self
    {
        $e = new self('No shipping address found. Please add an address to your account.');
        $e->errorCode = 'NO_SHIPPING_ADDRESS';

        return $e;
    }

    public static function noWarehouseAvailable(int $variantId): self
    {
        $e = new self("No warehouse has sufficient stock for variant ID {$variantId}.");
        $e->errorCode = 'NO_WAREHOUSE_AVAILABLE';

        return $e;
    }

    public static function totalMismatch(float $expected, float $calculated): self
    {
        $e = new self("Order total mismatch. Expected: {$expected}, Calculated: {$calculated}");
        $e->errorCode = 'ORDER_TOTAL_MISMATCH';

        return $e;
    }

    public static function cannotCancel(string $currentStatus): self
    {
        $e = new self("Cannot cancel order with status: {$currentStatus}");
        $e->errorCode = 'ORDER_CANNOT_CANCEL';

        return $e;
    }
}
