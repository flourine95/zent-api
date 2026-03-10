<?php

namespace App\Domain\Notification\Exceptions;

use Exception;

final class NotificationNotFoundException extends Exception
{
    public static function withId(string $id): self
    {
        return new self("Notification with ID {$id} not found.");
    }
}
