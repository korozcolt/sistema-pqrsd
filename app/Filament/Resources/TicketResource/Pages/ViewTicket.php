<?php

namespace App\Filament\Resources\TicketResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Filament\Resources\TicketResource\Widgets\CommentsList;
use Filament\Forms;
use Filament\Notifications\Notification;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('respond')
                ->label('Add Response')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->schema([
                    RichEditor::make('content')
                        ->label('Response')
                        ->required()
                        ->toolbarButtons([
                            'bold',
                            'bulletList',
                            'orderedList',
                            'link',
                        ]),
                    Toggle::make('is_internal')
                        ->label('Internal Note')
                        ->helperText('Internal notes are only visible to staff members')
                        ->default(false)
                        ->visible(fn () => Auth::user()->role !== UserRole::UserWeb),
                ])
                ->action(function (array $data): void {
                    $ticket = $this->getRecord();

                    $comment = $ticket->comments()->create([
                        'content' => $data['content'],
                        'is_internal' => $data['is_internal'] ?? false,
                        'user_id' => Auth::id(),
                    ]);

                    if (!$comment->is_internal && is_null($ticket->first_response_at)) {
                        $ticket->update(['first_response_at' => now()]);
                    }

                    Notification::make()
                        ->success()
                        ->title('Response added successfully')
                        ->send();
                })
                ->visible(fn () =>
                    !in_array($this->getRecord()->status, ['closed', 'rejected']) &&
                    in_array(Auth::user()->role, [
                        UserRole::SuperAdmin,
                        UserRole::Admin,
                        UserRole::Receptionist
                    ])
                ),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CommentsList::class,
        ];
    }
}
