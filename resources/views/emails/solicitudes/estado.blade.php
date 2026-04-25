<x-mail::message>
# Hola, {{ $solicitud->usuario->nombre }}

Te notificamos que el estado de tu solicitud de visita a terreno ha sido actualizado.

**Nuevo Estado:** <span style="color: {{ $solicitud->estado === 'aprobada' ? 'green' : ($solicitud->estado === 'rechazada' ? 'red' : 'gray') }}; font-weight: bold;">{{ strtoupper($solicitud->estado) }}</span>

@if($solicitud->estado === 'rechazada' && $solicitud->motivo_rechazo)
**Motivo del rechazo:**
{{ $solicitud->motivo_rechazo }}
@endif

**Detalles de la Cita:**
- **Terreno:** {{ $solicitud->terreno->nombre }}
- **Fecha:** {{ $solicitud->fecha_visita->format('d/m/Y') }}
- **Horario:** {{ substr($solicitud->hora_inicio, 0, 5) }} a {{ substr($solicitud->hora_fin, 0, 5) }}
- **Vendedor Asignado:** {{ $solicitud->vendedor->nombre }}

@if($solicitud->estado === 'aprobada')
¡Te esperamos el día y hora acordados! Ante cualquier inquietud o si requieres cancelar (debes hacerlo con 24h de anticipación mínimo), comunícate inmediatamente.
@endif

Gracias por preferirnos,<br>
{{ config('app.name', 'TerrenoSur') }}
</x-mail::message>
