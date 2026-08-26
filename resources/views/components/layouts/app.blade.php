<!DOCTYPE html>
<html lang="es" x-data x-init="$store.theme.init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $cleanTitle = trim(str_replace(' - VETCORESSEN', '', $title ?? 'Vetcoressen'));
        $lowerTitle = mb_strtolower($cleanTitle);
        $titleSuffix = '';

        if (str_starts_with($lowerTitle, 'historia clínica - ') || str_starts_with($lowerTitle, 'historia clinica - ') || str_starts_with($lowerTitle, 'medical record - ')) {
            $lowerTitle = 'ver_historia';
            $titleSuffix = ' ' . mb_substr($cleanTitle, mb_strpos($cleanTitle, '-'));
        } elseif (str_starts_with($lowerTitle, 'historia clínica #') || str_starts_with($lowerTitle, 'historia clinica #') || str_starts_with($lowerTitle, 'medical record #')) {
            $lowerTitle = 'ver_historia';
            $titleSuffix = ' ' . mb_substr($cleanTitle, mb_strpos($cleanTitle, '#'));
        } elseif (str_starts_with($lowerTitle, 'historial clínico - ') || str_starts_with($lowerTitle, 'historial clinico - ') || str_starts_with($lowerTitle, 'medical history - ')) {
            $lowerTitle = 'historial_mascota';
            $titleSuffix = ' ' . mb_substr($cleanTitle, mb_strpos($cleanTitle, '-'));
        }

        $titleAliases = [
            'dashboard' => 'dashboard',
            'panel' => 'dashboard',
            'pets' => 'pets',
            'mascotas' => 'pets',
            'mascota' => 'nueva_mascota',
            'nueva mascota' => 'nueva_mascota',
            'new pet' => 'nueva_mascota',
            'editar mascota' => 'editar_mascota',
            'edit pet' => 'editar_mascota',
            'clients' => 'clients',
            'clientes' => 'clients',
            'cliente' => 'nuevo_cliente',
            'nuevo cliente' => 'nuevo_cliente',
            'new client' => 'nuevo_cliente',
            'editar cliente' => 'editar_cliente',
            'edit client' => 'editar_cliente',
            'appointments' => 'appointments',
            'citas' => 'appointments',
            'cita' => 'nueva_cita',
            'nueva cita' => 'nueva_cita',
            'new appointment' => 'nueva_cita',
            'editar cita' => 'editar_cita',
            'edit appointment' => 'editar_cita',
            'inventory' => 'inventory',
            'inventario' => 'inventory',
            'producto' => 'nuevo_producto',
            'nuevo producto' => 'nuevo_producto',
            'new product' => 'nuevo_producto',
            'editar producto' => 'editar_producto',
            'edit product' => 'editar_producto',
            'recepcionar pedido (entrada de stock)' => 'entrada_productos',
            'entrada de productos' => 'entrada_productos',
            'entrada de stock' => 'entrada_productos',
            'stock entry' => 'entrada_productos',
            'historias clínicas' => 'records',
            'historias clinicas' => 'records',
            'historial clínico' => 'records',
            'historial clinico' => 'records',
            'medical records' => 'records',
            'historia clínica' => 'nueva_historia',
            'historia clinica' => 'nueva_historia',
            'medical record' => 'nueva_historia',
            'nueva historia clínica' => 'nueva_historia',
            'nueva historia' => 'nueva_historia',
            'new medical record' => 'nueva_historia',
            'editar historia clínica' => 'editar_historia',
            'editar historia' => 'editar_historia',
            'edit medical record' => 'editar_historia',
            'ver_historia' => 'ver_historia',
            'historial_mascota' => 'historial_mascota',
            'cashier' => 'cashier',
            'caja' => 'cashier',
            'point of sale' => 'point_of_sale',
            'punto de venta' => 'point_of_sale',
            'arqueo de caja' => 'arqueo_caja',
            'cash count' => 'arqueo_caja',
            'proveedores' => 'suppliers',
            'proveedor' => 'nuevo_proveedor',
            'suppliers' => 'suppliers',
            'nuevo proveedor' => 'nuevo_proveedor',
            'new supplier' => 'nuevo_proveedor',
            'editar proveedor' => 'editar_proveedor',
            'edit supplier' => 'editar_proveedor',
            'sucursales' => 'branches',
            'sucursal' => 'nueva_sucursal',
            'branches' => 'branches',
            'nueva sucursal' => 'nueva_sucursal',
            'new branch' => 'nueva_sucursal',
            'editar sucursal' => 'editar_sucursal',
            'edit branch' => 'editar_sucursal',
            'usuarios' => 'users',
            'usuario' => 'nuevo_usuario',
            'users' => 'users',
            'nuevo usuario' => 'nuevo_usuario',
            'new user' => 'nuevo_usuario',
            'editar usuario' => 'editar_usuario',
            'edit user' => 'editar_usuario',
            'roles y permisos' => 'roles_and_permissions',
            'roles & permissions' => 'roles_and_permissions',
            'roles' => 'roles_and_permissions',
            'rol' => 'nuevo_rol',
            'nuevo rol' => 'nuevo_rol',
            'new role' => 'nuevo_rol',
            'editar rol' => 'editar_rol',
            'edit role' => 'editar_rol',
            'reportes y estadísticas' => 'reports',
            'reports & statistics' => 'reports',
            'reportes' => 'reports',
            'reports' => 'reports',
            'ajustes' => 'settings',
            'configuración' => 'settings',
            'configuracion' => 'settings',
            'settings' => 'settings',
        ];

        $titleKey = $titleAliases[$lowerTitle] ?? null;
    @endphp
    <meta name="current-title-key" content="{{ $titleKey }}">
    <meta name="current-title-suffix" content="{{ $titleSuffix }}">
    <title>{{ $cleanTitle }} - VETCORESSEN</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    {{-- Chart.js para Dashboard y Reportes (Versión UMD estable) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/i18n.js') }}"></script>
    <script>
        // Sincronización robusta de tema (VC Theme & Flux Appearance)
        function applyAppTheme(isDark) {
            const themeStr = isDark ? 'dark' : 'light';
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                document.documentElement.setAttribute('data-theme', 'light');
            }
            try {
                localStorage.setItem('vc_theme', themeStr);
                localStorage.setItem('flux.appearance', themeStr);
                document.cookie = "vc_theme=" + themeStr + "; path=/; max-age=31536000; SameSite=Lax";
                document.cookie = "flux_appearance=" + themeStr + "; path=/; max-age=31536000; SameSite=Lax";
            } catch(e) {}
        }

        // Pre-render theme check to prevent flickering
        (function() {
            const saved = localStorage.getItem('vc_theme') || localStorage.getItem('flux.appearance');
            const isDark = saved ? (saved === 'dark') : false; // Default to light if not explicitly set to dark
            applyAppTheme(isDark);
        })();

        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                isDark: (localStorage.getItem('vc_theme') || localStorage.getItem('flux.appearance')) === 'dark',
                init() {
                    this.apply();
                },
                toggle() {
                    this.isDark = !this.isDark;
                    applyAppTheme(this.isDark);
                    this.apply();
                },
                apply() {
                    applyAppTheme(this.isDark);
                    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: this.isDark } }));
                }
            });
        });

        // Asegurar persistencia durante navegación Livewire SPA
        document.addEventListener('livewire:navigated', () => {
            const saved = localStorage.getItem('vc_theme') || localStorage.getItem('flux.appearance');
            const isDark = saved === 'dark';
            applyAppTheme(isDark);
            if (window.Alpine && Alpine.store('theme')) {
                Alpine.store('theme').isDark = isDark;
            }
        });
    </script>
    @fluxAppearance
