<?php

namespace App\Infrastructure\Jobs;

use App\Domain\Inventory\Services\InventoryCacheServiceInterface;
use App\Infrastructure\Models\Inventory;
use App\Infrastructure\Models\InventoryReservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredReservationsJob implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function handle(InventoryCacheServiceInterface $inventoryCache): void
    {
        $expired = InventoryReservation::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        $variantQuantities = [];

        foreach ($expired as $reservation) {
            DB::transaction(function () use ($reservation, &$count, &$variantQuantities) {
                Inventory::where('id', $reservation->inventory_id)
                    ->lockForUpdate()
                    ->firstOrFail()
                    ->increment('quantity', $reservation->quantity);

                $reservation->update(['status' => 'released']);
                $count++;

                $variantId = $reservation->product_variant_id;
                $variantQuantities[$variantId] = ($variantQuantities[$variantId] ?? 0) + $reservation->quantity;
            });
        }

        // Sync Redis after all DB transactions succeed
        if (! empty($variantQuantities)) {
            $inventoryCache->incrementBatch($variantQuantities);
        }

        if ($count > 0) {
            Log::info("Released {$count} expired inventory reservations.");
        }
    }
}
