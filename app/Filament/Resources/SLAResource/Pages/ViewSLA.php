<?php

namespace App\Filament\Resources\SLAResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\SLAResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSLA extends ViewRecord
{
    protected static string $resource = SLAResource::class;

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
