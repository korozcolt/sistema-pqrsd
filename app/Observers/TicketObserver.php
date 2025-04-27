<?php

namespace App\Observers;

use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Events\TicketStatusChanged;
use App\Models\Ticket;
use App\Notifications\NewTicketNotification;
use App\Notifications\TicketStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class TicketObserver
{
    public function creating(Ticket $ticket): void
    {
        // Nota: La generación del ticket_number ahora se maneja automáticamente
        // en el método booted() del modelo Ticket
    }

    public function created(Ticket $ticket): void
    {
        $ticket->user->notify(new NewTicketNotification($ticket));

        // Notificar al email configurado para PQRs
        Notification::route('mail', env('TICKET_NOTIFICATION_EMAIL', 'soporte@torcoromaweb.com'))
            ->notify(new NewTicketNotification($ticket));
        event(new TicketStatusChanged(
            ticket: $ticket,
            oldStatus: null,
            newStatus: $ticket->status,
            changedBy: Auth::id(),
            reason: 'Ticket Created',
            oldDepartment: null,
            newDepartment: $ticket->department_id,
            oldPriority: null,
            newPriority: $ticket->priority
        ));
    }

    public function updated(Ticket $ticket): void
    {
        if ($ticket->isDirty('status')) {
            // Notificar al usuario
            $ticket->user->notify(new TicketStatusUpdated(
                $ticket,
                $ticket->getOriginal('status'),
                $ticket->status
            ));

            // Notificar al email de PQRs
            Notification::route('mail', env('TICKET_NOTIFICATION_EMAIL', 'soporte@torcoromaweb.com'))
                ->notify(new TicketStatusUpdated(
                    $ticket,
                    $ticket->getOriginal('status'),
                    $ticket->status
                ));
        }

        $changes = $ticket->getDirty();

        if (isset($changes['status']) && in_array($ticket->status, ['closed', 'resolved', 'rejected'])) {
            $ticket->reminders()->delete();
        }

        if (array_intersect_key($changes, array_flip(['status', 'department_id', 'priority']))) {
            $oldStatusValue = $ticket->getOriginal('status');
            $oldPriorityValue = $ticket->getOriginal('priority');

            event(new TicketStatusChanged(
                ticket: $ticket,
                oldStatus: $oldStatusValue instanceof StatusTicket ? $oldStatusValue : null,
                newStatus: $ticket->status,
                changedBy: Auth::id(),
                reason: null,
                oldDepartment: $ticket->getOriginal('department_id'),
                newDepartment: $ticket->department_id,
                oldPriority: $oldPriorityValue instanceof Priority ? $oldPriorityValue : null,
                newPriority: $ticket->priority
            ));
        }
    }

    public function deleted(Ticket $ticket): void
    {
        event(new TicketStatusChanged(
            ticket: $ticket,
            oldStatus: $ticket->status,
            newStatus: StatusTicket::Closed, // Cambiamos el estado a cerrado al eliminar
            changedBy: Auth::id(),
            reason: 'Ticket Deleted',
            oldDepartment: $ticket->department_id,
            newDepartment: $ticket->department_id, // Mantenemos el mismo departamento
            oldPriority: $ticket->priority,
            newPriority: $ticket->priority // Mantenemos la misma prioridad
        ));
    }

    public function restored(Ticket $ticket): void
    {
        event(new TicketStatusChanged(
            ticket: $ticket,
            oldStatus: StatusTicket::Closed,
            newStatus: StatusTicket::Reopened,
            changedBy: Auth::id(),
            reason: 'Ticket Restored',
            oldDepartment: $ticket->department_id,
            newDepartment: $ticket->department_id,
            oldPriority: $ticket->priority,
            newPriority: $ticket->priority
        ));
    }

    public function forceDeleted(Ticket $ticket): void
    {
        event(new TicketStatusChanged(
            ticket: $ticket,
            oldStatus: $ticket->status,
            newStatus: StatusTicket::Closed,
            changedBy: Auth::id(),
            reason: 'Ticket Permanently Deleted',
            oldDepartment: $ticket->department_id,
            newDepartment: $ticket->department_id,
            oldPriority: $ticket->priority,
            newPriority: $ticket->priority
        ));
    }
}
