<?php

namespace App\Domain\User\Exceptions;

use App\Shared\Exceptions\DomainException;

final class InvalidCredentialsException extends DomainException
{
    public string $errorCode = 'INVALID_CREDENTIALS';

    public static function invalidEmailOrPassword(): self
    {
        return new self('Email hoặc mật khẩu không đúng.');
    }

    public static function incorrectCurrentPassword(): self
    {
        $e = new self('Mật khẩu hiện tại không đúng.');
        $e->errorCode = 'INCORRECT_CURRENT_PASSWORD';

        return $e;
    }
}
