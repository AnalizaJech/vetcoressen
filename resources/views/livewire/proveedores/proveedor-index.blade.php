<div x-data>
    <x-slot:title>Proveedores</x-slot:title>

    <div class="animate-slide-up">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="kpi-icon kpi-icon--emerald">
                    <span class="material-symbols-outlined">local_shipping</span>
                </div>
                <div>
                    <flux:heading size="xl"><span x-text="$store.i18n.t('page.providers') || 'Proveedores'"></span></flux:heading>
                    <flux:subheading><span x-text="$store.i18n.t('page.providersSub') || 'Gestión de proveedores e importadores.'"></span></flux:subheading>
                </div>
            </div>
            <a href="{{ route('proveedores.crear') }}" class="w-full sm:w-auto btn-primary justify-center">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span><span x-text="$store.i18n.t('btn.newSupplier') || 'Nuevo Proveedor'">Nuevo Proveedor</span></span>
            </a>
        </div>

        <x-vc-table-layout 
            :data="$proveedores"
            icon="local_shipping"
            emptyTitle="Sin proveedores"
            emptyText="No hay proveedores que coincidan con los filtros."
            emptyTitleKey="table.emptySuppliersTitle"
            emptyTextKey="table.emptySuppliersText"
        >
            <x-slot:filters>
                <x-vc-dropdown
                    wire:model.live="filtroProveedor"
                    :options="$proveedoresOptions"
                    placeholder="filter.allSuppliers"
                    :selected="$filtroProveedor"
                    searchable
                    class="w-full sm:w-64"
                />
            </x-slot:filters>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                @foreach($proveedores as $proveedor)
                    <div class="vc-card flex flex-col justify-between p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow relative">
                        {{-- Cabecera Card: Avatar, Nombre y Badge --}}
                        <div class="flex items-start gap-3 mb-5">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex flex-shrink-0 items-center justify-center font-bold text-emerald-600 dark:text-emerald-400">
                                {{ substr($proveedor->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start gap-2">
                                    <h3 class="font-bold text-zinc-800 dark:text-zinc-100 line-clamp-2 leading-tight" title="{{ $proveedor->name }}">
                                        {{ $proveedor->name }}
                                    </h3>
                                    @if($proveedor->is_active)
                                        <span class="badge badge-emerald flex-shrink-0 mt-0.5" x-text="$store.i18n.t('status.ACTIVO') || 'Activo'">Activo</span>
                                    @else
                                        <span class="badge badge-zinc flex-shrink-0 mt-0.5" x-text="$store.i18n.t('status.INACTIVO') || 'Inactivo'">Inactivo</span>
                                    @endif
                                </div>
                                <p class="text-xs text-zinc-500 mt-1">RUC: {{ $proveedor->ruc ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Contacto Info --}}
                        <div class="space-y-3 mb-6 flex-1">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-violet-500 mt-0.5">person</span>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-xs text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.contact') || 'Contacto'">Contacto</p>
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 truncate" x-text="@js($proveedor->contact_name) || $store.i18n.t('report.notRegistered')">{{ $proveedor->contact_name ?: 'No registrado' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-blue-500 mt-0.5">call</span>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-xs text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.phone') || 'Teléfono'">Teléfono</p>
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 truncate" x-text="@js($proveedor->phone) || $store.i18n.t('report.notRegistered')">{{ $proveedor->phone ?: 'No registrado' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-amber-500 mt-0.5">mail</span>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-xs text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.email') || 'Correo'">Correo</p>
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 truncate" x-text="@js($proveedor->email) || $store.i18n.t('report.notRegistered')">{{ $proveedor->email ?: 'No registrado' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-1.5 items-center">
                            <button type="button" class="vc-btn-action vc-btn-view" x-bind:
                                wire:click="ver({{ $proveedor->id }})">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </button>
                            <a href="{{ route('proveedores.editar', $proveedor) }}" class="vc-btn-action vc-btn-edit" x-bind:>
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            <button type="button" class="vc-btn-action vc-btn-delete" x-bind:
                                @click="$wire.confirmDeletion({{ $proveedor->id }}).then(() => Flux.modal('confirmar-eliminacion').show())">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-center">
                {{ $proveedores->links() }}
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
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal_extra.deleteSupplier') || 'Eliminar Proveedor'">Eliminar Proveedor</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal_extra.deleteSupplierMsg') || 'Esta acción no se puede deshacer.'">Esta acción no se puede deshacer.</p>
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

    {{-- Modal Ver Proveedor --}}
    <flux:modal :closable="false" name="ver-proveedor" class="w-[90vw] md:w-full max-w-lg overflow-y-auto max-h-[85vh]">
        @if($proveedorVer)
        <div class="space-y-4">
            <div class="flex justify-between items-start border-b border-zinc-100 dark:border-zinc-700/50 pb-4 pr-6">
                <div>
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-500">local_shipping</span>
                        {{ $proveedorVer->name }}
                    </h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">badge</span>
                        RUC: {{ $proveedorVer->ruc ?? '-' }}
                    </p>
                </div>
                <div>
                    <span class="badge {{ $proveedorVer->is_active ? 'badge-emerald' : 'badge-zinc' }}">
                        <span class="material-symbols-outlined text-xs mr-1">{{ $proveedorVer->is_active ? 'check_circle' : 'cancel' }}</span>
                        <span x-text="$store.i18n.t('status.{{ $proveedorVer->is_active ? 'ACTIVO' : 'INACTIVO' }}') || '{{ $proveedorVer->is_active ? 'Activo' : 'Inactivo' }}'">{{ $proveedorVer->is_active ? 'Activo' : 'Inactivo' }}</span>
                    </span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex flex-col">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider" x-text="$store.i18n.t('table.contact') || 'Contacto'">Contacto</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $proveedorVer->contact_name ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider" x-text="$store.i18n.t('table.phone') || 'Teléfono'">Teléfono</span>
                        <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $proveedorVer->phone ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider" x-text="$store.i18n.t('table.email') || 'Correo'">Correo</span>
                        <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $proveedorVer->email ?? '-' }}</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider" x-text="$store.i18n.t('table.address') || 'Dirección'">Dirección</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $proveedorVer->address ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.country') || 'País'">País</span>
                        <span class="text-xs font-medium text-zinc-900 dark:text-white truncate">{{ $proveedorVer->country ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.state') || 'Estado/Depto'">Estado/Depto</span>
                        <span class="text-xs font-medium text-zinc-900 dark:text-white truncate">{{ $proveedorVer->state ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.city') || 'Ciudad'">Ciudad</span>
                        <span class="text-xs font-medium text-zinc-900 dark:text-white truncate">{{ $proveedorVer->city ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-700/50 flex justify-end">
                <flux:modal.close class="overflow-y-auto max-h-[85vh]">
                    <button type="button" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 border-none px-4 py-2 md:px-5 md:py-2.5 lg:px-6 lg:py-3 rounded-lg font-medium flex items-center justify-center gap-2 w-full sm:w-auto transition-colors">
                        <span x-text="$store.i18n.t('btn.close') || 'Cerrar'">Cerrar</span>
                    </button>
                </flux:modal.close>
            </div>
        </div>
        @endif
    </flux:modal>
</div>


