<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;
use Config;

class TicketCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Ticket $ticket)
    {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nuevo Ticket Creado: {$this->ticket->ticket_number}")
            ->line("Se ha creado un nuevo ticket con el número: {$this->ticket->ticket_number}")
            ->line("Título: {$this->ticket->title}")
            ->line("Tipo: {$this->ticket->type->getLabel()}")
            ->line("Prioridad: {$this->ticket->priority->getLabel()}")
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"))
            ->line('Gracias por usar nuestro sistema de tickets.');
    }
}

class TicketStatusUpdated extends Notification implements ShouldQueue
{
    public function __construct(
        public Ticket $ticket,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Actualización de Ticket: {$this->ticket->ticket_number}")
            ->line("El estado del ticket ha sido actualizado")
            ->line("De: {$this->oldStatus}")
            ->line("A: {$this->newStatus}")
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"));
    }
}

class TicketComment extends Notification implements ShouldQueue
{
    public function __construct(
        public Ticket $ticket,
        public string $comment,
        public bool $isInternal = false
    ) {}

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nuevo Comentario en Ticket: {$this->ticket->ticket_number}")
            ->line("Se ha agregado un nuevo comentario al ticket")
            ->line("Comentario: {$this->comment}")
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"));
    }
}

class TicketReminderNotification extends Notification implements ShouldQueue
{
    public function __construct(
        public Ticket $ticket,
        public string $reminderType
    ) {}

    public function toMail($notifiable): MailMessage
    {
        $isResponse = $this->reminderType === 'response_due';

        return (new MailMessage)
            ->subject("Recordatorio de Ticket: {$this->ticket->ticket_number}")
            ->line($isResponse ?
                "El tiempo de respuesta está por vencer" :
                "El tiempo de resolución está por vencer")
            ->line("Ticket: {$this->ticket->title}")
            ->line("Vence: " . ($isResponse ?
                $this->ticket->response_due_date->format('d/m/Y H:i') :
                $this->ticket->resolution_due_date->format('d/m/Y H:i')))
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"));
    }
}
