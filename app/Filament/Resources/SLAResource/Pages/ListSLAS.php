<?php

namespace App\Filament\Resources\SLAResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\SLAResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSLAS extends ListRecords
{
    protected static string $resource = SLAResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
