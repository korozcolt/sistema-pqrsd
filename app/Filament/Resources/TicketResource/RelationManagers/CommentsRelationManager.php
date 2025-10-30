<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Models\TicketComment;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'Responses & Comments';
    protected static ?string $recordTitleAttribute = 'content';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Response Details')
                    ->description('Add an official response or internal note')
                    ->schema([
                        RichEditor::make('content')
                            ->label('Response')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->columnSpanFull(),

                        Toggle::make('is_internal')
                            ->label('Internal Note')
                            ->helperText('Internal notes are only visible to staff members')
                            ->default(false)
                            ->visible(fn() => Auth::user()->role !== UserRole::UserWeb),

                        Hidden::make('user_id')
                            ->default(Auth::id()),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Response By')
                    ->sortable()
                    ->description(fn($record) => $record->user->role->value)
                    ->searchable(),

                TextColumn::make('content')
                    ->label('Response')
                    ->html()
                    ->limit(50)
                    ->tooltip(function ($record): string {
                        return strip_tags($record->content);
                    }),

                IconColumn::make('is_internal')
                    ->label('Internal Note')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-globe-alt')
                    ->visible(fn() => !in_array(Auth::user()->role, [UserRole::UserWeb])),

                TextColumn::make('created_at')
                    ->label('Responded At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_internal')
                    ->label('Show Internal Notes')
                    ->visible(fn() => !in_array(Auth::user()->role, [UserRole::UserWeb])),
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function (TicketComment $record) {
                        // Si no es una nota interna, actualizar first_response_at si aún no está establecido
                        if (!$record->is_internal && is_null($this->ownerRecord->first_response_at)) {
                            $this->ownerRecord->update(['first_response_at' => now()]);
                        }

                        Notification::make()
                            ->success()
                            ->title('Response added successfully')
                            ->send();
                    })
                    ->mutateDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->after(function () {
                            Notification::make()
                                ->success()
                                ->title('Response updated successfully')
                                ->send();
                        }),
                    DeleteAction::make()
                        ->after(function () {
                            Notification::make()
                                ->success()
                                ->title('Response deleted successfully')
                                ->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()->role === UserRole::SuperAdmin),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn($query) => $query->when(
                Auth::user()->role === UserRole::UserWeb,
                fn($q) => $q->where('is_internal', false)
            ));
    }

    protected function canCreate(): bool
    {
        return in_array(Auth::user()->role, [
            UserRole::SuperAdmin,
            UserRole::Admin,
            UserRole::Receptionist
        ]) && !in_array($this->ownerRecord->status, ['closed', 'rejected']);
    }

    protected function canEdit(Model $record): bool
    {
        return (Auth::id() === $record->user_id &&
            $record->created_at->diffInMinutes(now()) < 30 &&
            !in_array($this->ownerRecord->status, ['closed', 'rejected'])) ||
            Auth::user()->role === UserRole::SuperAdmin;
    }

    protected function canDelete(Model $record): bool
    {
        return Auth::user()->role === UserRole::SuperAdmin;
    }
}
