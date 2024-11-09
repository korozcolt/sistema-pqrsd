<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\UserRole;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Support\Collection;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Information')
                    ->description('Manage user account details here.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->password()
                            ->required(fn(string $context): bool => $context === 'create')
                            ->dehydrateStateUsing(
                                fn($state) =>
                                !empty($state) ? Hash::make($state) : null
                            )
                            ->dehydrated(fn($state) => filled($state))
                            ->label(
                                fn(string $context): string =>
                                $context === 'edit' ? 'New Password' : 'Password'
                            ),

                        Select::make('role')
                            ->options(UserRole::class)
                            ->enum(UserRole::class)
                            ->required()
                            ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin)
                            ->default(UserRole::UserWeb)
                            ->disabled(
                                fn(?Model $record) =>
                                $record?->id === Auth::id() ||
                                    ($record?->role === UserRole::SuperAdmin &&
                                        Auth::user()->role !== UserRole::SuperAdmin)
                            ),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                    Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'superadmin' => 'Super Admin',
                            'admin' => 'Administrator',
                            'receptionist' => 'Receptionist',
                            'user_web' => 'Web User',
                            default => $state,
                        };
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(UserRole::class)
                    ->label('Filter by Role'),

                TrashedFilter::make()
                    ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(
                            fn(Model $record) =>
                            Auth::user()->role === UserRole::SuperAdmin ||
                                (Auth::user()->role === UserRole::Admin &&
                                    $record->role !== UserRole::SuperAdmin)
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->visible(
                            fn(Model $record) =>
                            $record->id !== Auth::id() &&
                                Auth::user()->role === UserRole::SuperAdmin
                        ),
                    Tables\Actions\RestoreAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                    Tables\Actions\ForceDeleteAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin)
                        ->action(function (Collection $records) {
                            $records = $records->filter(function ($record) {
                                return $record->id !== Auth::id() &&
                                    $record->role !== UserRole::SuperAdmin->value;
                            });

                            $records->each->delete();
                        }),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TicketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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

    public static function canEdit(Model $record): bool
    {
        if (Auth::user()->role === UserRole::SuperAdmin) {
            return true;
        }

        if (Auth::user()->role === UserRole::Admin) {
            return $record->role !== UserRole::SuperAdmin;
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if ($record->id === Auth::id()) {
            return false;
        }

        if (Auth::user()->role === UserRole::SuperAdmin) {
            return true;
        }

        if (Auth::user()->role === UserRole::Admin) {
            return $record->role !== UserRole::SuperAdmin &&
                $record->role !== UserRole::Admin;
        }

        return false;
    }
}
