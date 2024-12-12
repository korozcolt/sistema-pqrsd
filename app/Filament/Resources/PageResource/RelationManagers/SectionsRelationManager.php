<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use App\Enums\StatusGlobal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Forms\Components\{Grid, KeyValue};

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';
    protected static ?string $title = 'Secciones de la página';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre'),

                Forms\Components\TextInput::make('identifier')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Identificador'),

                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->label('Orden'),

                Forms\Components\Select::make('status')
                    ->options(StatusGlobal::class)
                    ->default(StatusGlobal::Active)
                    ->required()
                    ->label('Estado'),
            ]),

            Forms\Components\Textarea::make('description')
                ->maxLength(500)
                ->columnSpanFull()
                ->label('Descripción'),

            KeyValue::make('settings')
                ->label('Configuraciones')
                ->addActionLabel('Agregar configuración')
                ->keyLabel('Propiedad')
                ->valueLabel('Valor')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('identifier')
                    ->label('Identificador')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Estado'),

                Tables\Columns\TextColumn::make('contents_count')
                    ->counts('contents')
                    ->label('Contenidos'),

                Tables\Columns\TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(StatusGlobal::class)
                    ->label('Estado'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Sección creada')
                            ->body('La sección ha sido creada exitosamente.')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Sección actualizada')
                            ->body('La sección ha sido actualizada exitosamente.')
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Sección eliminada')
                            ->body('La sección ha sido eliminada exitosamente.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }
}
