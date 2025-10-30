<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Observers\TicketObserver;
use App\Observers\TicketCommentObserver;
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
        // Registrar observers
        Ticket::observe(TicketObserver::class);
        TicketComment::observe(TicketCommentObserver::class);

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
