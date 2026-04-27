<x-mail::message>
# Hola, {{ explode(' ', $vendedor->nombre)[0] }}

Te informamos que tu publicación del terreno en **{{ $terreno->ubicacion }}** ha sido **rechazada** tras la revisión de nuestro equipo.

**Motivo del rechazo:**
<x-mail::panel>
{{ $motivo }}
</x-mail::panel>

Por favor, ingresa a tu panel de vendedor para corregir los detalles mencionados y volver a enviar tu publicación a revisión.

<x-mail::button :url="config('app.url') . '/vendedor/terrenos/editar/' . $terreno->id">
Corregir Publicación
</x-mail::button>

Si tienes dudas, contacta con soporte.<br>
{{ config('app.name') }}
</x-mail::message>
