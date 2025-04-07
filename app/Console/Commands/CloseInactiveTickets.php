<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Enums\StatusTicket;
use App\Events\TicketStatusChanged;
use App\Notifications\TicketInactivityClosedNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseInactiveTickets extends Command
{
    protected $signature = 'tickets:close-inactive';
    protected $description = 'Cierra tickets que fueron notificados y siguen sin actividad del cliente';

    public function handle()
    {
        // Definir el período de aviso (72 horas = 3 días)
        $warningHours = 72;

        // Buscar tickets marcados para cierre hace más de 72 horas
        $tickets = Ticket::whereNotNull('marked_for_closure_at')
            ->where('marked_for_closure_at', '<', now()->subHours($warningHours))
            ->whereNotIn('status', [StatusTicket::Closed, StatusTicket::Rejected])
            ->get();

        $closedCount = 0;

        foreach ($tickets as $ticket) {
            // Verificar si hubo algún comentario del cliente después de la marca
            $hasNewClientComment = $ticket->comments()
                ->whereHas('user', function($query) {
                    $query->where('role', 'user_web');
                })
                ->where('created_at', '>', $ticket->marked_for_closure_at)
                ->exists();

            if (!$hasNewClientComment) {
                // Guardar estado anterior para el evento
                $oldStatus = $ticket->status;

                // Cerrar el ticket
                $ticket->status = StatusTicket::Closed;
                $ticket->resolution_at = now();
                $ticket->save();

                // Calcular días de inactividad total
                $lastActivity = $ticket->comments()->latest()->first()?->created_at ?? $ticket->created_at;
                $inactiveDays = $lastActivity->diffInDays(now());

                // Crear comentario de cierre automático
                $ticket->comments()->create([
                    'user_id' => 1, // ID del sistema
                    'content' => "Este ticket ha sido cerrado automáticamente por el sistema debido a {$inactiveDays} días de inactividad después del aviso previo de 72 horas.",
                    'is_internal' => false,
                ]);

                // Notificar al cliente
                $ticket->user->notify(new TicketInactivityClosedNotification($ticket, $inactiveDays));

                // Notificar al asignado (si existe)
                $assignedUser = $ticket->department?->users()->first(); // Ajustar según tu modelo
                if ($assignedUser) {
                    $assignedUser->notify(new TicketInactivityClosedNotification($ticket, $inactiveDays));
                }

                // Disparar evento de cambio de estado
                event(new TicketStatusChanged(
                    ticket: $ticket,
                    oldStatus: $oldStatus,
                    newStatus: StatusTicket::Closed,
                    changedBy: null, // sistema
                    reason: "Cierre automático por inactividad de {$inactiveDays} días"
                ));

                $closedCount++;
            } else {
                // Si el cliente respondió, quitar la marca de cierre
                $ticket->marked_for_closure_at = null;
                $ticket->save();
            }
        }

        $this->info("Se cerraron {$closedCount} tickets automáticamente por inactividad.");
    }
}
