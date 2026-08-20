<div x-data>
    <x-slot:title>Configuracion</x-slot:title>

    {{-- Header de Configuracion (Estandar Premium) --}}
    <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <span class="material-symbols-outlined text-2xl">settings</span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                    <span x-text="$store.i18n.t('nav.settings') || 'Configuración'">Configuración</span>
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('settings.subheading') || 'Configuración general de la cuenta y parámetros del sistema'">
                    Configuración general de la cuenta y parámetros del sistema
                </p>
            </div>
        </div>
    </div>

    {{-- Tabs Premium --}}
    <div class="mb-8 flex overflow-x-auto hide-scrollbar border-b border-zinc-200 dark:border-zinc-700/50">
        <a href="{{ route('configuracion.index', ['tab' => 'perfil']) }}" wire:navigate
                class="flex items-center gap-2 px-4 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap outline-none focus:outline-none focus:ring-0 {{ $tab === 'perfil' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
            <span class="material-symbols-outlined text-[18px]">person</span>
            <span x-text="$store.i18n.t('settings.profile') || 'Mi Perfil'">Mi Perfil</span>
        </a>
        
        @role('super_admin')
            <a href="{{ route('configuracion.index', ['tab' => 'clinica']) }}" wire:navigate
                    class="flex items-center gap-2 px-4 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap outline-none focus:outline-none focus:ring-0 {{ $tab === 'clinica' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                <span class="material-symbols-outlined text-[18px]">storefront</span>
                <span x-text="$store.i18n.t('settings.clinic') || 'Clínica'">Clínica</span>
            </a>
        @endrole
    </div>

    {{-- Contenido de los tabs --}}
    <div class="mt-4 animate-in fade-in slide-in-from-bottom-2 duration-300">
        @if($tab === 'perfil')
            <livewire:ajustes.perfil-form />
        @endif

        @role('super_admin')
            @if($tab === 'clinica')
                <livewire:ajustes.clinica-form />
            @endif
        @endrole
    </div>
</div>
