<?php

namespace App\Domain\Banner\Exceptions;

use App\Shared\Exceptions\DomainException;

final class BannerNotFoundException extends DomainException
{
    public string $errorCode = 'BANNER_NOT_FOUND';

    public static function withId(string $id): self
    {
        return new self("Banner with ID {$id} not found.");
    }
}
