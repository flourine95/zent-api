<?php

namespace App\Console\Commands;

use App\Infrastructure\Models\Inventory;
use App\Infrastructure\Models\InventoryReservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReleaseExpiredReservations extends Command
{
    protected $signature = 'inventory:release-expired';

    protected $description = 'Release expired inventory reservations and restore stock quantities';

    public function handle(): int
    {
        $expired = InventoryReservation::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No expired reservations found.');

            return self::SUCCESS;
        }

        $released = 0;

        foreach ($expired as $reservation) {
            DB::transaction(function () use ($reservation, &$released) {
                Inventory::where('id', $reservation->inventory_id)
                    ->lockForUpdate()
                    ->firstOrFail()
                    ->increment('quantity', $reservation->quantity);

                $reservation->update(['status' => 'released']);
                $released++;
            });
        }

        $this->info("Released {$released} expired reservations.");

        return self::SUCCESS;
    }
}
