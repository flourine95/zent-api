<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Infrastructure\Models\User;

final class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function getPaginated(int $userId, int $perPage = 20): array
    {
        $user = User::findOrFail($userId);
        $notifications = $user->notifications()->paginate($perPage);

        return [
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'unread_count' => $user->unreadNotifications()->count(),
            ],
        ];
    }

    public function markAsRead(int $userId, string $notificationId): bool
    {
        $user = User::findOrFail($userId);
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification) {
            $notification->markAsRead();

            return true;
        }

        return false;
    }

    public function markAllAsRead(int $userId): bool
    {
        $user = User::findOrFail($userId);
        $user->unreadNotifications->markAsRead();

        return true;
    }

    public function delete(int $userId, string $notificationId): bool
    {
        $user = User::findOrFail($userId);
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification) {
            return $notification->delete();
        }

        return false;
    }

    public function getUnreadCount(int $userId): int
    {
        $user = User::findOrFail($userId);

        return $user->unreadNotifications()->count();
    }

    public function exists(int $userId, string $notificationId): bool
    {
        $user = User::findOrFail($userId);

        return $user->notifications()->where('id', $notificationId)->exists();
    }
}
