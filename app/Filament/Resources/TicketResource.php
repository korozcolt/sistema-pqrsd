<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\TicketResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\LogsRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\RemindersRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\TagsRelationManager;
use App\Filament\Resources\TicketResource\Pages\ListTickets;
use App\Filament\Resources\TicketResource\Pages\CreateTicket;
use App\Filament\Resources\TicketResource\Pages\ViewTicket;
use App\Filament\Resources\TicketResource\Pages\EditTicket;
use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
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
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-ticket';
    protected static string | \UnitEnum | null $navigationGroup = 'Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'ticket_number';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ticket Information')
                    ->description('Basic ticket details')
                    ->schema([
                        TextInput::make('ticket_number')
                            ->label('Ticket Number')
                            ->default(fn () => 'TK-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Type')
                            ->options(TicketType::class)
                            ->required()
                            ->native(false),

                        Select::make('priority')
                            ->label('Priority')
                            ->options(Priority::class)
                            ->required()
                            ->default(Priority::Medium)
                            ->native(false),

                        Select::make('status')
                            ->label('Status')
                            ->options(StatusTicket::class)
                            ->required()
                            ->default(StatusTicket::Pending)
                            ->disabled(fn () => Auth::user()->role === UserRole::UserWeb)
                            ->native(false),

                        Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('user_id')
                            ->label('Created By')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => Auth::id())
                            ->disabled(fn () => Auth::user()->role === UserRole::UserWeb),

                        RichEditor::make('description')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('SLA Information')
                    ->description('Service Level Agreement details')
                    ->schema([
                        DateTimePicker::make('response_due_date')
                            ->label('Response Due Date')
                            ->disabled(),

                        DateTimePicker::make('resolution_due_date')
                            ->label('Resolution Due Date')
                            ->disabled(),

                        DateTimePicker::make('first_response_at')
                            ->label('First Response')
                            ->disabled(),

                        DateTimePicker::make('resolution_at')
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
                TextColumn::make('ticket_number')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('priority')
                    ->badge(),

                TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

                SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Filter by Department')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make()
                    ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
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

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
            AttachmentsRelationManager::class,
            LogsRelationManager::class,
            RemindersRelationManager::class,
            TagsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view' => ViewTicket::route('/{record}'),
            'edit' => EditTicket::route('/{record}/edit'),
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
