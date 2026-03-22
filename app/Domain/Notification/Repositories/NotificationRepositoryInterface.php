<?php

namespace App\Domain\Notification\Repositories;

interface NotificationRepositoryInterface
{
    public function getPaginated(string $userId, int $perPage = 20): array;

    public function markAsRead(string $userId, string $notificationId): bool;

    public function markAllAsRead(string $userId): bool;

    public function delete(string $userId, string $notificationId): bool;

    public function getUnreadCount(string $userId): int;

    public function exists(string $userId, string $notificationId): bool;
}
