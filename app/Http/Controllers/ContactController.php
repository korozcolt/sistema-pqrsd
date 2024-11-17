<?php

namespace App\Http\Controllers;

use App\Mail\ContactForm;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'g-recaptcha-response' => ['required', new Recaptcha()],
        ], [
            'g-recaptcha-response.required' => 'Por favor, completa la verificación reCAPTCHA.'
        ]);

        try {
            Mail::to(config('mail.from.address'))
                ->send(new ContactForm($validated));

            return back()->with('swal', [
                'icon' => 'success',
                'title' => '¡Mensaje enviado!',
                'text' => 'Gracias por contactarnos. Te responderemos lo antes posible.',
                'confirmButtonText' => 'Aceptar'
            ]);
        } catch (\Exception $e) {
            report($e);
            return back()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => '¡Ups!',
                    'text' => 'Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo.',
                    'confirmButtonText' => 'Entendido'
                ])
                ->withInput();
        }
    }
}
