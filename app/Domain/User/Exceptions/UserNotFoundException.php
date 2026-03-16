<?php

namespace App\Domain\User\Exceptions;

use App\Shared\Exceptions\DomainException;

final class UserNotFoundException extends DomainException
{
    public string $errorCode = 'USER_NOT_FOUND';

    public static function withId(int $id): self
    {
        return new self("User with ID {$id} not found.");
    }

    public static function withEmail(string $email): self
    {
        return new self("User with email '{$email}' not found.");
    }
}
