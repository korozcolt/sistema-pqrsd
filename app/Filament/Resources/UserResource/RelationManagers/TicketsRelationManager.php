<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\TicketStatus;
use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $title = 'User Tickets';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ticket Information')
                    ->schema([
                        TextInput::make('ticket_number')
                            ->default(fn () => 'TK-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->required(),

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('type')
                            ->options(TicketType::class)
                            ->enum(TicketType::class)
                            ->required(),

                        Select::make('priority')
                            ->options(Priority::class)
                            ->enum(Priority::class)
                            ->default(Priority::Medium)
                            ->required(),

                        RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('ticket_number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('priority')
                    ->badge(),

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusTicket::class)
                    ->label('Filter by Status'),

                SelectFilter::make('priority')
                    ->options(Priority::class)
                    ->label('Filter by Priority'),

                SelectFilter::make('type')
                    ->options(TicketType::class)
                    ->label('Filter by Type'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => Auth::user()->role !== UserRole::UserWeb),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(fn () => Auth::user()->role !== UserRole::UserWeb),
                    DeleteAction::make()
                        ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
