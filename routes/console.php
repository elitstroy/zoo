<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Планировщик для GosstroyMonitoring
// Запуск каждый понедельник в 08:00
Schedule::command('gosstroy:run')
    ->daily()
    ->at('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/gosstroy/scheduler.log'));
