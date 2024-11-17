<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Validation\ValidationRule;

class Recaptcha implements ValidationRule
{
    /**
     * Validate the reCAPTCHA response.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $value,
                'ip' => request()->ip(),
            ]);

            if (!$response->successful() || !$response->json('success')) {
                $fail('La verificación de reCAPTCHA ha fallado. Por favor, inténtalo de nuevo.');
            }
        } catch (\Exception $e) {
            $fail('Error al verificar reCAPTCHA. Por favor, inténtalo de nuevo.');
        }
    }
}
