<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;

final readonly class GetUnreadCountAction
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository
    ) {}

    public function execute(int $userId): int
    {
        return $this->notificationRepository->getUnreadCount($userId);
    }
}
