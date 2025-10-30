<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\StatusTicket;
use App\Enums\Priority;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';
    protected static ?string $title = 'Historial de Cambios';
    protected static ?string $recordTitleAttribute = 'change_reason';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('changed_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Cambiado por')
                    ->sortable(),

                TextColumn::make('previous_status')
                    ->label('Estado Anterior')
                    ->badge(),

                TextColumn::make('new_status')
                    ->label('Nuevo Estado')
                    ->badge(),

                TextColumn::make('previous_priority')
                    ->label('Prioridad Anterior')
                    ->badge(),

                TextColumn::make('new_priority')
                    ->label('Nueva Prioridad')
                    ->badge(),

                TextColumn::make('department.name')
                    ->label('Departamento')
                    ->formatStateUsing(fn ($state) => $state ?? '-'),

                TextColumn::make('change_reason')
                    ->label('Razón del Cambio')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Sin acciones de creación ya que los logs son automáticos
            ])
            ->recordActions([
                // Sin acciones de edición/eliminación
            ])
            ->toolbarActions([
                // Sin acciones masivas
            ])
            ->defaultSort('changed_at', 'desc')
            ->paginated(false);
    }
}
