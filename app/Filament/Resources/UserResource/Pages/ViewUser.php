<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use App\Enums\UserRole;
use Filament\Actions\ForceDeleteAction;
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
            EditAction::make()
                ->visible(fn () => $this->getResource()::canEdit($this->getRecord())),
            DeleteAction::make()
                ->visible(fn () => $this->getResource()::canDelete($this->getRecord())),
            RestoreAction::make()
                ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
            ForceDeleteAction::make()
                ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
        ];
    }
}
