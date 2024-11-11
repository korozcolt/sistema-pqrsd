<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\Reminder;
use App\Enums\ReminderType;
use App\Notifications\TicketReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class ProcessTicketReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Obtener tickets activos que necesitan recordatorios
        $tickets = Ticket::whereNotIn('status', ['closed', 'resolved', 'rejected'])
            ->whereNotNull('response_due_date')
            ->whereNotNull('resolution_due_date')
            ->get();

        foreach ($tickets as $ticket) {
            $this->processResponseReminders($ticket);
            $this->processResolutionReminders($ticket);
        }
    }

    private function processResponseReminders(Ticket $ticket): void
    {
        $now = now();
        $responseDueDate = Carbon::parse($ticket->response_due_date);
        $halfTimeResponse = Carbon::parse($ticket->created_at)
            ->addHours($responseDueDate->diffInHours($ticket->created_at) / 2);
        $dayBeforeResponse = $responseDueDate->copy()->subDay();

        // Verificar mitad de tiempo de respuesta
        if ($now->isAfter($halfTimeResponse) && !$this->hasReminder($ticket, ReminderType::HalfTimeResponse)) {
            $this->createReminder($ticket, ReminderType::HalfTimeResponse);
        }

        // Verificar 24h antes del vencimiento de respuesta
        if ($now->isAfter($dayBeforeResponse) && !$this->hasReminder($ticket, ReminderType::DayBeforeResponse)) {
            $this->createReminder($ticket, ReminderType::DayBeforeResponse);
        }
    }

    private function processResolutionReminders(Ticket $ticket): void
    {
        $now = now();
        $resolutionDueDate = Carbon::parse($ticket->resolution_due_date);
        $halfTimeResolution = Carbon::parse($ticket->created_at)
            ->addHours($resolutionDueDate->diffInHours($ticket->created_at) / 2);
        $dayBeforeResolution = $resolutionDueDate->copy()->subDay();

        // Verificar mitad de tiempo de resolución
        if ($now->isAfter($halfTimeResolution) && !$this->hasReminder($ticket, ReminderType::HalfTimeResolution)) {
            $this->createReminder($ticket, ReminderType::HalfTimeResolution);
        }

        // Verificar 24h antes del vencimiento de resolución
        if ($now->isAfter($dayBeforeResolution) && !$this->hasReminder($ticket, ReminderType::DayBeforeResolution)) {
            $this->createReminder($ticket, ReminderType::DayBeforeResolution);
        }
    }

    private function hasReminder(Ticket $ticket, ReminderType $type): bool
    {
        return $ticket->reminders()
            ->where('reminder_type', $type)
            ->exists();
    }

    private function createReminder(Ticket $ticket, ReminderType $type): void
    {
        $reminder = Reminder::create([
            'ticket_id' => $ticket->id,
            'sent_to' => $ticket->user_id,
            'reminder_type' => $type,
            'sent_at' => now(),
        ]);

        // Notificar al usuario
        $ticket->user->notify(new TicketReminderNotification($ticket, $type));

        // Notificar al email de PQRs
        Notification::route('mail', config('site.pqrs_email'))
            ->notify(new TicketReminderNotification($ticket, $type));
    }
}
