<?php

namespace App\Domain\Address\Exceptions;

use Exception;

final class AddressNotFoundException extends Exception
{
    public static function withId(int $id): self
    {
        return new self("Address with ID {$id} not found.");
    }
}
