<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ReminderResource\Pages\ListReminders;
use App\Filament\Resources\ReminderResource\Pages\CreateReminder;
use App\Filament\Resources\ReminderResource\Pages\ViewReminder;
use App\Filament\Resources\ReminderResource\Pages\EditReminder;
use App\Filament\Resources\ReminderResource\Pages;
use App\Models\Reminder;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class ReminderResource extends Resource
{
    protected static ?string $model = Reminder::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bell';
    protected static string | \UnitEnum | null $navigationGroup = 'Ticket Settings';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reminder Information')
                    ->description('Manage reminder details here.')
                    ->schema([
                        Select::make('ticket_id')
                            ->relationship('ticket', 'title')
                            ->required()
                            ->searchable(),

                        Select::make('sent_to')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable(),

                        TextInput::make('reminder_type')
                            ->required()
                            ->maxLength(255),

                        DateTimePicker::make('sent_at')
                            ->label('Sent At')
                            ->required(),

                        Toggle::make('is_read')
                            ->label('Read')
                            ->helperText('Mark as read')
                            ->default(false)
                            ->required(),

                        DateTimePicker::make('read_at')
                            ->label('Read At')
                            ->withoutSeconds(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket.title')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Sent To')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reminder_type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),

                IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('read_at')
                    ->label('Read At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_read')
                    ->options([
                        true => 'Read',
                        false => 'Unread',
                    ])
                    ->label('Read Status'),

                SelectFilter::make('sent_to')
                    ->relationship('user', 'name')
                    ->label('Sent To'),

                TernaryFilter::make('overdue')
                    ->label('Overdue')
                    ->indicator('Overdue'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->defaultSort('sent_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReminders::route('/'),
            'create' => CreateReminder::route('/create'),
            'view' => ViewReminder::route('/{record}'),
            'edit' => EditReminder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 20 ? 'danger' : 'success';
    }

    public static function canCreate(): bool
    {
        return Auth::user()->role === UserRole::SuperAdmin ||
            Auth::user()->role === UserRole::Admin;
    }
}
