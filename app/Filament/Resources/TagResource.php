<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Ticket Settings';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tag Information')
                    ->description('Manage tag details here.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        ColorPicker::make('color')
                            ->required()
                            ->default('#000000'),

                        Textarea::make('description')
                            ->maxLength(1000),
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

                Tables\Columns\ColorColumn::make('color')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tickets_count')
                    ->label('Tickets')
                    ->counts('tickets')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            ->defaultSort('name');
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
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'view' => Pages\ViewTag::route('/{record}'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
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
