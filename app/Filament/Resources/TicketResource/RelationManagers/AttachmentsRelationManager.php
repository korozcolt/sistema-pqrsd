<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRole;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';
    protected static ?string $title = 'Attachments';
    protected static ?string $recordTitleAttribute = 'file_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file_path')
                    ->label('File')
                    ->required()
                    ->maxSize(5120) // 5MB
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ])
                    ->directory('ticket-attachments')
                    ->preserveFilenames()
                    ->downloadable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('file_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('file_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper(str_replace('application/', '', $state))),

                TextColumn::make('file_size')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB'),

                TextColumn::make('user.name')
                    ->label('Uploaded By'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => Auth::user()->role !== UserRole::UserWeb)
                    ->mutateDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),

                DeleteAction::make()
                    ->visible(fn ($record) =>
                        Auth::id() === $record->user_id ||
                        Auth::user()->role === UserRole::SuperAdmin
                    ),
            ]);
    }
}
