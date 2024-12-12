<?php

namespace App\Filament\Resources\WidgetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\StatusGlobal;
use Filament\Notifications\Notification;

class DisplayRulesRelationManager extends RelationManager
{
    protected static string $relationship = 'displayRules';
    protected static ?string $title = 'Reglas de visualización';
    protected static ?string $recordTitleAttribute = 'type';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('Tipo de regla')
                        ->options([
                            'device' => 'Dispositivo',
                            'schedule' => 'Horario',
                            'user_role' => 'Rol de usuario',
                            'url_pattern' => 'Patrón de URL',
                            'query_param' => 'Parámetro de consulta',
                            'cookie' => 'Cookie',
                            'geo_location' => 'Ubicación geográfica',
                        ])
                        ->required(),

                    Forms\Components\Select::make('operator')
                        ->label('Operador')
                        ->options([
                            'equals' => 'Igual a',
                            'not_equals' => 'Diferente de',
                            'contains' => 'Contiene',
                            'not_contains' => 'No contiene',
                            'starts_with' => 'Empieza con',
                            'ends_with' => 'Termina con',
                            'matches' => 'Coincide con patrón',
                            'in' => 'Está en lista',
                            'not_in' => 'No está en lista',
                        ])
                        ->required(),

                    Forms\Components\KeyValue::make('conditions')
                        ->label('Condiciones')
                        ->addActionLabel('Agregar condición')
                        ->keyLabel('Parámetro')
                        ->valueLabel('Valor')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options(StatusGlobal::class)
                        ->default(StatusGlobal::Active)
                        ->required(),
                ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),

                Tables\Columns\TextColumn::make('operator')
                    ->label('Operador')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'equals' => 'Igual a',
                        'not_equals' => 'Diferente de',
                        'contains' => 'Contiene',
                        'not_contains' => 'No contiene',
                        'starts_with' => 'Empieza con',
                        'ends_with' => 'Termina con',
                        'matches' => 'Coincide con patrón',
                        'in' => 'Está en lista',
                        'not_in' => 'No está en lista',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('conditions')
                    ->label('Condiciones')
                    ->formatStateUsing(function ($state) {
                        if (!is_array($state)) return '';
                        return collect($state)
                            ->map(fn ($value, $key) => "$key: $value")
                            ->join(', ');
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'device' => 'Dispositivo',
                        'schedule' => 'Horario',
                        'user_role' => 'Rol de usuario',
                        'url_pattern' => 'Patrón de URL',
                        'query_param' => 'Parámetro de consulta',
                        'cookie' => 'Cookie',
                        'geo_location' => 'Ubicación geográfica',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(StatusGlobal::class),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Regla creada')
                            ->body('La regla de visualización se creó correctamente.')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Regla actualizada')
                            ->body('La regla de visualización se actualizó correctamente.')
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Regla eliminada')
                            ->body('La regla de visualización se eliminó correctamente.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
