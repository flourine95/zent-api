<?php

namespace App\Domain\Order\Exceptions;

use Exception;

final class OrderNotFoundException extends Exception
{
    public static function withId(int $id): self
    {
        return new self("Order with ID {$id} not found.");
    }

    public static function withCode(string $code): self
    {
        return new self("Order with code '{$code}' not found.");
    }
}
