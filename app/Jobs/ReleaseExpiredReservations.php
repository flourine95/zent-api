<?php

namespace App\Jobs;

use App\Models\InventoryReservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredReservations implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function handle(): void
    {
        $expiredReservations = InventoryReservation::where('expires_at', '<', now())
            ->whereNull('released_at')
            ->get();

        $count = 0;

        foreach ($expiredReservations as $reservation) {
            $reservation->update(['released_at' => now()]);
            $count++;
        }

        if ($count > 0) {
            Log::info("Released {$count} expired inventory reservations");
        }
    }
}
