<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\UserRole;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';
    protected static ?string $title = 'User Attachments';
    protected static ?string $recordTitleAttribute = 'file_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attachment Information')
                    ->schema([
                        Select::make('ticket_id')
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

                        TextInput::make('file_name')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('file_type')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('file_size')
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
                TextColumn::make('ticket.ticket_number')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.tickets.view', $record->ticket))
                    ->openUrlInNewTab(),

                TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('file_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'application/pdf' => 'danger',
                        'image/jpeg', 'image/png', 'image/gif' => 'success',
                        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'info',
                        default => 'warning',
                    }),

                TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('file_type')
                    ->options([
                        'application/pdf' => 'PDF',
                        'image/jpeg' => 'JPEG',
                        'image/png' => 'PNG',
                        'application/msword' => 'DOC',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'DOCX',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn ($record) => Storage::url($record->file_path))
                        ->openUrlInNewTab(),
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
