<?php

use App\Jobs\CancelUnpaidOrders;
use App\Jobs\ReleaseExpiredReservations;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule background jobs
Schedule::job(new ReleaseExpiredReservations)->everyMinute();
Schedule::job(new CancelUnpaidOrders)->everyFiveMinutes();
