<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NewTicketCommentNotification extends Notification implements ShouldQueue  // Nombre cambiado
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public TicketComment $comment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Nuevo Comentario - Ticket #{$this->ticket->ticket_number}")
            ->greeting('¡Hola!')
            ->line("Se ha agregado un nuevo comentario al ticket #{$this->ticket->ticket_number}.")
            ->line("Título del ticket: {$this->ticket->title}")
            ->line(new HtmlString('Comentario: ' . nl2br($this->comment->content)))
            ->line("Comentado por: {$this->comment->user->name}")
            ->action('Ver Ticket', url("/admin/tickets/{$this->ticket->id}"));

        if ($this->comment->is_internal) {
            $message->line('Este es un comentario interno.');
        }

        return $message->salutation('Saludos');
    }
}
