<?php

namespace App\Domain\Order\Exceptions;

use Exception;

final class InvalidOrderException extends Exception
{
    public static function noItems(): self
    {
        return new self('Order must have at least one item.');
    }

    public static function totalMismatch(float $expected, float $calculated): self
    {
        return new self("Order total mismatch. Expected: {$expected}, Calculated: {$calculated}");
    }

    public static function cannotCancel(string $currentStatus): self
    {
        return new self("Cannot cancel order with status: {$currentStatus}");
    }
}
