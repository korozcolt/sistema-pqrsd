<?php

namespace App\Observers;

use App\Models\User;
use App\Models\SLA;
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
     * Handle the Ticket "creating" event.
     * Se ejecuta ANTES de guardar el ticket en la base de datos.
     */
    public function creating(Ticket $ticket): void
    {
        // Calcular fechas de SLA automáticamente
        $this->calculateSLADates($ticket);
    }

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

        // Disparar evento para que listeners puedan procesarlo
        event(new \App\Events\TicketCreatedEvent($ticket));
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

            // Disparar evento de cambio de estado
            event(new TicketStatusChanged(
                ticket: $ticket,
                oldStatus: $ticket->getOriginal('status'),
                newStatus: $ticket->status,
                changedBy: Auth::id(),
                oldDepartment: $ticket->getOriginal('department_id'),
                newDepartment: $ticket->department_id,
                oldPriority: $ticket->getOriginal('priority'),
                newPriority: $ticket->priority,
                reason: null
            ));
        }

        // Si el ticket se cierra, resolver o rechaza, eliminar recordatorios
        if (isset($changes['status']) && in_array($ticket->status, ['closed', 'resolved', 'rejected'])) {
            $ticket->reminders()->delete();
        }

        // Registrar cambios significativos (si no hay cambio de estado)
        if (!isset($changes['status']) && array_intersect_key($changes, array_flip(['department_id', 'priority']))) {
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
        // Solo logear si es soft delete (no force delete)
        if ($ticket->trashed()) {
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
        // No intentar logear en force delete ya que el ticket ya no existe en BD
        // Los logs se eliminarán automáticamente por CASCADE
    }

    /**
     * Calcular fechas de SLA automáticamente según tipo y prioridad del ticket
     */
    private function calculateSLADates(Ticket $ticket): void
    {
        // Solo calcular si no se han establecido manualmente
        if ($ticket->response_due_date || $ticket->resolution_due_date) {
            return;
        }

        // Buscar SLA correspondiente al tipo y prioridad del ticket
        $sla = SLA::where('ticket_type', $ticket->type)
            ->where('priority', $ticket->priority)
            ->where('is_active', true)
            ->first();

        if (!$sla) {
            // Si no hay SLA configurado, usar valores por defecto
            $ticket->response_due_date = now()->addHours(24);
            $ticket->resolution_due_date = now()->addDays(15);
            return;
        }

        // Calcular fechas basadas en los tiempos del SLA (en horas)
        $ticket->response_due_date = now()->addHours($sla->response_time);
        $ticket->resolution_due_date = now()->addHours($sla->resolution_time);
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
