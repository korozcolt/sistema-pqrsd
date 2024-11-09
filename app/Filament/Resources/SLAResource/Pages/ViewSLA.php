<?php

namespace App\Filament\Resources\SLAResource\Pages;

use App\Filament\Resources\SLAResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSLA extends ViewRecord
{
    protected static string $resource = SLAResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->getResource()::canEdit($this->getRecord())),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->getResource()::canDelete($this->getRecord())),
        ];
    }
}
