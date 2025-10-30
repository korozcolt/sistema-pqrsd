<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => Auth::user()->role !== UserRole::UserWeb),
            DeleteAction::make()
                ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
            RestoreAction::make()
                ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
            ForceDeleteAction::make()
                ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
        ];
    }
}
