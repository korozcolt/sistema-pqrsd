<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\UserRole;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\RichEditor;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'User Comments';
    protected static ?string $recordTitleAttribute = 'content';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Comment Information')
                    ->schema([
                        Select::make('ticket_id')
                            ->relationship('ticket', 'ticket_number')
                            ->searchable()
                            ->preload()
                            ->required(),

                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),

                        Toggle::make('is_internal')
                            ->label('Internal Comment')
                            ->default(false)
                            ->helperText('Internal comments are only visible to staff members'),
                    ])
                    ->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                TextColumn::make('ticket.ticket_number')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.tickets.view', $record->ticket))
                    ->openUrlInNewTab(),

                TextColumn::make('content')
                    ->html()
                    ->limit(50)
                    ->searchable(),

                IconColumn::make('is_internal')
                    ->label('Internal')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_internal')
                    ->label('Internal Comments Only'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(fn ($record) =>
                            Auth::user()->role !== UserRole::UserWeb ||
                            $record->user_id === Auth::id()
                        ),
                    DeleteAction::make()
                        ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
