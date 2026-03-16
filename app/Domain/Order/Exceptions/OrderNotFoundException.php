<?php

namespace App\Domain\Order\Exceptions;

use App\Shared\Exceptions\DomainException;

final class OrderNotFoundException extends DomainException
{
    public string $errorCode = 'ORDER_NOT_FOUND';

    public static function withId(int $id): self
    {
        return new self("Order with ID {$id} not found.");
    }

    public static function withCode(string $code): self
    {
        return new self("Order with code '{$code}' not found.");
    }
}
