<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReminderResource\Pages;
use App\Models\Reminder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class ReminderResource extends Resource
{
    protected static ?string $model = Reminder::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = 'Ticket Settings';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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

                        Forms\Components\TextInput::make('reminder_type')
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
                Tables\Columns\TextColumn::make('ticket.title')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Sent To')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reminder_type')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('read_at')
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
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ListReminders::route('/'),
            'create' => Pages\CreateReminder::route('/create'),
            'view' => Pages\ViewReminder::route('/{record}'),
            'edit' => Pages\EditReminder::route('/{record}/edit'),
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
