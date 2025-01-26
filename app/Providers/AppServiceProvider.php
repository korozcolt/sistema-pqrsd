<?php

namespace App\Providers;

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
        \App\Models\Ticket::observe(\App\Observers\TicketObserver::class);
        Validator::extend('recaptcha', Recaptcha::class);

        if (extension_loaded('zlib') && !ob_get_level()) {
            ob_start('ob_gzhandler');
        }
        // Cacheo de respuestas
        if (!app()->isLocal()) {
            config(['cache.default' => 'redis']);
        }
    }
}
