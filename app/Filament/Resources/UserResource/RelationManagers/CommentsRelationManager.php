<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\UserRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\RichEditor;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'User Comments';
    protected static ?string $recordTitleAttribute = 'content';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Comment Information')
                    ->schema([
                        Forms\Components\Select::make('ticket_id')
                            ->relationship('ticket', 'ticket_number')
                            ->searchable()
                            ->preload()
                            ->required(),

                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_internal')
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
                Tables\Columns\TextColumn::make('ticket.ticket_number')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.tickets.view', $record->ticket))
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('content')
                    ->html()
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_internal')
                    ->label('Internal')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_internal')
                    ->label('Internal Comments Only'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn ($record) =>
                            Auth::user()->role !== UserRole::UserWeb ||
                            $record->user_id === Auth::id()
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
