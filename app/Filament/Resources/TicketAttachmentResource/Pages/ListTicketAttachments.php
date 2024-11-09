<?php

namespace App\Filament\Resources\TicketAttachmentResource\Pages;

use App\Filament\Resources\TicketAttachmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketAttachments extends ListRecords
{
    protected static string $resource = TicketAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
