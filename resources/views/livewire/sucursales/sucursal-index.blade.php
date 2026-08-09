<div x-data>
    <x-slot:title x-text="$store.i18n.t('page.branches') || 'Sucursales'">Sucursales</x-slot:title>

    <div class="animate-slide-up">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="kpi-icon kpi-icon--blue">
                    <span class="material-symbols-outlined">storefront</span>
                </div>
                <div>
                    <flux:heading size="xl">Sucursales</flux:heading>
                    <flux:subheading>Administración de locales de la clínica.</flux:subheading>
                </div>
            </div>
            <a href="{{ route('sucursales.crear') }}" class="w-full sm:w-auto btn-primary justify-center">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span>Nueva Sucursal</span>
            </a>
        </div>

        <x-vc-table-layout 
            :data="$sucursales"
            icon="storefront"
            emptyTitle="Sin sucursales"
            emptyText="No hay sucursales registradas o que coincidan con la búsqueda."
            searchModel="busqueda"
            searchPlaceholder="Buscar por nombre, dirección..."
        >
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                @foreach($sucursales as $sucursal)
                    <div class="vc-card flex flex-col justify-between p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow relative">
                        {{-- Badge de estado / principal --}}
                        <div class="absolute top-4 right-4 flex gap-2">
                            @if($sucursal->is_main)
                                <span class="badge badge-blue">Sede Principal</span>
                            @endif
                            @if($sucursal->is_active)
                                <span class="badge badge-emerald">Activa</span>
                            @else
                                <span class="badge badge-zinc">Inactiva</span>
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
                                <p class="text-xs text-zinc-500 uppercase tracking-wider">{{ $sucursal->email ?? 'Sin correo' }}</p>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="space-y-3 mb-6 flex-1">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-zinc-400 mt-0.5">location_on</span>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-xs text-zinc-500 uppercase tracking-wider font-semibold">Dirección</p>
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 line-clamp-2" title="{{ $sucursal->address }}">{{ $sucursal->address ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-zinc-400 mt-0.5">call</span>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-xs text-zinc-500 uppercase tracking-wider font-semibold">Teléfono</p>
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 truncate">{{ $sucursal->phone ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-1.5 items-center">
                            <a href="{{ route('sucursales.editar', $sucursal) }}" class="vc-btn-action vc-btn-edit" x-bind:data-vc-tooltip="$store.i18n.t('btn.edit') || 'Editar'">
                                <span class="material-symbols-outlined icon-sm">edit</span>
                            </a>
                            @if(!$sucursal->is_main)
                                <button type="button" class="vc-btn-action vc-btn-delete" x-bind:data-vc-tooltip="$store.i18n.t('btn.delete') || 'Eliminar'"
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
    <flux:modal :closable="false" name="confirmar-eliminacion" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Eliminar Sucursal</flux:heading>
                <flux:subheading>
                    ¿Estás seguro que deseas eliminar esta sucursal? Esta acción no se puede deshacer y puede afectar registros vinculados.
                </flux:subheading>
            </div>
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button wire:click="eliminar" variant="danger" icon="trash">
                    Eliminar
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

