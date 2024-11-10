<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';
    protected static ?string $title = 'Ticket History';
    protected static ?string $recordTitleAttribute = 'changed_at';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('changed_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Changed By')
                    ->sortable(),

                Tables\Columns\TextColumn::make('previous_status')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('new_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'resolved' => 'success',
                        'rejected' => 'danger',
                        'closed' => 'gray',
                        'reopened' => 'warning',
                        default => 'primary',
                    }),

                Tables\Columns\TextColumn::make('change_reason')
                    ->limit(50),
            ])
            ->defaultSort('changed_at', 'desc')
            ->paginated(false);
    }
}
