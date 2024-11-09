<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->getResource()::canEdit($this->getRecord())),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->getResource()::canDelete($this->getRecord())),
            Actions\RestoreAction::make()
                ->visible(fn () => Auth::user()->role === \App\Enums\UserRole::SuperAdmin),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => Auth::user()->role === \App\Enums\UserRole::SuperAdmin),
        ];
    }
}
