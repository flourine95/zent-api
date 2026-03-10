<?php

namespace App\Domain\User\Exceptions;

use Exception;

final class InvalidCredentialsException extends Exception
{
    public static function invalidEmailOrPassword(): self
    {
        return new self('Email hoặc mật khẩu không đúng.');
    }

    public static function incorrectCurrentPassword(): self
    {
        return new self('Mật khẩu hiện tại không đúng.');
    }
}
