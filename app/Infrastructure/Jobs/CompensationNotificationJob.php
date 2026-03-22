<?php

namespace App\Infrastructure\Jobs;

use App\Infrastructure\Models\User;
use App\Infrastructure\Notifications\OrderFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CompensationNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        private readonly string $userId,
        private readonly string $orderId,
        private readonly string $orderCode,
    ) {}

    public function handle(): void
    {
        $user = User::find($this->userId);

        if ($user === null) {
            Log::warning("CompensationNotificationJob: user {$this->userId} not found, skipping.");

            return;
        }

        $user->notify(new OrderFailedNotification($this->orderId, $this->orderCode));
    }
}
