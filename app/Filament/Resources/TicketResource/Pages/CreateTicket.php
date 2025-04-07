<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Events\TicketCreatedEvent;
use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function afterCreate(): void
    {
        $ticket = $this->record;

        event(new TicketCreatedEvent($ticket));
    }
}
