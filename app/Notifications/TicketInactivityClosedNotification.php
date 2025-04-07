<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketInactivityClosedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public int $inactiveDays
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket cerrado por inactividad - #{$this->ticket->ticket_number}")
            ->greeting('Ticket cerrado automáticamente')
            ->line("Le informamos que su ticket #{$this->ticket->ticket_number} ha sido cerrado automáticamente por el sistema debido a {$this->inactiveDays} días de inactividad.")
            ->line("Título: {$this->ticket->title}")
            ->line("Si considera que este ticket no debería cerrarse o necesita asistencia adicional, puede crear un nuevo ticket haciendo referencia a este número de ticket.")
            ->action('Crear nuevo ticket', url("/tickets"))
            ->line('Gracias por usar nuestro sistema de atención al cliente.');
    }
}
