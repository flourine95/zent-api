<?php

namespace App\Domain\Notification\Repositories;

interface NotificationRepositoryInterface
{
    public function getPaginated(int $userId, int $perPage = 20): array;

    public function markAsRead(int $userId, string $notificationId): bool;

    public function markAllAsRead(int $userId): bool;

    public function delete(int $userId, string $notificationId): bool;

    public function getUnreadCount(int $userId): int;

    public function exists(int $userId, string $notificationId): bool;
}
