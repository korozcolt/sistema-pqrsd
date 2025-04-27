<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Observers\TicketObserver;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar el observador para el modelo Ticket
        Ticket::observe(TicketObserver::class);

        // Extender validaciones
        Validator::extend('recaptcha', Recaptcha::class);

        // Aplicar compresión gzip si está disponible
        if (extension_loaded('zlib') && !ob_get_level()) {
            ob_start('ob_gzhandler');
        }

        // Cacheo de respuestas en entornos de producción
        if (!app()->isLocal()) {
            config(['cache.default' => 'redis']);
        }
    }
}
