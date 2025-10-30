<?php

namespace App\Providers;

use App\Events\TicketCreatedEvent;
use App\Events\TicketStatusChanged;
use App\Listeners\CreateTicketLog;
use App\Listeners\CreateTicketReminder;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TicketCreatedEvent::class => [
            CreateTicketReminder::class,
        ],
        TicketStatusChanged::class => [
            CreateTicketLog::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
