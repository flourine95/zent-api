<?php

namespace App\Infrastructure\Services;

use App\Domain\Order\Services\OrderNotificationServiceInterface;
use App\Infrastructure\Jobs\SendOrderConfirmationJob;

class OrderNotificationService implements OrderNotificationServiceInterface
{
    public function notifyOrderCreated(string $userId, array $order): void
    {
        SendOrderConfirmationJob::dispatch($userId, $order);
    }
}
