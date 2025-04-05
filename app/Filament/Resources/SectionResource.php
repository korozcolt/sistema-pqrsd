<?php

namespace App\Filament\Resources;

use App\Enums\SectionType;
use App\Filament\Resources\SectionResource\Pages;
use App\Models\Section;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Riodwanto\FilamentAceEditor\AceEditor;


class SectionResource extends Resource
{
    protected static ?string $model = Section::class;
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Sección';
    protected static ?string $pluralModelLabel = 'Secciones';

    public static function form(Form $form): Form
    {
        return $form->schema([
            FormSection::make()->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan('full'),

                Select::make('page_id')
                    ->relationship('page', 'title')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->required(),

                AceEditor::make('content')
                    ->mode('html')
                    ->theme('github')
                    ->darkTheme('dracula')
                    ->autosize()
                    ->required()
                    ->hint('You can use HTML tags here')
                    ->hintColor('warning')
                    ->columnSpan('full'),

                TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->columnSpan('full')
                    ->default(true),
            ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('page.title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('order')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('page')
                    ->relationship('page', 'title'),

                SelectFilter::make('type')
                    ->options(SectionType::class),

                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
            'reorder' => Pages\ReorderSection::route('/reorder'),
        ];
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes();
    }
}
