<?php

namespace App\Domain\Banner\Exceptions;

use App\Shared\Exceptions\DomainException;

final class InvalidBannerException extends DomainException
{
    public string $errorCode = 'INVALID_BANNER';

    public static function invalidDateRange(): self
    {
        return new self('Start date must be before end date.');
    }
}
