<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;

final readonly class MarkAllAsReadAction
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository
    ) {}

    public function execute(int $userId): bool
    {
        return $this->notificationRepository->markAllAsRead($userId);
    }
}