</head>
<body class="h-screen max-h-screen overflow-hidden antialiased text-zinc-800 dark:text-zinc-100 flex flex-col font-sans" style="background-color: var(--vc-bg);">

    @php
        $clinic = \App\Models\Clinic::first();
        $clinicName = $clinic->name ?? 'VETCORESSEN';
        $clinicLogo = ($clinic && $clinic->logo && file_exists(public_path('storage/' . $clinic->logo))) ? asset('storage/' . $clinic->logo) : asset('favicon.svg');
    @endphp

    <div class="flex-1 flex h-screen max-h-screen overflow-hidden p-2 md:p-3 lg:p-4 gap-2 md:gap-3" 
         x-data="{ sidebarOpen: false, clinicName: '{{ addslashes($clinicName) }}' }"
         @configuracion-actualizada.window="clinicName = $event.detail.clinica_nombre || clinicName">
        
        {{-- Backdrop Movil --}}
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-black/60 backdrop-blur-xs z-40 md:hidden"
             x-cloak></div>

        {{-- ═══ SIDEBAR NAVEGACION ═══ --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               class="fixed md:static inset-y-0 left-0 z-50 w-64 lg:w-72 h-full flex flex-col transition-transform duration-300 ease-in-out rounded-2xl md:rounded-3xl shadow-sm border shrink-0 overflow-hidden"
               style="background-color: var(--vc-sidebar-bg); border-color: var(--vc-border);">
            
            {{-- Logo / Header Clinica --}}
            <div class="h-16 flex items-center justify-between px-5 shrink-0" style="border-bottom: 1px solid var(--vc-border);">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 group focus:outline-none">
                    <div class="shrink-0 flex items-center justify-center">
                        <img src="{{ $clinicLogo }}" alt="{{ $clinicName }} Logo" class="w-10 h-10 object-contain rounded-lg">
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-extrabold text-sm tracking-wider font-display uppercase truncate" style="color: var(--vc-text);" x-text="clinicName"></span>
                        <span class="text-[10px] font-semibold tracking-widest uppercase truncate" style="color: var(--vc-primary-light); opacity: 0.9;" x-text="$store.i18n.t('sidebar.clinic')"></span>
                    </div>
                </a>
            </div>

            {{-- Navegacion con scroll interno --}}
            <nav class="flex-1 overflow-y-auto p-3 space-y-4 vc-custom-scroll pr-2">
                {{-- Principal --}}
                <div x-data="{ open: {{ request()->routeIs('dashboard') || request()->routeIs('reportes.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between sidebar-section-title outline-none focus:outline-none focus:ring-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px]">home</span>
                            <span x-text="$store.i18n.t('sidebar.main') || 'Main'">Main</span>
                        </div>
                        <span class="material-symbols-outlined text-[13px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="flex flex-col space-y-1 mt-1.5">
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">dashboard</span>
                            <span x-text="$store.i18n.t('sidebar.dashboard') || 'Dashboard'">Dashboard</span>
                        </a>
                        <a href="{{ route('reportes.index') }}" class="sidebar-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">analytics</span>
                            <span x-text="$store.i18n.t('sidebar.reports') || 'Reports & Statistics'">Reports & Statistics</span>
                        </a>
                    </div>
                </div>

                {{-- Gestion Medica --}}
                <div x-data="{ open: {{ request()->routeIs('clientes.*', 'mascotas.*', 'citas.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between sidebar-section-title outline-none focus:outline-none focus:ring-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px]">medical_services</span>
                            <span x-text="$store.i18n.t('sidebar.medical') || 'Medical Management'">Medical Management</span>
                        </div>
                        <span class="material-symbols-outlined text-[13px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="flex flex-col space-y-1 mt-1.5">
                        <a href="{{ route('clientes.index') }}" class="sidebar-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">group</span>
                            <span x-text="$store.i18n.t('sidebar.clients') || 'Clients'">Clients</span>
                        </a>
                        <a href="{{ route('mascotas.index') }}" class="sidebar-link {{ request()->routeIs('mascotas.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">pets</span>
                            <span x-text="$store.i18n.t('sidebar.pets') || 'Pets'">Pets</span>
                        </a>
                        <a href="{{ route('citas.index') }}" class="sidebar-link {{ request()->routeIs('citas.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">calendar_month</span>
                            <span x-text="$store.i18n.t('sidebar.appointments') || 'Appointments'">Appointments</span>
                        </a>
                    </div>
                </div>

                {{-- Atencion Clinica --}}
                <div x-data="{ open: {{ request()->routeIs('historias.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between sidebar-section-title outline-none focus:outline-none focus:ring-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px]">stethoscope</span>
                            <span x-text="$store.i18n.t('sidebar.clinical') || 'Clinical Care'">Clinical Care</span>
                        </div>
                        <span class="material-symbols-outlined text-[13px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="flex flex-col space-y-1 mt-1.5">
                        <a href="{{ route('historias.index') }}" class="sidebar-link {{ request()->routeIs('historias.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">clinical_notes</span>
                            <span x-text="$store.i18n.t('sidebar.records') || 'Medical Records'">Medical Records</span>
                        </a>
                    </div>
                </div>

                {{-- Operaciones --}}
                <div x-data="{ open: {{ request()->routeIs('inventario.*', 'caja.*', 'proveedores.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between sidebar-section-title outline-none focus:outline-none focus:ring-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px]">storefront</span>
                            <span x-text="$store.i18n.t('sidebar.operations') || 'Operations'">Operations</span>
                        </div>
                        <span class="material-symbols-outlined text-[13px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="flex flex-col space-y-1 mt-1.5">
                        <a href="{{ route('inventario.index') }}" class="sidebar-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">inventory_2</span>
                            <span x-text="$store.i18n.t('sidebar.inventory') || 'Inventory & Stock'">Inventory & Stock</span>
                        </a>
                        <a href="{{ route('caja.index') }}" class="sidebar-link {{ request()->routeIs('caja.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">point_of_sale</span>
                            <span x-text="$store.i18n.t('sidebar.cashier') || 'Cash Register / POS'">Cash Register / POS</span>
                        </a>
                        <a href="{{ route('proveedores.index') }}" class="sidebar-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">local_shipping</span>
                            <span x-text="$store.i18n.t('sidebar.suppliers') || 'Suppliers'">Suppliers</span>
                        </a>
                    </div>
                </div>

                {{-- Administracion --}}
                @role('super_admin')
                <div x-data="{ open: {{ request()->routeIs('usuarios.*') || request()->routeIs('roles.*') || request()->routeIs('sucursales.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between sidebar-section-title outline-none focus:outline-none focus:ring-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px]">admin_panel_settings</span>
                            <span x-text="$store.i18n.t('sidebar.administration') || 'Administration'">Administration</span>
                        </div>
                        <span class="material-symbols-outlined text-[13px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="flex flex-col space-y-1 mt-1.5">
                        <a href="{{ route('usuarios.index') }}" class="sidebar-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">group</span>
                            <span x-text="$store.i18n.t('sidebar.users') || 'Users'">Users</span>
                        </a>
                        <a href="{{ route('roles.index') }}" class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">shield_person</span>
                            <span x-text="$store.i18n.t('sidebar.roles') || 'Roles & Permissions'">Roles & Permissions</span>
                        </a>
                        <a href="{{ route('sucursales.index') }}" class="sidebar-link {{ request()->routeIs('sucursales.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined sidebar-icon">storefront</span>
                            <span x-text="$store.i18n.t('sidebar.branches') || 'Branches'">Branches</span>
                        </a>
                    </div>
                </div>
                @endrole

                {{-- Configuración --}}
                <div>
                    <a href="{{ route('configuracion.index') }}" class="sidebar-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined sidebar-icon">settings</span>
                        <span x-text="$store.i18n.t('sidebar.settings') || 'Settings'">Settings</span>
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
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden rounded-2xl md:rounded-3xl shadow-sm border relative" style="border-color: var(--vc-border); background-color: var(--vc-surface);">
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
            <main class="flex-1 p-4 md:p-6 lg:p-8 overflow-y-auto vc-custom-scroll z-10 relative">
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
    <script>
        window.updateDocumentTitle = function() {
            const meta = document.querySelector('meta[name="current-title-key"]');
            const suffixMeta = document.querySelector('meta[name="current-title-suffix"]');
            const suffix = suffixMeta ? suffixMeta.content : '';
            if (meta && meta.content && window.Alpine && Alpine.store('i18n')) {
                const key = 'title.' + meta.content;
                const translated = Alpine.store('i18n').t(key);
                if (translated && translated !== key) {
                    document.title = translated + (suffix || '') + ' - VETCORESSEN';
                }
            }
        };

        document.addEventListener('livewire:navigated', () => { setTimeout(updateDocumentTitle, 50); });
        window.addEventListener('language-changed', () => { updateDocumentTitle(); });
        document.addEventListener('alpine:init', () => {
            setTimeout(updateDocumentTitle, 150);
        });
        setTimeout(updateDocumentTitle, 150);
    </script>
</body>
</html>
