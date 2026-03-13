<?php

namespace App\Domain\Order\Notifications;

use App\Infrastructure\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusMessages = [
            'pending' => 'đang chờ xử lý',
            'processing' => 'đang xử lý',
            'shipped' => 'đang giao hàng',
            'delivered' => 'đã giao hàng',
            'cancelled' => 'đã hủy',
        ];

        $message = (new MailMessage)
            ->subject("Cập nhật trạng thái đơn hàng #{$this->order->code}")
            ->greeting("Xin chào {$notifiable->name}!")
            ->line("Đơn hàng #{$this->order->code} của bạn đã được cập nhật.")
            ->line('Trạng thái: '.($statusMessages[$this->newStatus] ?? $this->newStatus));

        if ($this->newStatus === 'shipped') {
            $message->line('Đơn hàng của bạn đang trên đường giao đến.');
        } elseif ($this->newStatus === 'delivered') {
            $message->line('Đơn hàng đã được giao thành công. Cảm ơn bạn đã mua hàng!');
        } elseif ($this->newStatus === 'cancelled') {
            $message->line('Đơn hàng đã bị hủy. Nếu có thắc mắc, vui lòng liên hệ với chúng tôi.');
        }

        return $message->action('Xem đơn hàng', url("/orders/{$this->order->id}"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->code,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Đơn hàng #{$this->order->code} đã chuyển sang trạng thái: {$this->newStatus}",
        ];
    }
}
