<x-mail::message>
# Receta Médica

Hola {{ $historia->cita->cliente->first_name ?? 'Cliente' }},

Adjuntamos la receta médica correspondiente a la consulta de tu mascota **{{ $historia->mascota->name }}** del día {{ $historia->date->format('d/m/Y') }}.

**Indicaciones:**
{{ $historia->tratamiento_indicaciones }}

<x-mail::button :url="config('app.url')">
Ver Detalles
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
