<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Scheduler: Sinkronisasi Google Sheets setiap 5 menit ────────────────────
Schedule::command('sync:sheet')->everyFiveMinutes();

