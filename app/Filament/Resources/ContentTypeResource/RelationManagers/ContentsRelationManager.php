<?php

namespace App\Filament\Resources\ContentTypeResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\{Select, RichEditor, DateTimePicker, KeyValue, TextInput};
use Filament\Tables\Columns\{TextColumn, IconColumn};
use App\Enums\{ContentStatus, StatusGlobal};
use Filament\Tables\Actions;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;

class ContentsRelationManager extends RelationManager
{
    protected static string $relationship = 'contents';
    protected static ?string $title = 'Contenidos';
    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('section_id')
                ->label('Sección')
                ->relationship('section', 'name')
                ->searchable()
                ->preload()
                ->required(),

            RichEditor::make('content')
                ->label('Contenido')
                ->columnSpanFull(),

            KeyValue::make('display_conditions')
                ->label('Condiciones de visualización')
                ->addActionLabel('Agregar Condición')
                ->keyLabel('Condición')
                ->valueLabel('Valor')
                ->columnSpanFull(),

            KeyValue::make('settings')
                ->label('Configuración')
                ->addActionLabel('Agregar Configuración')
                ->keyLabel('Propiedad')
                ->valueLabel('Valor')
                ->columnSpanFull(),

            DateTimePicker::make('start_date')
                ->label('Fecha de inicio'),

            DateTimePicker::make('end_date')
                ->label('Fecha de fin'),

            DateTimePicker::make('published_at')
                ->label('Fecha de publicación'),

            TextInput::make('order')
                ->label('Orden')
                ->numeric()
                ->default(0),

            Select::make('status')
                ->label('Estado')
                ->options(ContentStatus::class)
                ->required()
                ->default(ContentStatus::Draft),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('section.name')
                    ->label('Sección')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(ContentStatus::class),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('¡Actualizado!')
                            ->body('El contenido se actualizó con éxito.')
                    ),
                Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('¡Eliminado!')
                            ->body('El contenido se eliminó correctamente.')
                    ),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
