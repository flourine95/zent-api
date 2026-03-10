<?php

namespace App\Jobs;

use App\Infrastructure\Models\Order;
use App\Models\Setting;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelUnpaidOrders implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function handle(): void
    {
        $timeoutMinutes = Setting::get('order_auto_cancel_minutes', 30);

        $orders = Order::where('status', 'pending')
            ->where('payment_status', 'pending')
            ->where('created_at', '<', now()->subMinutes($timeoutMinutes))
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            DB::transaction(function () use ($order) {
                $oldStatus = $order->status;

                // Cancel order
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                ]);

                // Release inventory reservations
                $order->inventoryReservations()->update([
                    'released_at' => now(),
                ]);

                // Send notification
                $order->user->notify(
                    new OrderStatusChangedNotification($order, $oldStatus, 'cancelled')
                );
            });

            $count++;
        }

        if ($count > 0) {
            Log::info("Auto-cancelled {$count} unpaid orders");
        }
    }
}
