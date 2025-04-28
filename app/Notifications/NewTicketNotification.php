<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;
use Illuminate\Support\Facades\Config;

class NewTicketNotification extends Notification
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
