<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput, RichEditor, Select, Toggle, KeyValue};
use Filament\Tables\Columns\{TextColumn, IconColumn};
use Filament\Tables\Actions;
use App\Enums\StatusGlobal;
use App\Filament\Resources\ContentTypeResource\RelationManagers\ContentsRelationManager;
use App\Filament\Resources\PageResource\RelationManagers\SectionsRelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Pagina';
    protected static ?string $pluralModelLabel = 'Paginas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255)
                ->afterStateUpdated(fn ($state, callable $set) =>
                    $set('slug', Str::slug($state))),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('meta_description')
                ->label('Meta Descripción')
                ->maxLength(255)
                ->columnSpanFull(),

            TextInput::make('meta_keywords')
                ->label('Meta Palabras Clave')
                ->maxLength(255),

            Select::make('layout')
                ->label('Plantilla')
                ->options([
                    'default' => 'Por defecto',
                    'full-width' => 'Ancho completo',
                    'sidebar' => 'Con barra lateral',
                ])
                ->default('default'),

            KeyValue::make('settings')
                ->label('Configuración')
                ->keyLabel('Propiedad')
                ->valueLabel('Valor')
                ->columnSpanFull(),

            KeyValue::make('og_data')
                ->label('Datos OpenGraph')
                ->keyLabel('Propiedad')
                ->valueLabel('Contenido')
                ->columnSpanFull(),

            Toggle::make('searchable')
                ->label('¿Buscable?')
                ->default(true),

            Select::make('status')
                ->label('Estado')
                ->options(StatusGlobal::class)
                ->default(StatusGlobal::Active)
                ->required(),

            TextInput::make('order')
                ->label('Orden')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                TextColumn::make('sections_count')
                    ->counts('sections')
                    ->label('Secciones'),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(StatusGlobal::class),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('¡Página actualizada!')
                            ->body('La página se actualizó correctamente')
                    ),
                Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('¡Página eliminada!')
                            ->body('La página se eliminó correctamente')
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
            SectionsRelationManager::class,
            ContentsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', StatusGlobal::Active);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getEloquentQuery()->count();
        return $count > 10 ? 'warning' : 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
