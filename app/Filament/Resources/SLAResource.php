<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\SLAResource\Pages\ListSLAS;
use App\Filament\Resources\SLAResource\Pages\CreateSLA;
use App\Filament\Resources\SLAResource\Pages\ViewSLA;
use App\Filament\Resources\SLAResource\Pages\EditSLA;
use App\Filament\Resources\SLAResource\Pages;
use App\Models\SLA;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\TicketType;
use App\Enums\Priority;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class SLAResource extends Resource
{
    protected static ?string $model = SLA::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';
    protected static string | \UnitEnum | null $navigationGroup = 'Ticket Settings';
    protected static ?string $navigationLabel = 'SLA Configuration';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'SLA Rule';
    protected static ?string $pluralModelLabel = 'SLA Rules';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('SLA Configuration')
                    ->description('Configure response and resolution times for different ticket types')
                    ->schema([
                        Select::make('ticket_type')
                            ->label('Ticket Type')
                            ->options(TicketType::class)
                            ->required()
                            ->native(false),

                        Select::make('priority')
                            ->label('Priority Level')
                            ->options(Priority::class)
                            ->required()
                            ->native(false),

                        TextInput::make('response_time')
                            ->label('Response Time (Hours)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(168) // 1 week
                            ->helperText('Maximum response time in hours'),

                        TextInput::make('resolution_time')
                            ->label('Resolution Time (Hours)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(720) // 30 days
                            ->helperText('Maximum resolution time in hours'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Only active SLA rules will be applied')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge(),

                TextColumn::make('response_time')
                    ->label('Response Time')
                    ->formatStateUsing(fn(int $state): string => self::formatHours($state))
                    ->sortable(),

                TextColumn::make('resolution_time')
                    ->label('Resolution Time')
                    ->formatStateUsing(fn(int $state): string => self::formatHours($state))
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('ticket_type')
                    ->options(TicketType::class)
                    ->label('Filter by Type'),

                SelectFilter::make('priority')
                    ->options(Priority::class)
                    ->label('Filter by Priority'),

                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only')
                    ->placeholder('All'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->defaultSort('ticket_type')
            ->striped();
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
            'index' => ListSLAS::route('/'),
            'create' => CreateSLA::route('/create'),
            'view' => ViewSLA::route('/{record}'),
            'edit' => EditSLA::route('/{record}/edit'),
        ];
    }

    private static function formatHours(int $hours): string
    {
        if ($hours < 24) {
            return "{$hours}h";
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        if ($remainingHours === 0) {
            return "{$days}d";
        }

        return "{$days}d {$remainingHours}h";
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function canCreate(): bool
    {
        return Auth::user()->role === UserRole::SuperAdmin ||
            Auth::user()->role === UserRole::Admin;
    }
}
