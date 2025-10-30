<?php

namespace App\Filament\Resources\ReminderResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ReminderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReminder extends EditRecord
{
    protected static string $resource = ReminderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
