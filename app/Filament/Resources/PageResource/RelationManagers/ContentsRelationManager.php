<?php

namespace App\Filament\Resources\PageResource\RelationManagers;

use App\Enums\ContentStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Forms\Components\{Select, RichEditor, KeyValue, DateTimePicker};

class ContentsRelationManager extends RelationManager
{
    protected static string $relationship = 'contents';
    protected static ?string $title = 'Contenidos de la página';
    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('content_type_id')
                ->relationship('contentType', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->label('Tipo de contenido'),

            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->label('Título'),

            RichEditor::make('content')
                ->required()
                ->columnSpanFull()
                ->label('Contenido'),

            KeyValue::make('settings')
                ->label('Configuraciones')
                ->addActionLabel('Agregar configuración')
                ->columnSpanFull(),

            DateTimePicker::make('published_at')
                ->label('Fecha de publicación'),

            Select::make('status')
                ->options(ContentStatus::class)
                ->required()
                ->default(ContentStatus::Draft)
                ->label('Estado'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contentType.name')
                    ->label('Tipo')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Estado'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicación')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('content_type_id')
                    ->relationship('contentType', 'name')
                    ->label('Tipo de contenido'),

                Tables\Filters\SelectFilter::make('status')
                    ->options(ContentStatus::class)
                    ->label('Estado'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Contenido creado')
                            ->body('El contenido ha sido creado exitosamente.')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Contenido actualizado')
                            ->body('El contenido ha sido actualizado exitosamente.')
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Contenido eliminado')
                            ->body('El contenido ha sido eliminado exitosamente.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
