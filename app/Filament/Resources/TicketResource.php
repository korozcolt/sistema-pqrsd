<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\StatusTicket;
use App\Enums\Priority;
use App\Enums\TicketType;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'ticket_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ticket Information')
                    ->description('Basic ticket details')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_number')
                            ->label('Ticket Number')
                            ->default(fn () => 'TK-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options(TicketType::class)
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('priority')
                            ->label('Priority')
                            ->options(Priority::class)
                            ->required()
                            ->default(Priority::Medium)
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(StatusTicket::class)
                            ->required()
                            ->default(StatusTicket::Pending)
                            ->disabled(fn () => Auth::user()->role === UserRole::UserWeb)
                            ->native(false),

                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('user_id')
                            ->label('Created By')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => Auth::id())
                            ->disabled(fn () => Auth::user()->role === UserRole::UserWeb),

                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('SLA Information')
                    ->description('Service Level Agreement details')
                    ->schema([
                        Forms\Components\DateTimePicker::make('response_due_date')
                            ->label('Response Due Date')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('resolution_due_date')
                            ->label('Resolution Due Date')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('first_response_at')
                            ->label('First Response')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('resolution_at')
                            ->label('Resolution Date')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->visible(fn () => Auth::user()->role !== UserRole::UserWeb),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('type')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('priority')
                    ->badge(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(StatusTicket::class)
                    ->label('Filter by Status'),

                Tables\Filters\SelectFilter::make('priority')
                    ->options(Priority::class)
                    ->label('Filter by Priority'),

                Tables\Filters\SelectFilter::make('type')
                    ->options(TicketType::class)
                    ->label('Filter by Type'),

                Tables\Filters\SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Filter by Department')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TrashedFilter::make()
                    ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\AttachmentsRelationManager::class,
            RelationManagers\LogsRelationManager::class,
            RelationManagers\RemindersRelationManager::class,
            RelationManagers\TagsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        // Filtrar tickets según el rol del usuario
        if (Auth::user()->role === UserRole::UserWeb) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 20 ? 'warning' : 'success';
    }

    public static function canCreate(): bool
    {
        return true; // Todos pueden crear tickets
    }

    public static function canEdit(Model $record): bool
    {
        if (Auth::user()->role === UserRole::UserWeb) {
            return false;
        }
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->role === UserRole::SuperAdmin;
    }
}
