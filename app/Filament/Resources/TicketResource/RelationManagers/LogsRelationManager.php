<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

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
                Tables\Columns\TextColumn::make('changed_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cambiado por')
                    ->sortable(),

                Tables\Columns\TextColumn::make('previous_status')
                    ->label('Estado Anterior')
                    ->badge(),

                Tables\Columns\TextColumn::make('new_status')
                    ->label('Nuevo Estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('previous_priority')
                    ->label('Prioridad Anterior')
                    ->badge(),

                Tables\Columns\TextColumn::make('new_priority')
                    ->label('Nueva Prioridad')
                    ->badge(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departamento')
                    ->formatStateUsing(fn ($state) => $state ?? '-'),

                Tables\Columns\TextColumn::make('change_reason')
                    ->label('Razón del Cambio')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Sin acciones de creación ya que los logs son automáticos
            ])
            ->actions([
                // Sin acciones de edición/eliminación
            ])
            ->bulkActions([
                // Sin acciones masivas
            ])
            ->defaultSort('changed_at', 'desc')
            ->paginated(false);
    }
}
