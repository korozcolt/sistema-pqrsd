<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Enums\StatusTicket;
use App\Notifications\TicketInactivityWarningNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkInactiveTicketsForClosure extends Command
{
    protected $signature = 'tickets:mark-inactive';
    protected $description = 'Marca tickets inactivos para notificar al cliente sobre cierre próximo';

    public function handle()
    {
        // Número de días de inactividad antes de notificar
        $inactiveDays = 7; // Una semana de inactividad antes de notificar

        // Buscar tickets en estado 'resolved' o 'in_progress'
        $tickets = Ticket::whereIn('status', [StatusTicket::Resolved, StatusTicket::In_Progress])
            ->whereNull('marked_for_closure_at') // No marcados previamente
            ->get();

        $markedCount = 0;

        foreach ($tickets as $ticket) {
            // Verificar cuándo fue el último comentario del cliente
            $lastClientComment = $ticket->comments()
                ->whereHas('user', function($query) {
                    $query->where('role', 'user_web');
                })
                ->latest()
                ->first();

            $lastStaffComment = $ticket->comments()
                ->whereHas('user', function($query) {
                    $query->whereIn('role', ['admin', 'superadmin', 'receptionist']);
                })
                ->latest()
                ->first();

            // Si hay comentario del staff y no hay respuesta del cliente en X días
            if ($lastStaffComment &&
                (!$lastClientComment || $lastClientComment->created_at->isBefore($lastStaffComment->created_at)) &&
                $lastStaffComment->created_at->addDays($inactiveDays)->isPast()) {

                // Marcar el ticket para cierre
                $ticket->marked_for_closure_at = now();
                $ticket->save();

                // Notificar al cliente
                $ticket->user->notify(new TicketInactivityWarningNotification($ticket));

                // Notificar al asignado (si existe)
                $assignedUser = $ticket->department?->users()->first(); // Ajustar según tu modelo de asignación
                if ($assignedUser) {
                    $assignedUser->notify(new TicketInactivityWarningNotification($ticket));
                }

                // Agregar comentario interno
                $ticket->comments()->create([
                    'user_id' => 1, // ID del sistema
                    'content' => "Este ticket ha sido marcado para cierre automático por inactividad. Se cerrará en 72 horas si no hay respuesta del cliente.",
                    'is_internal' => true,
                ]);

                $markedCount++;
            }
        }

        $this->info("Se marcaron {$markedCount} tickets para cierre por inactividad.");
    }
}
