<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketInactivityWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public int $hoursUntilClose = 72
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Aviso de cierre automático - Ticket #{$this->ticket->ticket_number}")
            ->greeting('¡Importante!')
            ->line("Le informamos que su ticket #{$this->ticket->ticket_number} lleva un tiempo considerable sin actividad.")
            ->line("Título: {$this->ticket->title}")
            ->line("Este ticket se cerrará automáticamente en {$this->hoursUntilClose} horas si no hay respuesta.")
            ->line("Si su consulta ya fue resuelta, no necesita realizar ninguna acción.")
            ->line("Si aún necesita asistencia, por favor responda a este mensaje o ingrese al sistema para actualizar su ticket.")
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"))
            ->line('Gracias por usar nuestro sistema de atención al cliente.');
    }
}
