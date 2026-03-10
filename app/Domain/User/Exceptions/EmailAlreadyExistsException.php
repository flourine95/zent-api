<?php

namespace App\Domain\User\Exceptions;

use Exception;

final class EmailAlreadyExistsException extends Exception
{
    public static function forEmail(string $email): self
    {
        return new self("Email '{$email}' is already registered.");
    }
}
