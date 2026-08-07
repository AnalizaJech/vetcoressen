<!DOCTYPE html>
<html lang="en" x-data x-init="$store.theme.init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }} - Sistema de Gestión Veterinaria</title>
    <meta name="description" content="{{ config('app.name') }} - Sistema de Gestión Veterinaria">

    <!-- Configuración de Favicon para Laravel -->
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />

    {{-- Google Fonts: DM Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

    {{-- Google Material Symbols Outlined --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    {{-- CSS Design System --}}
    <link rel="stylesheet" href="{{ asset('css/vetcoressen.css') }}">

    {{-- Tailwind + Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Theme & i18n - antes de Alpine para registrar stores --}}
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="{{ asset('js/i18n.js') }}"></script>

    @livewireStyles
</head>
<body class="h-screen overflow-hidden font-sans flex items-center justify-center" style="background-color: var(--vc-bg); color: var(--vc-text);">
    {{-- Fondo con gradiente sutil --}}
    <div class="fixed inset-0" style="background: radial-gradient(circle at 30% 30%, var(--vc-emerald-glow) 0%, transparent 50%), radial-gradient(circle at 70% 70%, rgba(13, 148, 136, 0.04) 0%, transparent 50%); pointer-events: none;"></div>

    <main class="relative z-10 w-full h-full flex items-center justify-center overflow-y-auto">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
