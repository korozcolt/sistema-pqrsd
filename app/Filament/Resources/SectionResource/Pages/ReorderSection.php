<?php
namespace App\Filament\Resources\SectionResource\Pages;

use App\Filament\Resources\SectionResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ReorderSection extends Page
{
    protected static string $resource = SectionResource::class;

    public function reorderAction(): void
    {
        $this->authorize('reorder');
        $this->callHook('beforeReorder');
        $records = $this->getRecords();

        foreach ($records as $record) {
            $record->save();
        }

        $this->notify('success', 'Secciones reordenadas correctamente');
        $this->callHook('afterReorder');
    }

    protected function getGroupedRecordsProperty(): array
    {
        return $this->getRecords()
            ->groupBy('page.title')
            ->map(fn (Collection $records) => $records->sortBy('order'))
            ->all();
    }

    protected function getRecords(): Collection
    {
        return static::getResource()::getEloquentQuery()
            ->orderBy('order')
            ->get();
    }
}
