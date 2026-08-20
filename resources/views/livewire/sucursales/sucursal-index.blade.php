<div x-data>
    <x-slot:title>Sucursales</x-slot:title>

    <div class="animate-slide-up">
        {{-- ═══ Header de Sucursales (Estándar Premium) ═══ --}}
        <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined text-2xl">storefront</span>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                        <span x-text="$store.i18n.t('page.branches') || 'Sucursales'">Sucursales</span>
                    </h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('page.branchesSub') || 'Administración de sedes y locales de la clínica'">
                        Administración de sedes y locales de la clínica
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('sucursales.crear') }}" wire:navigate class="btn-primary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined icon-sm">add</span>
                    <span x-text="$store.i18n.t('btn.newBranch') || 'Nueva Sucursal'">Nueva Sucursal</span>
                </a>
            </div>
        </div>

        <x-vc-table-layout 
            :data="$sucursales"
            icon="storefront"
            emptyTitle="Sin sucursales"
            emptyText="No hay sucursales que coincidan con los filtros."
            emptyTitleKey="table.emptyBranchesTitle"
            emptyTextKey="table.emptyBranchesText"
        >
            <x-slot:filters>
                <x-vc-dropdown
                    wire:model.live="filtroSucursal"
                    :options="$sucursalesOptions"
                    placeholder="filter.allBranches"
                    :selected="$filtroSucursal"
                    searchable
                    class="w-48 sm:w-60"
                />
            </x-slot:filters>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                @foreach($sucursales as $sucursal)
                    <div class="vc-card flex flex-col justify-between p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow relative">
                        {{-- Badge de estado / principal --}}
                        <div class="absolute top-4 right-4 flex gap-2">
                            @if($sucursal->is_main)
                                <span class="badge badge-blue" x-text="$store.i18n.t('misc.mainBranch') || 'Sede Principal'">Sede Principal</span>
                            @endif
                            @if($sucursal->is_active)
                                <span class="badge badge-emerald" x-text="$store.i18n.t('status.ACTIVO') || 'Activa'">Activa</span>
                            @else
                                <span class="badge badge-zinc" x-text="$store.i18n.t('status.INACTIVO') || 'Inactiva'">Inactiva</span>
                            @endif
                        </div>

                        {{-- Nombre --}}
                        <div class="flex items-center gap-3 mb-4 mt-2">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center font-bold text-blue-500">
                                <span class="material-symbols-outlined icon-sm">apartment</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-zinc-800 dark:text-zinc-100 truncate pr-16" title="{{ $sucursal->name }}">
                                    {{ $sucursal->name }}
                                </h3>
                                @if($sucursal->ruc)
                                    <p class="text-xs text-zinc-500 uppercase tracking-wider font-semibold">RUC: {{ $sucursal->ruc }}</p>
                                @endif
                                <p class="text-xs text-zinc-500 uppercase tracking-wider" x-text="{!! $sucursal->email ? '`'.$sucursal->email.'`' : '$store.i18n.t(\'misc.noEmail\') || \'Sin correo\'' !!}">{{ $sucursal->email ?? 'Sin correo' }}</p>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="space-y-3 mb-6 flex-1">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-rose-500 mt-0.5">location_on</span>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-xs text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.address') || 'Dirección'">Dirección</p>
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 line-clamp-2" title="{{ $sucursal->address }}">{{ $sucursal->address ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-blue-500 mt-0.5">call</span>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-xs text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.phone') || 'Teléfono'">Teléfono</p>
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 truncate">{{ $sucursal->phone ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-1.5 items-center">
                            <a href="{{ route('sucursales.editar', $sucursal) }}" class="vc-btn-action vc-btn-edit" title="Editar">
                                <span class="material-symbols-outlined icon-sm">edit</span>
                            </a>
                            @if(!$sucursal->is_main)
                                <button type="button" class="vc-btn-action vc-btn-delete" title="Eliminar"
                                    wire:click="confirmDeletion({{ $sucursal->id }})">
                                    <span class="material-symbols-outlined icon-sm">delete</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-center">
                {{ $sucursales->links() }}
            </div>
        </x-vc-table-layout>
    </div>

    {{-- Modal Eliminar --}}
    <flux:modal :closable="false" name="confirmar-eliminacion" class="min-w-88 overflow-y-auto max-h-[85vh]">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">delete</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal_extra.deleteBranch') || 'Eliminar Sucursal'">Eliminar Sucursal</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal_extra.deleteBranchMsg') || '¿Estás seguro que deseas eliminar esta sucursal? Esta acción no se puede deshacer y puede afectar registros vinculados.'">¿Estás seguro que deseas eliminar esta sucursal? Esta acción no se puede deshacer y puede afectar registros vinculados.</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium px-4 py-2 md:px-5 md:py-2.5 lg:px-6 lg:py-3"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></span></flux:button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto btn-danger font-medium justify-center px-4 py-2 md:px-5 md:py-2.5 lg:px-6 lg:py-3" wire:click="eliminar" x-on:click="$dispatch('modal-close', { name: 'confirmar-eliminacion' })">
                    <span x-text="$store.i18n.t('btn.delete') || 'Eliminar'"><span x-text="$store.i18n.t('btn.delete') || 'Eliminar'"></span></span>
                </button>
            </div>
        </div>
    </flux:modal>
</div>

