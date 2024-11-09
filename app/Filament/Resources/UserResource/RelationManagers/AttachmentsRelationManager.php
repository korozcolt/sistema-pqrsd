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
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';
    protected static ?string $title = 'User Attachments';
    protected static ?string $recordTitleAttribute = 'file_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Attachment Information')
                    ->schema([
                        Forms\Components\Select::make('ticket_id')
                            ->relationship('ticket', 'ticket_number')
                            ->searchable()
                            ->preload()
                            ->required(),

                        FileUpload::make('file_path')
                            ->label('File')
                            ->required()
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->directory('ticket-attachments')
                            ->columnSpanFull()
                            ->beforeStateUpdated(function ($state) {
                                return [
                                    'file_name' => $state->getClientOriginalName(),
                                    'file_type' => $state->getMimeType(),
                                    'file_size' => $state->getSize(),
                                ];
                            }),

                        Forms\Components\TextInput::make('file_name')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('file_type')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('file_size')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB'),
                    ])
                    ->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_name')
            ->columns([
                Tables\Columns\TextColumn::make('ticket.ticket_number')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.tickets.view', $record->ticket))
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'application/pdf' => 'danger',
                        'image/jpeg', 'image/png', 'image/gif' => 'success',
                        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'info',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('file_type')
                    ->options([
                        'application/pdf' => 'PDF',
                        'image/jpeg' => 'JPEG',
                        'image/png' => 'PNG',
                        'application/msword' => 'DOC',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'DOCX',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn ($record) => Storage::url($record->file_path))
                        ->openUrlInNewTab(),
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
