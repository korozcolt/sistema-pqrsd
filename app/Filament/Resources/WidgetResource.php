<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WidgetResource\Pages;
use App\Models\Widget;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Enums\{ComponentType, StatusGlobal};
use Filament\Forms\Components\{TextInput, Select, KeyValue, DateTimePicker, Group, Section};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Str;

class WidgetResource extends Resource
{
    protected static ?string $model = Widget::class;
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Widget';
    protected static ?string $pluralModelLabel = 'Widgets';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Widget')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) =>
                                $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('type')
                            ->options(ComponentType::class)
                            ->required(),

                        Select::make('status')
                            ->options(StatusGlobal::class)
                            ->default(StatusGlobal::Active)
                            ->required(),

                        TextInput::make('order')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                Section::make('Contenido y Configuración')
                    ->schema([
                        // Para RedBus Widget
                        Group::make()
                            ->schema([
                                TextInput::make('content.widget_id')
                                    ->label('Widget ID')
                                    ->required(),
                                TextInput::make('content.position')
                                    ->label('Posición')
                                    ->required(),
                            ])
                            ->visible(fn (callable $get) =>
                                $get('type') === ComponentType::Widget->value),

                        KeyValue::make('settings')
                            ->label('Configuraciones')
                            ->reorderable()
                            ->addActionLabel('Agregar configuración'),

                        KeyValue::make('display_rules')
                            ->label('Reglas de visualización')
                            ->reorderable()
                            ->addActionLabel('Agregar regla'),
                    ]),

                Section::make('Programación')
                    ->schema([
                        DateTimePicker::make('start_date')
                            ->label('Fecha de inicio'),
                        DateTimePicker::make('end_date')
                            ->label('Fecha de fin'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('content')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return collect($state)
                                ->map(fn ($value, $key) =>
                                    ucfirst($key) . ': ' . $value)
                                ->join(' | ');
                        }
                        return $state;
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(ComponentType::class),
                SelectFilter::make('status')
                    ->options(StatusGlobal::class),
            ]);
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
            'index' => Pages\ListWidgets::route('/'),
            'create' => Pages\CreateWidget::route('/create'),
            'edit' => Pages\EditWidget::route('/{record}/edit'),
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
}
