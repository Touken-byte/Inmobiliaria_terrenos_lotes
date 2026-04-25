<x-mail::message>
# Hola, {{ explode(' ', $vendedor->nombre)[0] }},

Lamentamos informarle que el documento de identidad que subió ha sido rechazado tras ser revisado.

**Motivo del rechazo:**
{{ $comentario }}

Por favor, acceda a su panel y suba un nuevo documento que cumpla con todos los requisitos para continuar con su verificación.

<x-mail::button :url="config('app.url') . '/vendedor/dashboard'">
Subir Nuevo Documento
</x-mail::button>

Si tiene alguna duda, por favor responda a este correo de los administradores.

Atentamente,<br>
{{ config('app.name') }}
</x-mail::message>
