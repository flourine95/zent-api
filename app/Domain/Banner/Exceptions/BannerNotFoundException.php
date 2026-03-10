<?php

namespace App\Domain\Banner\Exceptions;

use Exception;

final class BannerNotFoundException extends Exception
{
    public static function withId(int $id): self
    {
        return new self("Banner with ID {$id} not found.");
    }
}
