<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;

final readonly class GetNotificationsAction
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository
    ) {}

    public function execute(string $userId, int $perPage = 20): array
    {
        return $this->notificationRepository->getPaginated($userId, $perPage);
    }
}
