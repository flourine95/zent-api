<?php

namespace App\Domain\Cart\Exceptions;

use Exception;

final class InvalidQuantityException extends Exception
{
    public static function mustBePositive(): self
    {
        return new self('Quantity must be greater than 0.');
    }

    public static function withValue(int $quantity): self
    {
        return new self("Invalid quantity: {$quantity}. Must be greater than 0.");
    }
}
