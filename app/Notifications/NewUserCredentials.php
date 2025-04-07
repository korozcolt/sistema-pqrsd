<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserCredentials extends Notification implements ShouldQueue
{
    use Queueable;

    protected $password;

    public function __construct(
        public User $user,
        string $password
    ) {
        $this->password = $password;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Credenciales de acceso al sistema de tickets')
            ->greeting('¡Hola ' . $this->user->name . '!')
            ->line('Se ha creado una cuenta para ti en nuestro sistema de tickets.')
            ->line('Puedes usar estas credenciales para consultar y responder a tus tickets:')
            ->line('Email: ' . $this->user->email)
            ->line('Contraseña: ' . $this->password)
            ->line('Te recomendamos guardar esta información en un lugar seguro.')
            ->action('Acceder al sistema de tickets', url('/tickets'))
            ->line('¡Gracias por contactarnos!')
            ->salutation('Atentamente, el equipo de soporte');
    }
}
