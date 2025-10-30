<?php

namespace App\Filament\Resources\ReminderResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ReminderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReminders extends ListRecords
{
    protected static string $resource = ReminderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
