<x-mail::message>
# Nuevo Mensaje de Contacto

<x-mail::panel>
<div style="margin-bottom: 15px;">
    <strong style="color: #374151;">De:</strong> {{ $name }}<br>
    <strong style="color: #374151;">Email:</strong> {{ $email }}<br>
    <strong style="color: #374151;">Teléfono:</strong> {{ $phone }}
</div>
</x-mail::panel>

<x-mail::panel>
<strong style="color: #374151;">Mensaje:</strong><br>
{{ $messageText }}
</x-mail::panel>

<x-mail::button :url="config('app.url')" color="primary">
Ir al sitio web
</x-mail::button>

<x-mail::subcopy>
Este mensaje fue enviado desde el formulario de contacto de {{ config('app.name') }}.
</x-mail::subcopy>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
