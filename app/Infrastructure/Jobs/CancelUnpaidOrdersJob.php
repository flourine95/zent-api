<?php

namespace App\Infrastructure\Jobs;

use App\Infrastructure\Models\Inventory;
use App\Infrastructure\Models\InventoryReservation;
use App\Infrastructure\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelUnpaidOrdersJob implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function handle(): void
    {
        $timeoutMinutes = (int) config('order.auto_cancel_minutes', 30);

        $orders = Order::where('status', 'pending')
            ->where('payment_status', 'unpaid')
            ->where('created_at', '<', now()->subMinutes($timeoutMinutes))
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            DB::transaction(function () use ($order, &$count) {
                $reservations = InventoryReservation::where('order_id', $order->id)
                    ->where('status', 'pending')
                    ->get();

                foreach ($reservations as $reservation) {
                    Inventory::where('id', $reservation->inventory_id)
                        ->lockForUpdate()
                        ->firstOrFail()
                        ->increment('quantity', $reservation->quantity);

                    $reservation->update(['status' => 'released']);
                }

                $order->update(['status' => 'cancelled']);
                $count++;
            });
        }

        if ($count > 0) {
            Log::info("Auto-cancelled {$count} unpaid orders.");
        }
    }
}
