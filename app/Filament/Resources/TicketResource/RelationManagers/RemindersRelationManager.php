<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RemindersRelationManager extends RelationManager
{
    protected static string $relationship = 'reminders';
    protected static ?string $title = 'Reminders';
    protected static ?string $recordTitleAttribute = 'reminder_type';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reminder_type')
                    ->badge(),

                // Corrección aquí - usar el nombre completo de la relación
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Sent To'),

                Tables\Columns\IconColumn::make('is_read')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('read_at')
                    ->dateTime()
                    ->placeholder('Not read yet'),
            ])
            ->defaultSort('sent_at', 'desc')
            ->paginated(false);
    }
}
