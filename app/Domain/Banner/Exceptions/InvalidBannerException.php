<?php

namespace App\Domain\Banner\Exceptions;

use Exception;

final class InvalidBannerException extends Exception
{
    public static function invalidDateRange(): self
    {
        return new self('Start date must be before end date.');
    }
}
