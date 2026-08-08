<div>
    <x-slot:title x-text="$store.i18n.t('page.clients')">Clients</x-slot:title>

<div class="animate-slide-up">
    {{-- Cabecera con icono --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="kpi-icon kpi-icon--emerald">
                <span class="material-symbols-outlined">group</span>
            </div>
            <div>
                <flux:heading size="xl"><span x-text="$store.i18n.t('page.clients')"></span></flux:heading>
                <flux:subheading><span x-text="$store.i18n.t('page.clientsSub')"></span></flux:subheading>
            </div>
        </div>
        <div class="w-full sm:w-auto mt-2 sm:mt-0">
            <a href="{{ route('clientes.crear') }}" class="w-full sm:w-auto btn-primary justify-center">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newClient')"></span>
            </a>
        </div>
    </div>

    <x-vc-table-layout 
        :data="$clientes"
        icon="group"
        emptyTitle="Sin clientes"
        emptyText="No hay clientes que coincidan con los filtros."
        :searchable="true"
        searchModel="busqueda"
        x-bind:searchPlaceholder="$store.i18n.t('btn.search') || 'Buscar...'"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            @foreach($clientes as $cliente)
                <div class="vc-card flex flex-col justify-between p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow relative">
                    {{-- Avatar y Nombre --}}
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl">person</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $cliente->nombre_completo }}</h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="material-symbols-outlined text-[14px] text-sky-500">badge</span>
                                <span class="text-xs font-medium text-sky-600 dark:text-sky-500">{{ $cliente->tipo_documento }}: {{ $cliente->numero_documento }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Info Principal --}}
                    <div class="space-y-3 mb-6 flex-1">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.phone')"></p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="material-symbols-outlined text-emerald-500 icon-sm">phone</span>
                                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-300 truncate">{{ $cliente->phone ?? '-' }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.pets')"></p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="material-symbols-outlined text-amber-500 icon-sm">pets</span>
                                    <p class="text-sm font-bold text-amber-600 dark:text-amber-500">{{ $cliente->mascotas_count }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.email')"></p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="material-symbols-outlined text-blue-500 icon-sm">mail</span>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-300 truncate" title="{{ $cliente->email }}">{{ $cliente->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-1.5 items-center">
                        <button type="button" class="vc-btn-action vc-btn-view" data-vc-tooltip="Ver" x-bind:data-vc-tooltip="$store.i18n.t('btn.view') || 'Ver'" 
                            @click="$wire.ver({{ $cliente->id }}); $dispatch('modal-show', { name: 'ver-cliente' })">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                        <a href="{{ route('clientes.editar', $cliente) }}" class="vc-btn-action vc-btn-edit" data-vc-tooltip="Editar" x-bind:data-vc-tooltip="$store.i18n.t('btn.edit') || 'Editar'">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <button type="button" class="vc-btn-action vc-btn-delete" data-vc-tooltip="Eliminar" x-bind:data-vc-tooltip="$store.i18n.t('btn.delete') || 'Eliminar'"
                            @click="$wire.set('clienteEliminarId', {{ $cliente->id }}); $dispatch('modal-show', { name: 'confirmar-eliminar' })"
                        >
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $clientes->links() }}
        </div>
    </x-vc-table-layout>
</div>

    {{-- Modal de confirmacion --}}
    <flux:modal :closable="false" name="confirmar-eliminar" class="w-[90vw] md:w-full max-w-md">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">warning</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white">Eliminar Cliente</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed">Esta acción no se puede revertir y perderás toda la información asociada a este registro.</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.cancel') === 'btn.cancel' ? 'Cancelar' : $store.i18n.t('btn.cancel')">Cancelar</span></flux:button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto btn-danger font-medium justify-center" wire:click="eliminar" x-on:click="$dispatch('modal-close', { name: 'confirmar-eliminar' })">
                    <span x-text="$store.i18n.t('btn.delete') === 'btn.delete' ? 'Eliminar' : $store.i18n.t('btn.delete')">Eliminar</span>
                </button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Ver Cliente --}}
    <flux:modal :closable="false" name="ver-cliente" class="w-[90vw] md:w-full max-w-2xl">
        @if($clienteVer)
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-sky-500/10 flex items-center justify-center text-sky-500 shrink-0">
                    <span class="material-symbols-outlined text-3xl">person</span>
                </div>
                <div>
                    <flux:heading size="xl" class="font-bold">{{ $clienteVer->nombre_completo }}</flux:heading>
                    <flux:subheading class="flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-sm">badge</span>
                        {{ $clienteVer->tipo_documento }}: {{ $clienteVer->numero_documento }}
                    </flux:subheading>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                <div>
                    <div class="flex items-center gap-2 text-zinc-500 mb-1">
                        <span class="material-symbols-outlined text-sm">mail</span>
                        <p class="text-xs uppercase tracking-wider font-semibold">Email</p>
                    </div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 pl-6">{{ $clienteVer->email ?? '-' }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-zinc-500 mb-1">
                        <span class="material-symbols-outlined text-sm">phone</span>
                        <p class="text-xs uppercase tracking-wider font-semibold">Teléfono</p>
                    </div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 pl-6">{{ $clienteVer->phone ?? '-' }}</p>
                </div>
                <div class="col-span-1 sm:col-span-2">
                    <div class="flex items-center gap-2 text-zinc-500 mb-1">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        <p class="text-xs uppercase tracking-wider font-semibold">Dirección</p>
                    </div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 pl-6">
                        {{ $clienteVer->address ? $clienteVer->address . ', ' : '' }}
                        {{ $clienteVer->city ?? '' }}
                        {{ $clienteVer->state ? ' - ' . $clienteVer->state : '' }}
                        {{ $clienteVer->country ? ' (' . $clienteVer->country . ')' : '' }}
                        @if(!$clienteVer->address && !$clienteVer->city && !$clienteVer->state && !$clienteVer->country)
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-zinc-500 mb-1">
                        <span class="material-symbols-outlined text-sm">pets</span>
                        <p class="text-xs uppercase tracking-wider font-semibold">Mascotas Registradas</p>
                    </div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 pl-6">{{ $clienteVer->mascotas->count() }}</p>
                </div>
            </div>
            
            @if($clienteVer->notes)
            <div>
                <div class="flex items-center gap-2 text-zinc-500 mb-2">
                    <span class="material-symbols-outlined text-sm">notes</span>
                    <p class="text-xs uppercase tracking-wider font-semibold">Notas</p>
                </div>
                <div class="p-3 bg-zinc-50 dark:bg-vc-surface-alt/50 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 ml-6">
                    {{ $clienteVer->notes }}
                </div>
            </div>
            @endif

            <div class="flex justify-end mt-4 w-full">
                <flux:modal.close class="w-full sm:w-auto">
                    <button type="button" class="btn-primary bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 border-none px-4 py-2 font-medium flex items-center justify-center gap-2 w-full">
                        <span x-text="$store.i18n.t('btn.close') === 'btn.close' ? 'Cerrar' : $store.i18n.t('btn.close')">Cerrar</span>
                    </button>
                </flux:modal.close>
            </div>
        </div>
        @endif
    </flux:modal>
</div>

