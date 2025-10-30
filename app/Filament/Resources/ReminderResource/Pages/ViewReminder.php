<?php

namespace App\Filament\Resources\ReminderResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\ReminderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReminder extends ViewRecord
{
    protected static string $resource = ReminderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => $this->getResource()::canEdit($this->getRecord())),
            DeleteAction::make()
                ->visible(fn () => $this->getResource()::canDelete($this->getRecord())),
        ];
    }
}
