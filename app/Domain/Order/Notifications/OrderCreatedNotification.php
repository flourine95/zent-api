<?php

namespace App\Domain\Order\Notifications;

use App\Infrastructure\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Created Successfully')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your order #{$this->order->code} has been created successfully.")
            ->line('Total amount: '.number_format($this->order->total_amount, 0, ',', '.').' VND')
            ->action('View Order', url("/orders/{$this->order->id}"))
            ->line('Thank you for your purchase!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->code,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
            'message' => "Order #{$this->order->code} has been created successfully",
        ];
    }
}
