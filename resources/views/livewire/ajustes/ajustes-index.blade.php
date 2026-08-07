<div x-data>
    <x-slot:title x-text="$store.i18n.t('nav.settings') || 'Configuración'"></x-slot:title>

    {{-- Cabecera con icono --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="kpi-icon kpi-icon--emerald">
                <span class="material-symbols-outlined">settings</span>
            </div>
            <div>
                <flux:heading size="xl"><span x-text="$store.i18n.t('nav.settings') || 'Configuración'"></span></flux:heading>
                <flux:subheading><span x-text="$store.i18n.t('settings.subheading') || 'Configuración general de la cuenta y sistema'"></span></flux:subheading>
            </div>
        </div>
    </div>

    {{-- Tabs Premium --}}
    <div class="mb-8 flex overflow-x-auto hide-scrollbar border-b border-zinc-200 dark:border-zinc-700/50">
        <a href="{{ route('configuracion.index', ['tab' => 'perfil']) }}" wire:navigate
                class="flex items-center gap-2 px-4 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap outline-none focus:outline-none focus:ring-0 {{ $tab === 'perfil' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
            <span class="material-symbols-outlined text-[18px]">person</span>
            <span x-text="$store.i18n.t('settings.profile') || 'Mi Perfil'"></span>
        </a>
        
        @role('super_admin')
            <a href="{{ route('configuracion.index', ['tab' => 'clinica']) }}" wire:navigate
                    class="flex items-center gap-2 px-4 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap outline-none focus:outline-none focus:ring-0 {{ $tab === 'clinica' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                <span class="material-symbols-outlined text-[18px]">storefront</span>
                <span x-text="$store.i18n.t('settings.clinic') || 'Clínica'"></span>
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
