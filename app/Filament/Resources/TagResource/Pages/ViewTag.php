<?php

namespace App\Filament\Resources\TagResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\TagResource;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ViewRecord;

class ViewTag extends ViewRecord
{
    protected static string $resource = TagResource::class;

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
