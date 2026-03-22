<?php

namespace App\Domain\Order\Services;

interface OrderNotificationServiceInterface
{
    /**
     * Dispatch an async notification to the user that their order was created.
     *
     * @param  array{id: string, code: string, total_amount: float|string, status: string}  $order
     */
    public function notifyOrderCreated(string $userId, array $order): void;
}
