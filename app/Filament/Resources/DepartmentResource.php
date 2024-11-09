<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\StatusGlobal;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Department Information')
                    ->description('Basic department details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Department Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label('Department Code')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->placeholder('E.g., SEDE001')
                            ->helperText('Use uppercase letters and numbers only')
                            ->regex('/^[A-Z0-9]+$/')
                            ->extraInputAttributes(['style' => 'text-transform: uppercase']),

                        Forms\Components\Select::make('status')
                            ->label('Department Status')
                            ->options(StatusGlobal::class)
                            ->required()
                            ->default(StatusGlobal::Active),

                        Forms\Components\TextInput::make('email')
                            ->label('Contact Email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Contact Phone')
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        Forms\Components\Textarea::make('address')
                            ->label('Physical Address')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Department Description')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('tickets_count')
                    ->label('Tickets')
                    ->counts('tickets')
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(StatusGlobal::class)
                    ->label('Filter by Status'),

                Tables\Filters\TrashedFilter::make()
                    ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn() => Auth::user()->role !== UserRole::UserWeb),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                    Tables\Actions\RestoreAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                    Tables\Actions\ForceDeleteAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'view' => Pages\ViewDepartment::route('/{record}'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
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
        return static::getModel()::count() > 10 ? 'warning' : 'success';
    }

    public static function canCreate(): bool
    {
        return Auth::user()->role === UserRole::SuperAdmin ||
            Auth::user()->role === UserRole::Admin;
    }
}
