<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SLAResource\Pages;
use App\Models\SLA;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\TicketType;
use App\Enums\Priority;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use Filament\Forms\Components\Section;

class SLAResource extends Resource
{
    protected static ?string $model = SLA::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Ticket Settings';
    protected static ?string $navigationLabel = 'SLA Configuration';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'SLA Rule';
    protected static ?string $pluralModelLabel = 'SLA Rules';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('SLA Configuration')
                    ->description('Configure response and resolution times for different ticket types')
                    ->schema([
                        Forms\Components\Select::make('ticket_type')
                            ->label('Ticket Type')
                            ->options(TicketType::class)
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('priority')
                            ->label('Priority Level')
                            ->options(Priority::class)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('response_time')
                            ->label('Response Time (Hours)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(168) // 1 week
                            ->helperText('Maximum response time in hours'),

                        Forms\Components\TextInput::make('resolution_time')
                            ->label('Resolution Time (Hours)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(720) // 30 days
                            ->helperText('Maximum resolution time in hours'),

                        Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('ticket_type')
                    ->label('Type')
                    ->badge(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->badge(),

                Tables\Columns\TextColumn::make('response_time')
                    ->label('Response Time')
                    ->formatStateUsing(fn(int $state): string => self::formatHours($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('resolution_time')
                    ->label('Resolution Time')
                    ->formatStateUsing(fn(int $state): string => self::formatHours($state))
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ticket_type')
                    ->options(TicketType::class)
                    ->label('Filter by Type'),

                Tables\Filters\SelectFilter::make('priority')
                    ->options(Priority::class)
                    ->label('Filter by Priority'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only')
                    ->placeholder('All'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ListSLAS::route('/'),
            'create' => Pages\CreateSLA::route('/create'),
            'view' => Pages\ViewSLA::route('/{record}'),
            'edit' => Pages\EditSLA::route('/{record}/edit'),
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
