<x-mail::message>
# Hola, {{ explode(' ', $vendedor->nombre)[0] }}

Te notificamos sobre el resultado de la revisión de tu publicación de alquiler **"{{ $alquiler->titulo }}"** (Ubicación: {{ $alquiler->ubicacion }}).

@if ($aprobado)
Tu publicación ha sido **aprobada** y ya se encuentra activa en el catálogo público de alquileres.
@else
Tu publicación ha sido **rechazada** tras la revisión de nuestro equipo de administración.

**Motivo del rechazo:**
<x-mail::panel>
{{ $alquiler->motivo_rechazo ?? 'No especificado.' }}
</x-mail::panel>

Por favor, revisa las observaciones del alquiler, edita la información correspondiente y vuelve a solicitar la aprobación.
@endif

<x-mail::button :url="config('app.url') . '/vendedor/alquileres'">
Ir a mis Alquileres
</x-mail::button>

Gracias por confiar en nosotros,<br>
{{ config('app.name') }}
</x-mail::message>
