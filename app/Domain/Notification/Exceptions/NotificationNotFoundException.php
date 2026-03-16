<?php

namespace App\Domain\Notification\Exceptions;

use App\Shared\Exceptions\DomainException;

final class NotificationNotFoundException extends DomainException
{
    public string $errorCode = 'NOTIFICATION_NOT_FOUND';

    public static function withId(string $id): self
    {
        return new self("Notification with ID {$id} not found.");
    }
}
