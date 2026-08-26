@props([
    'data', // La colección paginada
    'icon' => 'table_rows',
    'emptyTitle' => 'No hay registros',
    'emptyText' => 'No se encontraron datos para mostrar.',
    'emptyTitleKey' => null,
    'emptyTextKey' => null,
    'searchable' => false,
    'searchModel' => 'busqueda',
    'searchPlaceholder' => 'Buscar...',
    'searchPlaceholderKey' => null,
])

@php
    $effectiveKey = $searchPlaceholderKey ?? (str_contains($searchPlaceholder, '.') ? $searchPlaceholder : null);
@endphp

<div class="vc-table-wrapper animate-slide-up relative pb-20 md:pb-0">
    {{-- Topbar: Buscador, Filtros y Acciones en línea --}}
    @if($searchable || isset($filters) || isset($actions))
        <div class="vc-panel mb-6 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3.5">
            <div class="flex flex-wrap items-center gap-3 flex-1 min-w-0">
                @if($searchable)
                    <div class="w-full sm:w-72 relative shrink-0">
                        <flux:input
                            wire:model.live.debounce.300ms="{{ $searchModel }}"
                            icon="magnifying-glass"
                            placeholder="{{ $searchPlaceholder }}"
                            @if($effectiveKey)
                                x-bind:placeholder="$store.i18n.t('{{ $effectiveKey }}') || '{{ $searchPlaceholder }}'"
                            @endif
                        />
                    </div>
                @endif
                
                @if(isset($filters))
                    <div class="flex flex-wrap items-center gap-2.5">
                        {{ $filters }}
                    </div>
                @endif
            </div>

            @if(isset($actions))
                <div class="flex items-center gap-2 justify-end shrink-0 mt-1 lg:mt-0">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex class="absolute inset-0 z-10 bg-white/50 dark:bg-vc-surface/50 backdrop-blur-sm flex items-center justify-center rounded-xl" style="display: none;">
        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium">
            <span class="material-symbols-outlined vc-spinner text-2xl">progress_activity</span>
            <span x-text="$store.i18n.t('table.loading') || 'Cargando...'">Cargando...</span>
        </div>
    </div>

    {{-- Content --}}
    @if(isset($data) && $data->isEmpty())
        <div class="vc-empty-state my-6 w-full flex flex-col items-center justify-center text-center p-12 bg-white dark:bg-vc-surface border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-xs">
            <div class="vc-empty-icon w-16 h-16 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80 flex items-center justify-center text-zinc-400 dark:text-zinc-500 mb-4 shadow-xs">
                <span class="material-symbols-outlined text-3xl">{{ $icon }}</span>
            </div>
            <p class="vc-empty-title text-base font-bold text-zinc-800 dark:text-zinc-200 mb-1" {!! $emptyTitleKey ? 'x-text="$store.i18n.t(\''.$emptyTitleKey.'\') || \''.$emptyTitle.'\'"' : '' !!}>{{ $emptyTitle }}</p>
            <p class="vc-empty-text text-xs text-zinc-500 dark:text-zinc-400 max-w-sm" {!! $emptyTextKey ? 'x-text="$store.i18n.t(\''.$emptyTextKey.'\') || \''.$emptyText.'\'"' : '' !!}>{{ $emptyText }}</p>
        </div>
    @else
        <div class="vc-responsive-table-container">
            {{ $slot }}
        </div>
    @endif
</div>
