<?php

namespace App\Domain\User\Exceptions;

use App\Shared\Exceptions\DomainException;

final class EmailAlreadyExistsException extends DomainException
{
    public string $errorCode = 'EMAIL_ALREADY_EXISTS';

    public static function forEmail(string $email): self
    {
        return new self("Email '{$email}' is already registered.");
    }
}
