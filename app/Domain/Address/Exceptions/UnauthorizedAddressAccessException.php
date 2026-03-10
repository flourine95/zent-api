<?php

namespace App\Domain\Address\Exceptions;

use Exception;

final class UnauthorizedAddressAccessException extends Exception
{
    public static function forUser(int $userId, int $addressId): self
    {
        return new self("User {$userId} is not authorized to access address {$addressId}.");
    }

    public static function message(): self
    {
        return new self('You are not authorized to access this address.');
    }
}
