<?php

namespace App\Filament\Resources\TicketAttachmentResource\Pages;

use App\Filament\Resources\TicketAttachmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketAttachment extends EditRecord
{
    protected static string $resource = TicketAttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
