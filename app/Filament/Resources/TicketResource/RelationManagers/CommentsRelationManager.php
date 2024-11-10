<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use Filament\Forms\Components\Section;
use App\Models\TicketComment;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = 'Responses & Comments';
    protected static ?string $recordTitleAttribute = 'content';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Response Details')
                    ->description('Add an official response or internal note')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Response')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_internal')
                            ->label('Internal Note')
                            ->helperText('Internal notes are only visible to staff members')
                            ->default(false)
                            ->visible(fn() => Auth::user()->role !== UserRole::UserWeb),

                        Forms\Components\Hidden::make('user_id')
                            ->default(Auth::id()),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Response By')
                    ->sortable()
                    ->description(fn($record) => $record->user->role->value)
                    ->searchable(),

                Tables\Columns\TextColumn::make('content')
                    ->label('Response')
                    ->html()
                    ->limit(50)
                    ->tooltip(function ($record): string {
                        return strip_tags($record->content);
                    }),

                Tables\Columns\IconColumn::make('is_internal')
                    ->label('Internal Note')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-globe-alt')
                    ->visible(fn() => !in_array(Auth::user()->role, [UserRole::UserWeb])),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Responded At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_internal')
                    ->label('Show Internal Notes')
                    ->visible(fn() => !in_array(Auth::user()->role, [UserRole::UserWeb])),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
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
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->after(function () {
                            Notification::make()
                                ->success()
                                ->title('Response updated successfully')
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->after(function () {
                            Notification::make()
                                ->success()
                                ->title('Response deleted successfully')
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
