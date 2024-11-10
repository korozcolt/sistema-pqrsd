<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public $oldStatus,
        public $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Estado Actualizado - Ticket #{$this->ticket->ticket_number}")
            ->greeting('¡Hola!')
            ->line("El estado del ticket #{$this->ticket->ticket_number} ha sido actualizado.")
            ->line("Título: {$this->ticket->title}")
            ->line("Estado anterior: {$this->oldStatus->getLabel()}")
            ->line("Nuevo estado: {$this->newStatus->getLabel()}")
            ->line("Departamento: {$this->ticket->department->name}")
            ->line("Prioridad: {$this->ticket->priority->getLabel()}")
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"))
            ->line('Gracias por usar nuestro sistema de gestión de tickets.')
            ->salutation('Saludos');
    }
}
