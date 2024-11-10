<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Models\Reminder;
use Carbon\Carbon;
use App\Enums\ReminderType;
use App\Notifications\TicketReminderNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class CreateTicketReminder
{
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;

        if ($ticket->resolution_due_date) {
            $reminder = Reminder::create([
                'ticket_id' => $ticket->id,
                'sent_to' => $ticket->user_id,
                'reminder_type' => ReminderType::ResolutionDue,
                'sent_at' => Carbon::parse($ticket->resolution_due_date)->subDay(),
            ]);

            // Notificar al usuario y al administrador
            $ticket->user->notify(new TicketReminderNotification($ticket, 'resolution_due'));
            Notification::route('mail', Config::get('site.pqrs_email'))
                ->notify(new TicketReminderNotification($ticket, 'resolution_due'));
        }

        // Recordatorio para tiempo de resolución
        if ($ticket->resolution_due_date) {
            $reminderSentAt = Carbon::parse($ticket->resolution_due_date)->subDay();

            Reminder::create([
                'ticket_id' => $ticket->id,
                'sent_to' => $ticket->user_id,
                'reminder_type' => ReminderType::ResolutionDue,
                'sent_at' => $reminderSentAt,
            ]);
        }

        // Recordatorio para tiempo de respuesta
        if ($ticket->response_due_date) {
            $responseReminderSentAt = Carbon::parse($ticket->response_due_date)->subHours(2);

            Reminder::create([
                'ticket_id' => $ticket->id,
                'sent_to' => $ticket->user_id,
                'reminder_type' => ReminderType::ResponseDue,
                'sent_at' => $responseReminderSentAt,
            ]);
        }
    }
}
