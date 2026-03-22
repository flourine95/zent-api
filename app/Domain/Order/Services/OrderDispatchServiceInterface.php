<?php

namespace App\Domain\Order\Services;

interface OrderDispatchServiceInterface
{
    /**
     * Store an idempotency snapshot and dispatch the async DB persistence job.
     *
     * @param  array<string, mixed>  $orderData
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, int>  $variantQuantities
     */
    public function dispatch(
        string $orderId,
        string $userId,
        array $orderData,
        array $items,
        array $variantQuantities,
    ): void;
}
