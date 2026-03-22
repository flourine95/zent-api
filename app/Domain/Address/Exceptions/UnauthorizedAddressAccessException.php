<?php

namespace App\Domain\Address\Exceptions;

use App\Shared\Exceptions\DomainException;

final class UnauthorizedAddressAccessException extends DomainException
{
    public string $errorCode = 'UNAUTHORIZED_ADDRESS_ACCESS';

    public static function forUser(string $userId, string $addressId): self
    {
        return new self("User {$userId} is not authorized to access address {$addressId}.");
    }

    public static function message(): self
    {
        return new self('You are not authorized to access this address.');
    }
}
