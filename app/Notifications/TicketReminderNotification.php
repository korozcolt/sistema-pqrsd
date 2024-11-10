<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Enums\ReminderType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class TicketReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public ReminderType $reminderType
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isResponseDue = $this->reminderType === ReminderType::ResponseDue;
        $dueDate = $isResponseDue ?
            $this->ticket->response_due_date :
            $this->ticket->resolution_due_date;

        $hoursLeft = now()->diffInHours($dueDate, false);
        $urgencyLevel = $this->getUrgencyLevel($hoursLeft);

        return (new MailMessage)
            ->subject("Recordatorio Importante - Ticket #{$this->ticket->ticket_number}")
            ->greeting('¡Atención!')
            ->line($this->getReminderMessage($isResponseDue))
            ->line("Título del ticket: {$this->ticket->title}")
            ->line("Tipo: {$this->ticket->type->getLabel()}")
            ->line("Prioridad: {$this->ticket->priority->getLabel()}")
            ->line("Departamento: {$this->ticket->department->name}")
            ->line($this->getTimeMessage($dueDate, $hoursLeft))
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"))
            ->level($urgencyLevel)
            ->salutation('Saludos');
    }

    private function getReminderMessage(bool $isResponseDue): string
    {
        return $isResponseDue
            ? "El tiempo para dar la primera respuesta está por vencer."
            : "El tiempo para resolver el ticket está por vencer.";
    }

    private function getTimeMessage(Carbon $dueDate, int $hoursLeft): string
    {
        if ($hoursLeft < 0) {
            return "¡El plazo venció hace " . abs($hoursLeft) . " horas!";
        }

        if ($hoursLeft < 24) {
            return "Quedan {$hoursLeft} horas para el vencimiento.";
        }

        $days = floor($hoursLeft / 24);
        return "Quedan {$days} días para el vencimiento.";
    }

    private function getUrgencyLevel(int $hoursLeft): string
    {
        if ($hoursLeft < 0) return 'error';
        if ($hoursLeft < 24) return 'warning';
        return 'info';
    }
}
