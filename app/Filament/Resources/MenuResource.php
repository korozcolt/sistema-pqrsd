<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Enums\{MenuLocation, StatusGlobal};
use Filament\Forms\Components\{TextInput, Select, KeyValue, Card, Section};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use App\Filament\Resources\MenuResource\RelationManagers\MenuItemsRelationManager;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Menú';
    protected static ?string $pluralModelLabel = 'Menús';

    public static function form(Form $form): Form
{
    return $form->schema([
        Section::make()->schema([
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->afterStateUpdated(function (string $state, $set) {
                    $set('slug', str($state)->slug());
                }),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->rules(['alpha_dash']),

            Select::make('location')
                ->label('Ubicación')
                ->options(MenuLocation::class)
                ->required(),

            Select::make('status')
                ->label('Estado')
                ->options(StatusGlobal::class)
                ->required()
                ->default(StatusGlobal::Active),
        ])->columns(2),

        Section::make()->schema([
            KeyValue::make('settings')
                ->label('Configuraciones')
                ->addActionLabel('Agregar configuración')
                ->keyLabel('Propiedad')
                ->valueLabel('Valor')
                ->columnSpanFull(),
        ]),
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('location')
                    ->label('Ubicación')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Elementos')
                    ->counts('items'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('location')
                    ->label('Ubicación')
                    ->options(MenuLocation::class),

                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(StatusGlobal::class),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Menú actualizado')
                            ->body('El menú se actualizó correctamente.')
                    ),
                \Filament\Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Menú eliminado')
                            ->body('El menú se eliminó correctamente.')
                    ),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            MenuItemsRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }

}
