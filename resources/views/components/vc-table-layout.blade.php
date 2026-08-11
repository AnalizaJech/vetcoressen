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

<div class="vc-table-wrapper animate-slide-up relative pb-20 md:pb-0">
    {{-- Topbar: Buscador, Filtros y Acciones --}}
    @if($searchable || isset($filters) || isset($actions))
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto flex-1">
                @if($searchable)
                    <div class="w-full sm:max-w-xs relative">
                        <flux:input
                            wire:model.live.debounce.300ms="{{ $searchModel }}"
                            icon="magnifying-glass"
                            placeholder="{{ $searchPlaceholder }}"
                            x-bind:placeholder="{{ $searchPlaceholderKey ? '$store.i18n.t(\''.$searchPlaceholderKey.'\') || \''.$searchPlaceholder.'\'' : 'null' }}"
                        />
                    </div>
                @endif
                
                @if(isset($filters))
                    <div class="flex gap-2 w-full sm:w-auto">
                        {{ $filters }}
                    </div>
                @endif
            </div>

            @if(isset($actions))
                <div class="w-full sm:w-auto mt-2 sm:mt-0 flex gap-2 justify-end">
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
        <div class="vc-empty-state my-8">
            <div class="vc-empty-icon">
                <span class="material-symbols-outlined">{{ $icon }}</span>
            </div>
            <p class="vc-empty-title" {!! $emptyTitleKey ? 'x-text="$store.i18n.t(\''.$emptyTitleKey.'\') || \''.$emptyTitle.'\'"' : '' !!}>{{ $emptyTitle }}</p>
            <p class="vc-empty-text" {!! $emptyTextKey ? 'x-text="$store.i18n.t(\''.$emptyTextKey.'\') || \''.$emptyText.'\'"' : '' !!}>{{ $emptyText }}</p>
        </div>
    @else
        <div class="vc-responsive-table-container">
            {{ $slot }}
        </div>
    @endif
</div>
