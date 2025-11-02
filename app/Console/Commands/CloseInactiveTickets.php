<?php

namespace App\Console\Commands;

use App\Enums\StatusTicket;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketInactivityClosedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class CloseInactiveTickets extends Command
{
    protected $signature = 'tickets:close-inactive';

    protected $description = 'Cierra tickets que fueron notificados y siguen sin actividad del cliente';

    public function handle()
    {
        // Autenticar como usuario del sistema para los cambios automáticos
        $systemUser = User::find(1);
        if ($systemUser) {
            Auth::login($systemUser);
        }

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
                ->whereHas('user', function ($query) {
                    $query->where('role', 'user_web');
                })
                ->where('created_at', '>', $ticket->marked_for_closure_at)
                ->exists();

            if (! $hasNewClientComment) {
                // Cerrar el ticket (el Observer se encargará de disparar eventos)
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

                // TODO: Notificar al asignado (si existe) - requiere implementar relación Department->users()
                // $assignedUser = $ticket->department?->users()->first();
                // if ($assignedUser) {
                //     $assignedUser->notify(new TicketInactivityClosedNotification($ticket, $inactiveDays));
                // }

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
