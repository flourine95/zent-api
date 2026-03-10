<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Exceptions\NotificationNotFoundException;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;

final readonly class DeleteNotificationAction
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository
    ) {}

    /**
     * @throws NotificationNotFoundException
     */
    public function execute(int $userId, string $notificationId): bool
    {
        if (! $this->notificationRepository->exists($userId, $notificationId)) {
            throw NotificationNotFoundException::withId($notificationId);
        }

        return $this->notificationRepository->delete($userId, $notificationId);
    }
}
