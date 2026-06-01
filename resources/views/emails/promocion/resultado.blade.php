<x-mail::message>
# Hola, {{ explode(' ', $vendedor->nombre)[0] }}

Te notificamos sobre el resultado de tu solicitud de promoción **"{{ $promocion->titulo }}"** ({{ number_format($promocion->descuento_porcentaje, 0) }}% desc.) para tu propiedad.

@if ($aprobada)
Tu promoción ha sido **aprobada** y ya se encuentra activa en el catálogo de compradores, destacando tu propiedad en las búsquedas prioritarias.
@else
Tu promoción ha sido **rechazada** tras la revisión de nuestro equipo.

**Motivo del rechazo:**
<x-mail::panel>
{{ $promocion->motivo_rechazo ?? 'No especificado.' }}
</x-mail::panel>

Por favor, revisa las observaciones e intenta postular una nueva promoción si lo deseas.
@endif

<x-mail::button :url="config('app.url') . '/vendedor/dashboard'">
Ir al Panel de Vendedor
</x-mail::button>

Gracias por confiar en nosotros,<br>
{{ config('app.name') }}
</x-mail::message>
