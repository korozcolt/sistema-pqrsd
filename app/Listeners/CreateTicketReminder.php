<?php

namespace App\Listeners;

use App\Events\TicketCreatedEvent;
use App\Models\Reminder;
use App\Enums\ReminderType;
use Carbon\Carbon;

class CreateTicketReminder
{
    public function handle(TicketCreatedEvent $event): void
    {
        $ticket = $event->ticket;

        if ($ticket->response_due_date) {
            // Recordatorio para mitad de tiempo de respuesta
            $halfTimeResponse = Carbon::parse($ticket->created_at)
                ->addHours(Carbon::parse($ticket->response_due_date)->diffInHours($ticket->created_at) / 2);

            Reminder::create([
                'ticket_id' => $ticket->id,
                'sent_to' => $ticket->user_id,
                'reminder_type' => ReminderType::HalfTimeResponse,
                'sent_at' => $halfTimeResponse,
            ]);

            // Recordatorio 24h antes del vencimiento de respuesta
            Reminder::create([
                'ticket_id' => $ticket->id,
                'sent_to' => $ticket->user_id,
                'reminder_type' => ReminderType::DayBeforeResponse,
                'sent_at' => Carbon::parse($ticket->response_due_date)->subDay(),
            ]);
        }

        if ($ticket->resolution_due_date) {
            // Recordatorio para mitad de tiempo de resolución
            $halfTimeResolution = Carbon::parse($ticket->created_at)
                ->addHours(Carbon::parse($ticket->resolution_due_date)->diffInHours($ticket->created_at) / 2);

            Reminder::create([
                'ticket_id' => $ticket->id,
                'sent_to' => $ticket->user_id,
                'reminder_type' => ReminderType::HalfTimeResolution,
                'sent_at' => $halfTimeResolution,
            ]);

            // Recordatorio 24h antes del vencimiento de resolución
            Reminder::create([
                'ticket_id' => $ticket->id,
                'sent_to' => $ticket->user_id,
                'reminder_type' => ReminderType::DayBeforeResolution,
                'sent_at' => Carbon::parse($ticket->resolution_due_date)->subDay(),
            ]);
        }
    }
}
