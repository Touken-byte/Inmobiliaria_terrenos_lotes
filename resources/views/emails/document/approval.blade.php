<x-mail::message>
# ¡Felicidades, {{ explode(' ', $vendedor->nombre)[0] }}!

Su documento de identidad ha sido verificado exitosamente por nuestro equipo de administradores.

Ya tiene acceso completo a todas las funciones del Módulo de Ventas de TerrenoSur.

<x-mail::button :url="config('app.url') . '/vendedor/dashboard'">
Ir a Mi Dashboard
</x-mail::button>

Gracias por confiar en TerrenoSur,<br>
{{ config('app.name') }}
</x-mail::message>
