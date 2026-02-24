<?php

namespace App\Notifications;

use App\Models\Order;
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
            ->subject('Đơn hàng đã được tạo thành công')
            ->greeting("Xin chào {$notifiable->name}!")
            ->line("Đơn hàng #{$this->order->code} của bạn đã được tạo thành công.")
            ->line('Tổng tiền: '.number_format($this->order->total_amount, 0, ',', '.').' VND')
            ->action('Xem đơn hàng', url("/orders/{$this->order->id}"))
            ->line('Cảm ơn bạn đã mua hàng!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->code,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
            'message' => "Đơn hàng #{$this->order->code} đã được tạo thành công",
        ];
    }
}
