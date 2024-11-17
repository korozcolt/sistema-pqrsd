<x-mail::message>
# Nuevo Mensaje desde el Formulario de Contacto

**Detalles del remitente:**

<x-mail::panel>
- **Nombre:** {{ $name }}
- **Email:** {{ $email }}
- **Teléfono:** {{ $phone }}
</x-mail::panel>

## Mensaje
<x-mail::panel>
{{ $messageText }}
</x-mail::panel>

<x-mail::button :url="config('app.url')" color="primary">
Ver en el sitio web
</x-mail::button>

---
Este mensaje fue enviado desde el formulario de contacto de {{ config('app.name') }}.

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
