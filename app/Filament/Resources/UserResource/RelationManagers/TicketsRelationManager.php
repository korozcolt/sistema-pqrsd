<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\TicketStatus;
use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Enums\UserRole;
use Filament\Forms\Components\Section;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        StatusTicket::Pending->value => 'warning',
                        StatusTicket::In_Progress->value => 'info',
                        StatusTicket::Resolved->value => 'success',
                        StatusTicket::Rejected->value => 'danger',
                        StatusTicket::Closed->value => 'gray',
                        StatusTicket::Reopened->value => 'warning',
                        default => 'primary',
                    }),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Priority::Low->value => 'info',
                        Priority::Medium->value => 'warning',
                        Priority::High->value => 'danger',
                        Priority::Urgent->value => 'danger',
                        default => 'primary',
                    }),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        TicketType::Petition->value => 'info',
                        TicketType::Complaint->value => 'warning',
                        TicketType::Claim->value => 'danger',
                        TicketType::Suggestion->value => 'success',
                        default => 'primary',
                    }),

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
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => Auth::user()->role !== UserRole::UserWeb),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn () => Auth::user()->role !== UserRole::UserWeb),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
