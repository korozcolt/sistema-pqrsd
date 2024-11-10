<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Events\TicketStatusChanged;
use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function beforeSave(): void
    {
        $ticket = $this->record;
        $data = $this->data;

        // Verificar cambios en el estado
        if ($ticket->isDirty('status') ||
            $ticket->isDirty('department_id') ||
            $ticket->isDirty('priority')) {

            // Solicitar razón del cambio si hay cambio de estado
            if ($ticket->isDirty('status')) {
                $reason = $this->getReason();
            } else {
                $reason = null;
            }

            event(new TicketStatusChanged(
                ticket: $ticket,
                oldStatus: $ticket->getOriginal('status'),
                newStatus: $data['status'],
                changedBy: Auth::id(),
                reason: $reason,
                oldDepartment: $ticket->getOriginal('department_id'),
                newDepartment: $data['department_id'],
                oldPriority: $ticket->getOriginal('priority'),
                newPriority: $data['priority']
            ));
        }
    }

    protected function getReason(): ?string
    {
        return $this->modal('changeReason', [
            'title' => 'Reason for Status Change',
            'form' => [
                TextInput::make('reason')
                    ->label('Please provide a reason for this status change')
                    ->required(),
            ],
        ])->openModal();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
