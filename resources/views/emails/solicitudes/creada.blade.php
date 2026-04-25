<x-mail::message>
# Hola, {{ $destinatarioTipo === 'vendedor' ? $solicitud->vendedor->nombre : $solicitud->usuario->nombre }}

@if($destinatarioTipo === 'vendedor')
Se te ha asignado una nueva solicitud de visita a terreno en el sistema TerrenoSur.
@else
Hemos recibido exitosamente tu solicitud de visita a terreno.
@endif

**Detalles de la Cita:**
- **Terreno:** {{ $solicitud->terreno->nombre }} ({{ $solicitud->terreno->ubicacion }})
- **Fecha:** {{ $solicitud->fecha_visita->format('d/m/Y') }}
- **Horario:** {{ substr($solicitud->hora_inicio, 0, 5) }} a {{ substr($solicitud->hora_fin, 0, 5) }}

@if($destinatarioTipo === 'vendedor')
- **Cliente:** {{ $solicitud->usuario->nombre }} ({{ $solicitud->usuario->email }})

<x-mail::button :url="route('vendedor.solicitudes.show', $solicitud->id)">
Gestionar Solicitud
</x-mail::button>
@else
- **Vendedor Asignado:** {{ $solicitud->vendedor->nombre }}

En breve estaremos gestionando tu solicitud y te informaremos en cuanto haya sido aprobada o si surge algún inconveniente de agenda.
@endif

Gracias,<br>
{{ config('app.name', 'TerrenoSur') }}
</x-mail::message>
