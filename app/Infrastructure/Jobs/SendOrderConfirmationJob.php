<?php

namespace App\Infrastructure\Jobs;

use App\Infrastructure\Models\User;
use App\Infrastructure\Notifications\OrderCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    /**
     * @param  array{id: string, code: string, total_amount: float|string, status: string}  $order
     */
    public function __construct(
        private readonly string $userId,
        private readonly array $order,
    ) {}

    public function handle(): void
    {
        $user = User::find($this->userId);

        if ($user === null) {
            Log::warning("SendOrderConfirmationJob: user {$this->userId} not found, skipping.");

            return;
        }

        $user->notify(new OrderCreatedNotification($this->order));
    }
}
