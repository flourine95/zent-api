<?php

namespace App\Domain\User\Exceptions;

use App\Shared\Exceptions\DomainException;

final class InvalidCredentialsException extends DomainException
{
    public string $errorCode = 'INVALID_CREDENTIALS';

    public static function invalidEmailOrPassword(): self
    {
        return new self('Invalid email or password.');
    }

    public static function incorrectCurrentPassword(): self
    {
        $e = new self('Current password is incorrect.');
        $e->errorCode = 'INCORRECT_CURRENT_PASSWORD';

        return $e;
    }
}
