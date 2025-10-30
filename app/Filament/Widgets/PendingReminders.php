<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use App\Models\Reminder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use App\Enums\ReminderType;

class PendingReminders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Reminder::query()
                    ->where('sent_to', Auth::id())
                    ->where('is_read', false)
                    ->latest('sent_at')
            )
            ->columns([
                TextColumn::make('ticket.ticket_number')
                    ->label('Ticket')
                    ->searchable()
                    ->url(fn ($record) => route('filament.admin.resources.tickets.view', $record->ticket_id)),

                TextColumn::make('ticket.title')
                    ->label('Título del Ticket')
                    ->limit(30),

                TextColumn::make('reminder_type')
                    ->label('Tipo de Recordatorio')
                    ->badge(),

                TextColumn::make('sent_at')
                    ->label('Enviado')
                    ->dateTime()
                    ->sortable()
                    ->description(fn ($record) => $record->sent_at->diffForHumans()),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Ver Ticket')
                    ->icon('heroicon-m-eye')
                    ->url(fn ($record) => route('filament.admin.resources.tickets.view', $record->ticket_id))
                    ->openUrlInNewTab(),

                Action::make('mark_read')
                    ->label('Marcar como Leído')
                    ->icon('heroicon-m-check')
                    ->action(fn ($record) => $record->markAsRead())
                    ->color('success'),
            ])
            ->toolbarActions([
                BulkAction::make('mark_all_read')
                    ->label('Marcar Seleccionados como Leídos')
                    ->action(fn ($records) => $records->each->markAsRead())
                    ->deselectRecordsAfterCompletion()
                    ->icon('heroicon-m-check')
                    ->color('success'),
            ])
            ->defaultSort('sent_at', 'desc')
            ->heading('Recordatorios Pendientes')
            ->description('Recordatorios no leídos de tus tickets asignados')
            ->emptyStateHeading('No hay recordatorios pendientes')
            ->emptyStateDescription('No tienes recordatorios sin leer en este momento.')
            ->emptyStateIcon('heroicon-o-bell-slash')
            ->poll('30s'); // Actualiza cada 30 segundos
    }
}
