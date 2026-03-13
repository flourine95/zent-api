<?php

use App\Domain\Order\Jobs\CancelUnpaidOrdersJob;
use App\Domain\Order\Jobs\ReleaseExpiredReservationsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule background jobs
Schedule::job(new ReleaseExpiredReservationsJob)->everyMinute();
Schedule::job(new CancelUnpaidOrdersJob)->everyFiveMinutes();
