<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:sync-partner-tags')
    ->hourly()
    ->withoutOverlapping();
Schedule::command('online-systems:renew-due')
    ->dailyAt('00:05')
    ->timezone('UTC')
    ->withoutOverlapping();
