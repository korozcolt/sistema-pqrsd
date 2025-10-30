<?php

namespace App\Observers;

use App\Models\TicketComment;
use App\Notifications\NewTicketCommentNotification;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Notification;

class TicketCommentObserver
{
    /**
     * Handle the TicketComment "created" event.
     */
    public function created(TicketComment $comment): void
    {
        $ticket = $comment->ticket;
        $commentUser = $comment->user;

        // Si el comentario es del cliente (user_web), notificar al staff
        if ($commentUser->role === UserRole::UserWeb) {
            // Notificar al email de administración
            Notification::route('mail', env('TICKET_NOTIFICATION_EMAIL', 'soporte@torcoromaweb.com'))
                ->notify(new NewTicketCommentNotification($ticket, $comment));
        }
        // Si el comentario es del staff, notificar al cliente
        else {
            // Notificar al usuario del ticket
            $ticket->user->notify(new NewTicketCommentNotification($ticket, $comment));
        }
    }
}
