<?php

namespace App\Infrastructure\Jobs;

use App\Domain\Inventory\Services\InventoryCacheServiceInterface;
use App\Infrastructure\Models\Inventory;
use App\Infrastructure\Models\InventoryReservation;
use App\Infrastructure\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class PersistOrderJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 10;

    private const SNAPSHOT_PREFIX = 'order:snapshot:';

    /**
     * @param  array<string, mixed>  $orderData
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, int>  $variantQuantities
     */
    public function __construct(
        private readonly string $orderId,
        private readonly string $userId,
        private readonly array $orderData,
        private readonly array $items,
        private readonly array $variantQuantities,
    ) {}

    public function handle(InventoryCacheServiceInterface $inventoryCache): void
    {
        $snapshotKey = self::SNAPSHOT_PREFIX.$this->orderId;

        // Idempotency: skip if already persisted
        if (! Redis::exists($snapshotKey)) {
            Log::info("PersistOrderJob: order {$this->orderId} already persisted or snapshot expired, skipping.");

            return;
        }

        DB::transaction(function () use ($snapshotKey) {
            $order = Order::create(array_merge($this->orderData, ['id' => $this->orderId]));

            foreach ($this->items as $item) {
                $order->items()->create($item);

                $inventory = Inventory::where('warehouse_id', $item['warehouse_id'])
                    ->where('product_variant_id', $item['product_variant_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $inventory->decrement('quantity', $item['quantity']);

                InventoryReservation::create([
                    'inventory_id' => $inventory->id,
                    'order_id' => $order->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(30),
                ]);
            }

            // Remove snapshot after successful persist
            Redis::del($snapshotKey);
        });

        SendOrderConfirmationJob::dispatch($this->userId, $this->orderData);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("PersistOrderJob failed for order {$this->orderId}: {$exception->getMessage()}");

        // Compensate: restore Redis inventory
        app(InventoryCacheServiceInterface::class)->incrementBatch($this->variantQuantities);

        // Clean up snapshot
        Redis::del(self::SNAPSHOT_PREFIX.$this->orderId);

        // Notify user their order failed
        CompensationNotificationJob::dispatch($this->userId, $this->orderId, $this->orderData['code']);
    }
}
