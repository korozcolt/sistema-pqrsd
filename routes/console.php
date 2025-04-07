<?php

use App\Jobs\ProcessTicketReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(new ProcessTicketReminders)->hourly();
//Schedule::command('sitemap:generate')->daily();
Schedule::command('log:info "Cron is working: '.date('Y-m-d H:i:s').'"')->everyMinute();
Schedule::command('tickets:check-reminders')->everyFiveMinutes();
Schedule::command('tickets:mark-inactive')->daily(); // Revisar tickets inactivos diariamente
Schedule::command('tickets:close-inactive')->hourly(); // Cerrar tickets después del período de aviso
