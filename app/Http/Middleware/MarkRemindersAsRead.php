<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MarkRemindersAsRead
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si estamos viendo un ticket en Filament
        if (str_contains($request->url(), '/admin/tickets/') && !str_contains($request->url(), '/edit')) {
            $segments = explode('/', $request->path());
            $ticketId = end($segments);

            if (is_numeric($ticketId)) {
                // Marcar como leídos los reminders del ticket para el usuario actual
                Auth::user()
                    ->reminders()
                    ->where('ticket_id', $ticketId)
                    ->where('is_read', false)
                    ->each(function ($reminder) {
                        $reminder->markAsRead();
                    });
            }
        }

        return $next($request);
    }
}
