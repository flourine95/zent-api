<?php

namespace App\App\Notification\Controllers;

use App\Domain\Notification\Actions\DeleteNotificationAction;
use App\Domain\Notification\Actions\GetNotificationsAction;
use App\Domain\Notification\Actions\GetUnreadCountAction;
use App\Domain\Notification\Actions\MarkAllAsReadAction;
use App\Domain\Notification\Actions\MarkAsReadAction;
use App\Domain\Notification\Exceptions\NotificationNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NotificationController
{
    public function __construct(
        private readonly GetNotificationsAction $getNotificationsAction,
        private readonly MarkAsReadAction $markAsReadAction,
        private readonly MarkAllAsReadAction $markAllAsReadAction,
        private readonly DeleteNotificationAction $deleteNotificationAction,
        private readonly GetUnreadCountAction $getUnreadCountAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->getNotificationsAction->execute($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'meta' => $result['meta'],
        ]);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        try {
            $this->markAsReadAction->execute($request->user()->id, $id);

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu đã đọc',
            ]);
        } catch (NotificationNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo',
            ], 404);
        }
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $this->markAllAsReadAction->execute($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả đã đọc',
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $this->deleteNotificationAction->execute($request->user()->id, $id);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thông báo',
            ]);
        } catch (NotificationNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo',
            ], 404);
        }
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $this->getUnreadCountAction->execute($request->user()->id);

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}
