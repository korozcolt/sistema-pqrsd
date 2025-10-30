<?php

namespace App\Observers;

use App\Models\User;
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
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // Notificar al usuario
        $ticket->user->notify(new NewTicketNotification($ticket));

        // Notificar al email configurado para PQRs
        Notification::route('mail', env('TICKET_NOTIFICATION_EMAIL', 'soporte@torcoromaweb.com'))
            ->notify(new NewTicketNotification($ticket));

        // Registrar creación en el log
        $this->logTicketChange(
            $ticket,
            null,  // old status
            $ticket->status,
            null,  // old department
            $ticket->department_id,
            null,  // old priority
            $ticket->priority,
            'Ticket Created'
        );
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        $changes = $ticket->getDirty();

        // Verificar si hubo cambio de estado
        if (isset($changes['status'])) {
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

        // Si el ticket se cierra, resolver o rechaza, eliminar recordatorios
        if (isset($changes['status']) && in_array($ticket->status, ['closed', 'resolved', 'rejected'])) {
            $ticket->reminders()->delete();
        }

        // Registrar cambios significativos
        if (array_intersect_key($changes, array_flip(['status', 'department_id', 'priority']))) {
            $this->logTicketChange(
                $ticket,
                $ticket->getOriginal('status'),
                $ticket->status,
                $ticket->getOriginal('department_id'),
                $ticket->department_id,
                $ticket->getOriginal('priority'),
                $ticket->priority,
                null // Sin razón específica
            );
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        $this->logTicketChange(
            $ticket,
            $ticket->status,
            StatusTicket::Closed->value,
            $ticket->department_id,
            $ticket->department_id,
            $ticket->priority,
            $ticket->priority,
            'Ticket Deleted'
        );
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        $this->logTicketChange(
            $ticket,
            StatusTicket::Closed->value,
            StatusTicket::Reopened->value,
            $ticket->department_id,
            $ticket->department_id,
            $ticket->priority,
            $ticket->priority,
            'Ticket Restored'
        );
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        $this->logTicketChange(
            $ticket,
            $ticket->status,
            StatusTicket::Closed->value,
            $ticket->department_id,
            $ticket->department_id,
            $ticket->priority,
            $ticket->priority,
            'Ticket Permanently Deleted'
        );
    }

    /**
     * Crear un registro en el log de tickets
     */
    private function logTicketChange(
        Ticket $ticket,
        $oldStatus,
        $newStatus,
        $oldDepartment,
        $newDepartment,
        $oldPriority,
        $newPriority,
        $reason = null
    ): void {
        // Obtener el ID del usuario que realiza el cambio
        $userId = Auth::id();

        // Si no hay usuario autenticado, usar el ID del propietario del ticket
        if (!$userId) {
            $userId = $ticket->user_id;
        }

        // Asegurarse de que tenemos un valor válido para changed_by
        if (!$userId) {
            // Último recurso: buscar un administrador
            $adminUser = User::where('role', 'admin')->first();
            $userId = $adminUser ? $adminUser->id : 1; // Usar el ID 1 como último recurso
        }

        // Crear el registro de log
        $ticket->logs()->create([
            'changed_by' => $userId,
            'previous_status' => $oldStatus,
            'new_status' => $newStatus,
            'previous_department_id' => $oldDepartment,
            'new_department_id' => $newDepartment,
            'previous_priority' => $oldPriority,
            'new_priority' => $newPriority,
            'change_reason' => $reason,
            'changed_at' => now()
        ]);
    }
}
