<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cache:clear', function () {
    \Illuminate\Support\Facades\Cache::put('cache_cleared', true);
    $this->call('cache:clear');
    $this->info('Cache cleared and flag set.');
})->purpose('Clear cache and set cache_cleared flag.');
