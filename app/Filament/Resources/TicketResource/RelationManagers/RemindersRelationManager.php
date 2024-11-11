<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class RemindersRelationManager extends RelationManager
{
    protected static string $relationship = 'reminders';
    protected static ?string $title = 'Recordatorios';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reminder_type')
                    ->label('Tipo')
                    ->badge() // Automáticamente usará el color, icono y label del enum
                    ->sortable(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Enviado')
                    ->dateTime()
                    ->sortable()
                    ->description(fn ($record) => $record->sent_at->diffForHumans()),

                Tables\Columns\IconColumn::make('is_read')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('read_at')
                    ->label('Leído el')
                    ->dateTime()
                    ->placeholder('Pendiente de lectura')
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('toggle_read')
                        ->icon(fn ($record) => $record->is_read ? 'heroicon-m-x-mark' : 'heroicon-m-check')
                        ->label(fn ($record) => $record->is_read ? 'Marcar como no leído' : 'Marcar como leído')
                        ->action(function ($record) {
                            $record->is_read ? $record->markAsUnread() : $record->markAsRead();
                        })
                        ->color(fn ($record) => $record->is_read ? 'gray' : 'success'),

                    Tables\Actions\DeleteAction::make()
                        ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_all_read')
                        ->label('Marcar como leídos')
                        ->action(fn ($records) => $records->each->markAsRead())
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-m-check')
                        ->color('success'),

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->defaultSort('sent_at', 'desc')
            ->emptyStateHeading('No hay recordatorios')
            ->emptyStateDescription('No hay recordatorios para este ticket.')
            ->emptyStateIcon('heroicon-o-bell-slash')
            ->poll('30s');
    }
}
