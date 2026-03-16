<?php

namespace App\Domain\Address\Exceptions;

use App\Shared\Exceptions\DomainException;

final class AddressNotFoundException extends DomainException
{
    public string $errorCode = 'ADDRESS_NOT_FOUND';

    public static function withId(int $id): self
    {
        return new self("Address with ID {$id} not found.");
    }
}
