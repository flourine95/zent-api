<?php

namespace App\Infrastructure\Services;

use App\Domain\Order\Services\OrderDispatchServiceInterface;
use App\Infrastructure\Jobs\PersistOrderJob;
use Illuminate\Support\Facades\Redis;

class OrderDispatchService implements OrderDispatchServiceInterface
{
    private const SNAPSHOT_PREFIX = 'order:snapshot:';

    private const SNAPSHOT_TTL = 600; // 10 minutes

    public function dispatch(
        string $orderId,
        string $userId,
        array $orderData,
        array $items,
        array $variantQuantities,
    ): void {
        // Store snapshot as idempotency guard before dispatching
        Redis::setex(
            self::SNAPSHOT_PREFIX.$orderId,
            self::SNAPSHOT_TTL,
            json_encode(['order_id' => $orderId, 'dispatched_at' => now()->toIso8601String()])
        );

        PersistOrderJob::dispatch($orderId, $userId, $orderData, $items, $variantQuantities);
    }
}
