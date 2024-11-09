<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

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
            Actions\EditAction::make()
                ->visible(fn () => Auth::user()->role !== UserRole::UserWeb),
            Actions\DeleteAction::make()
                ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
            Actions\RestoreAction::make()
                ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
        ];
    }
}
