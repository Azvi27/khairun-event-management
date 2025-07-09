<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 🎁 BIRTHDAY SURPRISE AUTOMATION
Schedule::command('surprises:reveal')
        ->everyMinute()
        ->withoutOverlapping()
        ->runInBackground();