<!DOCTYPE html>
<html lang="es" x-data x-init="$store.theme.init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
    <meta name="description" content="{{ config('app.name') }} - Sistema de Gestión Veterinaria">

    <!-- Configuración de Favicon para Laravel -->
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />

    {{-- Google Fonts: Plus Jakarta Sans + DM Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">


    {{-- Material Symbols Outlined: ahora self-hosted via Vite (npm material-symbols) --}}

    {{-- CSS Design System --}}
    <link rel="stylesheet" href="{{ asset('css/vetcoressen.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Theme & i18n Scripts (antes de Alpine para registrar stores) --}}
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="{{ asset('js/i18n.js') }}"></script>

    @livewireStyles
</head>
<body class="h-screen overflow-hidden overflow-x-hidden font-sans antialiased selection:bg-emerald-500 selection:text-white" style="background-color: var(--vc-bg); color: var(--vc-text);" x-data="{ sidebarOpen: false, clinicName: '{{ addslashes(\App\Models\Clinic::first()->name ?? config('app.name')) }}' }" @clinic-updated.window="clinicName = $event.detail[0].name">

    <div class="h-screen overflow-hidden flex flex-col md:flex-row md:p-4 md:gap-4">
        {{-- Overlay Movil con Glass Blur --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-40 bg-black/70 md:hidden backdrop-blur-md transition-opacity"></div>

        {{-- ═══ SIDEBAR ═══ --}}
        <aside class="fixed md:static inset-y-0 left-0 z-50 w-64 vc-glass-sidebar border-r md:border flex flex-col transition-transform duration-300 ease-in-out shrink-0 vc-mobile-sidebar md:rounded-3xl shadow-sm overflow-hidden" :class="{'is-open': sidebarOpen}" style="border-color: var(--vc-border);">
            @php
                $clinic = \App\Models\Clinic::first();
                $clinicName = $clinic->name ?? config('app.name');
                $clinicLogo = $clinic->logo ? asset('storage/' . $clinic->logo) : asset('favicon.svg');
            @endphp
            {{-- Brand / Logo --}}
            <div class="h-16 flex items-center justify-between px-5" style="border-bottom: 1px solid var(--vc-border);">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 group focus:outline-none">
                    <div class="vc-sidebar-logo shrink-0" style="background: transparent;">
                    <img src="{{ $clinicLogo }}" alt="{{ $clinicName }} Logo" class="w-12 h-12 object-contain">
                </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-base tracking-wider font-display" style="color: var(--vc-text);" x-text="clinicName"></span>
                        <span class="text-[10px] font-medium tracking-widest uppercase" style="color: var(--vc-emerald-light); opacity: 0.8;" x-text="$store.i18n.t('sidebar.clinic')"></span>
                    </div>
                </a>
            </div>

            {{-- Navegacion --}}
            <nav class="flex-1 overflow-y-auto p-4 space-y-6">
                {{-- Principal --}}
                <div x-data="{ open: {{ request()->routeIs('dashboard') || request()->routeIs('reportes.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between sidebar-section-title outline-none focus:outline-none focus:ring-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">home</span>
                            <span x-text="$store.i18n.t('sidebar.main')"></span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="space-y-1 mt-2">
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">dashboard</span>
                            <span x-text="$store.i18n.t('sidebar.dashboard')"></span>
                        </a>
                        <a href="{{ route('reportes.index') }}" class="sidebar-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">analytics</span>
                            <span x-text="$store.i18n.t('sidebar.reports') || 'Reportes'"></span>
                        </a>
                    </div>
                </div>

                {{-- Gestion Medica --}}
                <div x-data="{ open: {{ request()->routeIs('clientes.*', 'mascotas.*', 'citas.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between sidebar-section-title outline-none focus:outline-none focus:ring-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">medical_services</span>
                            <span x-text="$store.i18n.t('sidebar.medical')"></span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="space-y-1 mt-2">
                        <a href="{{ route('clientes.index') }}" class="sidebar-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">group</span>
                            <span x-text="$store.i18n.t('sidebar.clients')"></span>
                        </a>
                        <a href="{{ route('mascotas.index') }}" class="sidebar-link {{ request()->routeIs('mascotas.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">pets</span>
                            <span x-text="$store.i18n.t('sidebar.pets')"></span>
                        </a>
                        <a href="{{ route('citas.index') }}" class="sidebar-link {{ request()->routeIs('citas.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">calendar_month</span>
                            <span x-text="$store.i18n.t('sidebar.appointments')"></span>
                        </a>
                    </div>
                </div>

                {{-- Atencion Clinica --}}
                <div x-data="{ open: {{ request()->routeIs('historias.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between sidebar-section-title outline-none focus:outline-none focus:ring-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">stethoscope</span>
                            <span x-text="$store.i18n.t('sidebar.clinical')"></span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="space-y-1 mt-2">
                        <a href="{{ route('historias.index') }}" class="sidebar-link {{ request()->routeIs('historias.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">clinical_notes</span>
                            <span x-text="$store.i18n.t('sidebar.records')"></span>
                        </a>
                    </div>
                </div>

                {{-- Operaciones --}}
                <div x-data="{ open: {{ request()->routeIs('inventario.*', 'caja.*', 'proveedores.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between sidebar-section-title outline-none focus:outline-none focus:ring-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">storefront</span>
                            <span x-text="$store.i18n.t('sidebar.operations')"></span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="space-y-1 mt-2">
                        <a href="{{ route('inventario.index') }}" class="sidebar-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">inventory_2</span>
                            <span x-text="$store.i18n.t('sidebar.inventory')"></span>
                        </a>
                        <a href="{{ route('caja.index') }}" class="sidebar-link {{ request()->routeIs('caja.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">point_of_sale</span>
                            <span x-text="$store.i18n.t('sidebar.cashier')"></span>
                        </a>
                        <a href="{{ route('proveedores.index') }}" class="sidebar-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">local_shipping</span>
                            <span x-text="$store.i18n.t('sidebar.suppliers') || 'Proveedores'"></span>
                        </a>
                    </div>
                </div>

                {{-- Administracion --}}
                @role('super_admin')
                <div x-data="{ open: {{ request()->routeIs('usuarios.*') || request()->routeIs('roles.*') || request()->routeIs('sucursales.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between sidebar-section-title outline-none focus:outline-none focus:ring-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                            <span x-text="$store.i18n.t('sidebar.administration')"></span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="space-y-1 mt-2">
                        <a href="{{ route('usuarios.index') }}" class="sidebar-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">group</span>
                            <span x-text="$store.i18n.t('sidebar.users')"></span>
                        </a>
                        <a href="{{ route('roles.index') }}" class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">shield_person</span>
                            <span x-text="$store.i18n.t('sidebar.roles')"></span>
                        </a>
                        <a href="{{ route('sucursales.index') }}" class="sidebar-link {{ request()->routeIs('sucursales.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">storefront</span>
                            <span x-text="$store.i18n.t('sidebar.branches') || 'Sucursales'"></span>
                        </a>
                    </div>
                </div>
                @endrole
                {{-- Configuración --}}
                <div>
                    <a href="{{ route('configuracion.index') }}" class="sidebar-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined sidebar-icon">settings</span>
                        <span x-text="$store.i18n.t('sidebar.settings')"></span>
                    </a>
                </div>

                {{-- Preferencias Adicionales --}}
                <div class="pt-2 space-y-1">
                    {{-- Dark/Light Mode Toggle --}}
                    <button type="button" @click="$store.theme.toggle()" class="w-full sidebar-link flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined sidebar-icon" x-text="$store.theme.isDark ? 'dark_mode' : 'light_mode'"></span>
                            <span x-text="$store.i18n.t('sidebar.darkMode')"></span>
                        </div>
                        <div class="vc-switch pointer-events-none" :class="{ 'active': $store.theme.isDark }" aria-label="Toggle dark mode"></div>
                    </button>

                    {{-- Language Toggle --}}
                    <button type="button" @click="$store.i18n.toggle()" class="w-full sidebar-link flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined sidebar-icon">translate</span>
                            <span x-text="$store.i18n.t('sidebar.language')"></span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md uppercase transition-colors" 
                              style="background: var(--vc-emerald-glow); color: var(--vc-emerald-light);" 
                              x-text="$store.i18n.locale === 'en' ? 'EN' : 'ES'"></span>
                    </button>
                        
                        {{-- Logout --}}
                        <div class="pt-4 mt-2" style="border-top: 1px solid var(--vc-border);">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full sidebar-link sidebar-link-logout text-red-500 border border-transparent">
                                    <span class="material-symbols-outlined sidebar-icon text-red-500">logout</span>
                                    <span x-text="$store.i18n.t('sidebar.logout')"></span>
                                </button>
                            </form>
                        </div>
                    </div>
            </nav>
        </aside>

        {{-- ═══ AREA PRINCIPAL ═══ --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden md:rounded-3xl shadow-sm md:border relative" style="border-color: var(--vc-border); background: var(--vc-bg);">
            {{-- Header Movil --}}
            <header class="h-16 flex items-center justify-between px-4 md:hidden z-30 sticky top-0" style="border-bottom: 1px solid var(--vc-border); background: var(--vc-glass-bg); backdrop-filter: blur(12px);">
                <button @click="sidebarOpen = true" class="p-2 rounded-xl bg-white/50 dark:bg-black/20 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-vc-primary transition-colors">
                    <span class="material-symbols-outlined icon-md">menu</span>
                </button>
                <div class="flex items-center gap-3">
                    <img src="{{ $clinicLogo }}" alt="{{ $clinicName }} Logo" class="w-8 h-8 object-contain">
                    <span class="font-extrabold text-base tracking-wider font-display text-zinc-800 dark:text-zinc-100" x-text="clinicName"></span>
                </div>
                <div class="w-10"></div>
            </header>



            {{-- Contenido Principal --}}
            <main class="flex-1 p-4 md:p-6 lg:p-8 overflow-y-auto z-10 relative">
                {{ $slot }}
            </main>
        </div>
    </div>

    <livewire:session-timeout-warning />

    @if(session('mensaje'))
        <input type="hidden" id="flash-message-data" value="{{ session('mensaje') }}">
    @endif

    {{-- Global Success Modal --}}
    <flux:modal :closable="false" name="global-success-modal" class="w-[90vw] md:w-full max-w-md">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-emerald-100/50 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center border border-emerald-200 dark:border-emerald-500/30 shadow-sm shadow-emerald-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;" id="global-success-icon">check_circle</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" id="global-success-title"></h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" id="global-success-text"></p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.close') || 'Cerrar'"></span></flux:button>
                </flux:modal.close>
                <flux:modal.close class="w-full sm:w-auto">
                    <button type="button" id="global-success-btn" class="w-full sm:w-auto btn-primary font-medium justify-center">
                        <span x-text="$store.i18n.t('btn.accept', 'Aceptar')"></span>
                    </button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <script>
        document.addEventListener('livewire:navigated', () => {
            checkSessionMessage();
        });
        document.addEventListener('DOMContentLoaded', () => {
            checkSessionMessage();
        });

        // Event listener for dispatch('notify') from Livewire components
        document.addEventListener('notify', (event) => {
            let data = event.detail;
            if (Array.isArray(data)) data = data[0];
            showGlobalModal(data.type || 'success', data.message);
        });

        function checkSessionMessage() {
            let msgInput = document.getElementById('flash-message-data');
            if (msgInput && msgInput.value) {
                showGlobalModal('success', msgInput.value);
                msgInput.value = '';
            }
        }

        function showGlobalModal(type, message) {
            let fallbackTitle = type === 'error' ? 'Error' : 'Éxito';
            let title = window.Alpine?.store('i18n')?.t('alert.' + type, fallbackTitle) || fallbackTitle;
            document.getElementById('global-success-title').innerText = title;
            
            let translatedMsg = window.Alpine?.store('i18n')?.t(message, message) || message;
            document.getElementById('global-success-text').innerText = translatedMsg;
            
            let iconEl = document.getElementById('global-success-icon');
            let iconContainer = iconEl.parentElement;
            let btn = document.getElementById('global-success-btn');
            
            if (type === 'error') {
                iconEl.innerText = 'error';
                iconContainer.className = 'w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10';
                if(btn) btn.className = 'w-full sm:w-auto btn-danger font-medium justify-center';
            } else {
                iconEl.innerText = 'check_circle';
                iconContainer.className = 'w-20 h-20 bg-emerald-100/50 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center border border-emerald-200 dark:border-emerald-500/30 shadow-sm shadow-emerald-500/10';
                if(btn) btn.className = 'w-full sm:w-auto btn-primary font-medium justify-center';
            }

            // Show modal using Flux via custom event
            window.dispatchEvent(new CustomEvent('modal-show', { detail: { name: 'global-success-modal' } }));
        }
    </script>



    @fluxScripts
    @livewireScripts
</body>
</html>
