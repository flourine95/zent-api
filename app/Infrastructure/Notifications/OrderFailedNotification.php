<?php

namespace App\Infrastructure\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $orderId,
        private readonly string $orderCode,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Could Not Be Processed')
            ->greeting("Hello {$notifiable->name}!")
            ->line("We're sorry — your order #{$this->orderCode} could not be processed due to a system error.")
            ->line('Your payment has not been charged. Please try placing your order again.')
            ->line('We apologize for the inconvenience.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->orderId,
            'order_code' => $this->orderCode,
            'message' => "Order #{$this->orderCode} could not be processed. Please try again.",
        ];
    }
}
