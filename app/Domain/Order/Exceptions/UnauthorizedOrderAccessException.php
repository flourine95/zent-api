<?php

namespace App\Domain\Order\Exceptions;

use App\Shared\Exceptions\DomainException;

final class UnauthorizedOrderAccessException extends DomainException
{
    public string $errorCode = 'UNAUTHORIZED_ORDER_ACCESS';

    public static function forUser(): self
    {
        return new self('You are not authorized to access this order.');
    }
}
