<?php

namespace App\Shared\Exceptions;

use Exception;

abstract class DomainException extends Exception
{
    public string $errorCode = 'UNKNOWN_ERROR';
}
