<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Models\Reminder;
use Carbon\Carbon;

class CreateTicketReminder
{
    /**
     * Handle the event.
     */
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;

        // Calcular la fecha y hora de envío del recordatorio (por ejemplo, 24 horas antes de la fecha límite)
        $reminderSentAt = Carbon::parse($ticket->resolution_due_date)->subDay();

        // Crear el recordatorio
        Reminder::create([
            'ticket_id' => $ticket->id,
            'sent_to' => $ticket->user_id, // Asumiendo que el recordatorio se envía al usuario que creó el ticket
            'reminder_type' => 'Ticket Resolution Due', // Tipo de recordatorio
            'sent_at' => $reminderSentAt,
        ]);
    }
}
