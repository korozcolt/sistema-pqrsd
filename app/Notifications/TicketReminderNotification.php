<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Enums\ReminderType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public $reminderType
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Recordatorio de Ticket #{$this->ticket->ticket_number}")
            ->greeting('¡Importante!');

        // Mensaje específico según el tipo de recordatorio
        if (is_string($this->reminderType)) {
            $isResponse = $this->reminderType === 'response_due' || $this->reminderType === 'day_before_response';

            $message->line($isResponse ?
                "El tiempo de respuesta está por vencer" :
                "El tiempo de resolución está por vencer")
                ->line("Ticket: {$this->ticket->title}")
                ->line("Vence: " . ($isResponse ?
                    $this->ticket->response_due_date->format('d/m/Y H:i') :
                    $this->ticket->resolution_due_date->format('d/m/Y H:i')));
        } else {
            // Asumir que es un objeto ReminderType
            switch ($this->reminderType) {
                case ReminderType::HalfTimeResponse:
                    $message->line('Ha transcurrido la mitad del tiempo para responder este ticket.')
                        ->line('Fecha límite de respuesta: ' . $this->ticket->response_due_date->format('d/m/Y H:i'));
                    break;

                case ReminderType::DayBeforeResponse:
                    $message->line('Falta menos de 24 horas para que venza el tiempo de respuesta.')
                        ->line('Fecha límite de respuesta: ' . $this->ticket->response_due_date->format('d/m/Y H:i'));
                    break;

                case ReminderType::HalfTimeResolution:
                    $message->line('Ha transcurrido la mitad del tiempo para resolver este ticket.')
                        ->line('Fecha límite de resolución: ' . $this->ticket->resolution_due_date->format('d/m/Y H:i'));
                    break;

                case ReminderType::DayBeforeResolution:
                    $message->line('Falta menos de 24 horas para que venza el tiempo de resolución.')
                        ->line('Fecha límite de resolución: ' . $this->ticket->resolution_due_date->format('d/m/Y H:i'));
                    break;

                default:
                    $message->line('Recordatorio de ticket pendiente.')
                        ->line('Por favor revise los plazos de respuesta y resolución.');
            }
        }

        return $message
            ->line('Detalles del Ticket:')
            ->line("Título: {$this->ticket->title}")
            ->line("Tipo: {$this->ticket->type->getLabel()}")
            ->line("Prioridad: {$this->ticket->priority->getLabel()}")
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"))
            ->line('Por favor, tome las acciones necesarias.')
            ->salutation('Saludos');
    }
}
