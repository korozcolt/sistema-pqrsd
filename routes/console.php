<?php

use App\Jobs\ProcessTicketReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Job de procesamiento de recordatorios (cada hora)
Schedule::job(new ProcessTicketReminders)->hourly();

// Comandos de gestión de tickets inactivos
Schedule::command('tickets:mark-inactive')->daily(); // Revisar tickets inactivos diariamente
Schedule::command('tickets:close-inactive')->hourly(); // Cerrar tickets después del período de aviso

// Comandos opcionales (comentados)
//Schedule::command('sitemap:generate')->daily();
//Schedule::command('log:info "Cron is working: '.date('Y-m-d H:i:s').'"')->everyMinute();
