<?php

namespace App\App\Notification\Controllers;

use App\Domain\Notification\Actions\DeleteNotificationAction;
use App\Domain\Notification\Actions\GetNotificationsAction;
use App\Domain\Notification\Actions\GetUnreadCountAction;
use App\Domain\Notification\Actions\MarkAllAsReadAction;
use App\Domain\Notification\Actions\MarkAsReadAction;
use App\Domain\Notification\Exceptions\NotificationNotFoundException;
use App\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class NotificationController
{
    use ApiResponse;

    public function __construct(
        private GetNotificationsAction $getNotificationsAction,
        private MarkAsReadAction $markAsReadAction,
        private MarkAllAsReadAction $markAllAsReadAction,
        private DeleteNotificationAction $deleteNotificationAction,
        private GetUnreadCountAction $getUnreadCountAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->getNotificationsAction->execute($request->user()->id);

        return $this->paginated($result['data'], $result['meta']);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return $this->success(['count' => $this->getUnreadCountAction->execute($request->user()->id)]);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        try {
            $this->markAsReadAction->execute($request->user()->id, $id);

            return $this->message('Đã đánh dấu đã đọc');
        } catch (NotificationNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $this->markAllAsReadAction->execute($request->user()->id);

        return $this->message('Đã đánh dấu tất cả đã đọc');
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $this->deleteNotificationAction->execute($request->user()->id, $id);

            return $this->message('Đã xóa thông báo');
        } catch (NotificationNotFoundException $e) {
            return $this->error($e->getMessage(), $e->errorCode, 404);
        }
    }
}
