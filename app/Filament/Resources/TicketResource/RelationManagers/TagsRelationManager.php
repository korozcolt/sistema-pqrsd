<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class TagsRelationManager extends RelationManager
{
    protected static string $relationship = 'tags';
    protected static ?string $title = 'Ticket Tags';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('name')
                    ->label('Tag')
                    ->relationship('tag', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('description')
                            ->maxLength(255),
                        ColorPicker::make('color')
                            ->default('#000000'),
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->badge()
                    ->color(fn ($record) => $record->color ?? 'gray'),

                TextColumn::make('description')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->visible(fn () => Auth::user()->role !== UserRole::UserWeb),
            ])
            ->recordActions([
                DetachAction::make()
                    ->visible(fn () => Auth::user()->role !== UserRole::UserWeb),
            ]);
    }
}
