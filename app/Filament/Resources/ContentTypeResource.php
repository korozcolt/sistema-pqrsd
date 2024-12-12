<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentTypeResource\Pages;
use App\Models\ContentType;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Enums\{ComponentType, StatusGlobal};
use App\Filament\Resources\ContentTypeResource\RelationManagers\ContentsRelationManager;
use Filament\Forms\Components\{TextInput, Select, Textarea, KeyValue};
use Filament\Tables\Columns\{TextColumn, IconColumn};
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;

class ContentTypeResource extends Resource
{
    protected static ?string $model = ContentType::class;
    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Tipo de Contenido';
    protected static ?string $pluralModelLabel = 'Tipos de Contenidos';

    public static function form(Form $form): Form
    {
        return $form->schema([
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

            Select::make('component_type')
                ->label('Tipo de componente')
                ->options(ComponentType::class)
                ->required(),

            Textarea::make('description')
                ->label('Descripción')
                ->maxLength(500)
                ->columnSpanFull(),

            KeyValue::make('schema')
                ->label('Esquema')
                ->addActionLabel('Agregar Campo')
                ->keyLabel('Campo')
                ->valueLabel('Tipo')
                ->columnSpanFull(),

            KeyValue::make('validation_rules')
                ->label('Reglas de validación')
                ->addActionLabel('Agregar Regla')
                ->keyLabel('Campo')
                ->valueLabel('Reglas')
                ->columnSpanFull(),

            KeyValue::make('default_settings')
                ->label('Configuración predeterminada')
                ->addActionLabel('Agregar Configuración')
                ->keyLabel('Propiedad')
                ->valueLabel('Valor por Defecto')
                ->columnSpanFull(),

            Select::make('status')
                ->label('Estado')
                ->options(StatusGlobal::class)
                ->required()
                ->default(StatusGlobal::Active),
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

                TextColumn::make('component_type')
                    ->label('Tipo de componente')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                TextColumn::make('contents_count')
                    ->counts('contents')
                    ->label('Contenidos'),

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
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('component_type')
                    ->label('Tipo de componente')
                    ->options(ComponentType::class),

                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(StatusGlobal::class),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('¡Actualizado!')
                            ->body('El tipo de contenido se actualizó con éxito.')
                    ),
                Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('¡Eliminado!')
                            ->body('El tipo de contenido se eliminó correctamente.')
                    ),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ContentsRelationManager::class,
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
            'index' => Pages\ListContentTypes::route('/'),
            'create' => Pages\CreateContentType::route('/create'),
            'edit' => Pages\EditContentType::route('/{record}/edit'),
        ];
    }
}
