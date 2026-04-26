<x-mail::message>
# ¡Felicidades, {{ explode(' ', $vendedor->nombre)[0] }}!

Tu publicación del terreno en **{{ $terreno->ubicacion }}** ha sido verificada y **aprobada** por nuestro equipo de administradores.

Tu anuncio ya es visible para todos los compradores en el catálogo público de TerrenoSur.

<x-mail::button :url="config('app.url') . '/catalogo/' . $terreno->id">
Ver mi Publicación
</x-mail::button>

Gracias por confiar en TerrenoSur,<br>
{{ config('app.name') }}
</x-mail::message>
