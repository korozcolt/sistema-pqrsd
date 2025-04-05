<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketComment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $comment,
        public bool $isInternal = false
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nuevo Comentario en Ticket: {$this->ticket->ticket_number}")
            ->line("Se ha agregado un nuevo comentario al ticket")
            ->line("Comentario: {$this->comment}")
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"));
    }
}
