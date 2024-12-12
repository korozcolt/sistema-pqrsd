<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use App\Enums\StatusGlobal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Elementos del menú';
    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('url')
                        ->label('URL')
                        ->required()
                        ->maxLength(255)
                        ->url(),

                    Forms\Components\Select::make('page_id')
                        ->label('Página')
                        ->relationship('page', 'title')
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('parent_id')
                        ->label('Elemento padre')
                        ->relationship('parent', 'title')
                        ->searchable()
                        ->preload(),

                    Forms\Components\TextInput::make('order')
                        ->label('Orden')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options(StatusGlobal::class)
                        ->default(StatusGlobal::Active)
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Atributos Adicionales')
                ->schema([
                    Forms\Components\KeyValue::make('attributes')
                        ->label('Atributos')
                        ->addActionLabel('Agregar atributo')
                        ->keyLabel('Atributo')
                        ->valueLabel('Valor')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('page.title')
                    ->label('Página')
                    ->searchable(),

                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Elemento padre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('page_id')
                    ->label('Página')
                    ->relationship('page', 'title'),

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Elemento padre')
                    ->relationship('parent', 'title'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(StatusGlobal::class),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Elemento creado')
                            ->body('El elemento del menú se creó correctamente.')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Elemento actualizado')
                            ->body('El elemento del menú se actualizó correctamente.')
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Elemento eliminado')
                            ->body('El elemento del menú se eliminó correctamente.')
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
