<div x-data>
    <x-slot:title x-text="$store.i18n.t('page.inventory')">Inventory</x-slot:title>

<div class="animate-slide-up">
    {{-- Cabecera con icono --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="kpi-icon kpi-icon--emerald">
                <span class="material-symbols-outlined">inventory_2</span>
            </div>
            <div>
                <flux:heading size="xl"><span x-text="$store.i18n.t('page.inventory')"></span></flux:heading>
                <flux:subheading><span x-text="$store.i18n.t('page.inventorySub')"></span></flux:subheading>
            </div>
        </div>
        <div class="w-full sm:w-auto mt-2 sm:mt-0 flex items-center gap-2">
            <a href="{{ route('inventario.entrada') }}" class="w-full sm:w-auto btn-secondary justify-center">
                <span class="material-symbols-outlined icon-sm">add_box</span>
                Recepcionar Pedido
            </a>
            <a href="{{ route('inventario.crear') }}" class="w-full sm:w-auto btn-primary justify-center">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newProduct')"></span>
            </a>
        </div>
    </div>

    <x-vc-table-layout 
        :data="$productos"
        icon="inventory_2"
        emptyTitle="Sin productos"
        emptyText="No hay productos que coincidan con los filtros."
        :searchable="true"
        searchModel="busqueda"
        x-bind:searchPlaceholder="$store.i18n.t('btn.search') || 'Buscar...'"
    >
        <x-slot:filters>
            <x-vc-dropdown
                wire:model.live="filtroTipo"
                :options="[
                    ['value' => '', 'label' => 'Todos los Tipos'],
                    ['value' => 'Medicamento', 'label' => 'Medicamento'],
                    ['value' => 'Alimento', 'label' => 'Alimento'],
                    ['value' => 'Accesorio', 'label' => 'Accesorio'],
                    ['value' => 'Servicio', 'label' => 'Servicio'],
                ]"
                :selected="$filtroTipo"
                placeholder="Todos los Tipos"
            />
            <div class="flex items-center gap-2 px-2 bg-white dark:bg-vc-surface-alt rounded-lg border border-zinc-200 dark:border-zinc-700 h-10">
                <flux:checkbox wire:model.live="soloStockBajo" />
                <span class="text-sm whitespace-nowrap" style="color: var(--vc-text-muted);" x-text="$store.i18n.t('misc.lowStockOnly')"></span>
            </div>
        </x-slot:filters>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            @foreach($productos as $producto)
                <div class="vc-card flex flex-col justify-between p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow relative">
                    {{-- Status Badge --}}
                    <div class="absolute top-4 right-4">
                        <span class="badge {{ $producto->is_active ? 'badge-emerald' : 'badge-zinc' }}">
                            <span class="material-symbols-outlined text-xs mr-1">{{ $producto->is_active ? 'check_circle' : 'cancel' }}</span>
                            <span x-text="$store.i18n.t({{ $producto->is_active ? '\'status.activo\'' : '\'status.inactivo\'' }})"></span>
                        </span>
                    </div>

                    {{-- Avatar y Nombre --}}
                    <div class="flex items-center gap-4 mb-5">
                        @php
                            $tipoLimpio = trim(strtoupper($producto->type));
                            $avatarClasses = match($tipoLimpio) {
                                'PRODUCTO' => 'bg-sky-50 dark:bg-sky-500/10 text-sky-500',
                                'SERVICIO' => 'bg-violet-50 dark:bg-violet-500/10 text-violet-500',
                                'MEDICAMENTO' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-500',
                                'ALIMENTO' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500',
                                'ACCESORIO' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-500',
                                default => 'bg-zinc-50 dark:bg-zinc-500/10 text-zinc-500',
                            };
                            $badgeClasses = match($tipoLimpio) {
                                'PRODUCTO' => 'bg-sky-50 text-sky-700 ring-sky-700/10 dark:bg-sky-400/10 dark:text-sky-400 dark:ring-sky-400/20',
                                'SERVICIO' => 'bg-violet-50 text-violet-700 ring-violet-700/10 dark:bg-violet-400/10 dark:text-violet-400 dark:ring-violet-400/20',
                                'MEDICAMENTO' => 'bg-amber-50 text-amber-700 ring-amber-700/10 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20',
                                'ALIMENTO' => 'bg-emerald-50 text-emerald-700 ring-emerald-700/10 dark:bg-emerald-400/10 dark:text-emerald-400 dark:ring-emerald-400/20',
                                'ACCESORIO' => 'bg-rose-50 text-rose-700 ring-rose-700/10 dark:bg-rose-400/10 dark:text-rose-400 dark:ring-rose-400/20',
                                default => 'bg-zinc-50 text-zinc-700 ring-zinc-700/10 dark:bg-zinc-400/10 dark:text-zinc-400 dark:ring-zinc-400/20',
                            };
                            $iconName = match($tipoLimpio) {
                                'SERVICIO' => 'medical_services',
                                'MEDICAMENTO' => 'medication',
                                'ALIMENTO' => 'pets',
                                'ACCESORIO' => 'toys',
                                'PRODUCTO' => 'inventory_2',
                                default => 'inventory_2',
                            };
                        @endphp
                        <div class="w-12 h-12 rounded-xl {{ $avatarClasses }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl">{{ $iconName }}</span>
                        </div>
                        <div class="pr-12">
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 truncate" title="{{ $producto->name }}">{{ $producto->name }}</h3>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badgeClasses }}">
                                    <span class="material-symbols-outlined text-[12px]">{{ $iconName }}</span>
                                    {{ $producto->type }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Info Principal --}}
                    <div class="space-y-3 mb-6 flex-1">
                        <div class="{{ $tipoLimpio === 'SERVICIO' ? 'grid grid-cols-1 gap-3' : (in_array($tipoLimpio, ['MEDICAMENTO', 'ALIMENTO']) ? 'grid grid-cols-3 gap-3' : 'grid grid-cols-2 gap-3') }}">
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.price')"></p>
                                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5">S/ {{ number_format($producto->precio_final, 2) }}</p>
                            </div>
                            @if($tipoLimpio !== 'SERVICIO')
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.stock')"></p>
                                <div class="mt-0.5">
                                    @if($producto->current_stock <= $producto->minimum_stock)
                                        <span class="badge badge-red py-0.5 px-2 flex items-center gap-1 w-fit">
                                            <span class="material-symbols-outlined text-[14px]">warning</span>
                                            {{ round($producto->current_stock) }}
                                        </span>
                                    @else
                                        <span class="badge badge-emerald py-0.5 px-2 flex items-center gap-1 w-fit">
                                            <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                                            {{ round($producto->current_stock) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if(in_array($tipoLimpio, ['MEDICAMENTO', 'ALIMENTO']))
                            <div class="ml-auto text-right">
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">Lote / Venc.</p>
                                <div class="mt-0.5">
                                    @php
                                        $loteProximo = $producto->productBatches->where('stock_actual', '>', 0)->sortBy('fecha_vencimiento')->first();
                                    @endphp
                                    @if($loteProximo && $loteProximo->fecha_vencimiento)
                                        <span class="text-xs text-zinc-700 dark:text-zinc-300">{{ $loteProximo->lote }}</span>
                                        <span class="text-[10px] text-zinc-500 block">{{ $loteProximo->fecha_vencimiento->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 italic">S/L</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-1.5 items-center">
                        <button type="button" class="vc-btn-action vc-btn-view" data-vc-tooltip="Ver" x-bind:data-vc-tooltip="$store.i18n.t('btn.view') || 'Ver'" 
                            @click="$wire.ver({{ $producto->id }}).then(() => Flux.modal('ver-producto').show())">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                        <a href="{{ route('inventario.editar', $producto) }}" class="vc-btn-action vc-btn-edit" x-bind:data-vc-tooltip="$store.i18n.t('btn.edit')">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <button type="button" class="vc-btn-action vc-btn-delete" x-bind:data-vc-tooltip="$store.i18n.t('btn.delete')"
                            @click="$wire.confirmDeletion({{ $producto->id }}).then(() => Flux.modal('confirmar-eliminar').show())"
                        >
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $productos->links() }}
        </div>
    </x-vc-table-layout>
</div>

    {{-- Modal de confirmacion --}}
    <flux:modal :closable="false" name="confirmar-eliminar" class="w-[90vw] md:w-full max-w-md">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-amber-100/50 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center border border-amber-200 dark:border-amber-500/30 shadow-sm shadow-amber-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">warning</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.deleteProduct') === 'modal.deleteProduct' ? 'Eliminar Producto' : $store.i18n.t('modal.deleteProduct')">Eliminar Producto</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.deleteProductMsg') === 'modal.deleteProductMsg' ? 'Esta acción no se puede revertir y perderás toda la información asociada a este registro.' : $store.i18n.t('modal.deleteProductMsg')">Esta acción no se puede revertir y perderás toda la información asociada a este registro.</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.cancel') === 'btn.cancel' ? 'Cancelar' : $store.i18n.t('btn.cancel')">Cancelar</span></flux:button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto bg-amber-600 hover:bg-amber-700 text-white rounded-xl shadow-sm hover:shadow transition-all px-4 py-2 font-medium flex items-center justify-center gap-2" wire:click="eliminar" x-on:click="Flux.modal('confirmar-eliminar').close()">
                    <span x-text="$store.i18n.t('btn.delete') === 'btn.delete' ? 'Eliminar' : $store.i18n.t('btn.delete')">Eliminar</span>
                </button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Ver Producto --}}
    <flux:modal name="ver-producto" class="w-[90vw] md:w-full max-w-2xl">
        @if($productoVer)
        <div class="space-y-4">
            <div class="flex justify-between items-start border-b border-zinc-100 dark:border-zinc-700/50 pb-4 pr-6">
                <div>
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-500">inventory_2</span>
                        {{ $productoVer->name }}
                    </h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">category</span>
                        {{ $productoVer->categoria ?? 'Sin categoría' }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <span class="badge {{ $productoVer->is_active ? 'badge-emerald' : 'badge-zinc' }}">
                        <span class="material-symbols-outlined text-xs mr-1">{{ $productoVer->is_active ? 'check_circle' : 'cancel' }}</span>
                        {{ $productoVer->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                    @php
                        $tipoColor = match(strtoupper($productoVer->type)) {
                            'PRODUCTO' => 'sky',
                            'SERVICIO' => 'violet',
                            'MEDICAMENTO' => 'amber',
                            'ALIMENTO' => 'emerald',
                            default => 'zinc',
                        };
                        $iconName = strtoupper(trim($productoVer->type)) === 'SERVICIO' ? 'medical_services' : (strtoupper(trim($productoVer->type)) === 'MEDICAMENTO' ? 'medication' : (strtoupper(trim($productoVer->type)) === 'ALIMENTO' ? 'pets' : 'inventory_2'));
                    @endphp
                    <flux:badge size="sm" :color="$tipoColor">
                        <span class="material-symbols-outlined text-[14px] mr-1">{{ $iconName }}</span> {{ $productoVer->type }}
                    </flux:badge>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Columna 1: Info General --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-2" x-text="$store.i18n.t('form.basicInfo') || 'Información Básica'"></h3>
                    
                    @if($productoVer->type === 'Medicamento')
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider">Principio Activo</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $productoVer->principio_activo ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider">Presentación</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $productoVer->presentacion ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="material-symbols-outlined text-sm {{ $productoVer->requiere_receta ? 'text-red-500' : 'text-emerald-500' }}">
                                {{ $productoVer->requiere_receta ? 'prescription' : 'check_circle' }}
                            </span>
                            <span class="text-sm font-medium {{ $productoVer->requiere_receta ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $productoVer->requiere_receta ? 'Requiere Receta' : 'Venta Libre' }}
                            </span>
                        </div>
                    @elseif($productoVer->type === 'Alimento')
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider">Peso / Presentación</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $productoVer->weight ?? '-' }}</span>
                        </div>
                    @endif
                    
                    @if($productoVer->type !== 'Servicio')
                    <div class="flex flex-col">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider">Código de Barras</span>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-zinc-400">barcode</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-white font-mono">{{ $productoVer->codigo_barras ?? '-' }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Columna 2: Stock y Precio --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-2">Precios y Stock</h3>
                    
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">Precio de Venta</span>
                            <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">S/ {{ number_format($productoVer->precio_final, 2) }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $productoVer->afecto_igv ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' : 'bg-zinc-200 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300' }}">
                                {{ $productoVer->afecto_igv ? '+ IGV (18%)' : 'Inafecto' }}
                            </span>
                        </div>
                    </div>

                    @if(trim(strtoupper($productoVer->type)) !== 'SERVICIO')
                    <div class="flex justify-between items-center pt-2">
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider">Stock Actual</span>
                            <span class="text-lg font-bold {{ $productoVer->current_stock <= $productoVer->minimum_stock ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-white' }}">
                                {{ round($productoVer->current_stock) }}
                            </span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider">Stock Mínimo</span>
                            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ floatval($productoVer->minimum_stock) }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if(in_array(trim(strtoupper($productoVer->type)), ['MEDICAMENTO', 'ALIMENTO']))
                    <div class="pt-4 mt-2 border-t border-zinc-200 dark:border-zinc-700">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider block mb-2">Lotes Activos</span>
                        <div class="space-y-2">
                            @php
                                $lotes = $productoVer->productBatches->where('stock_actual', '>', 0)->sortBy('fecha_vencimiento');
                            @endphp
                            @forelse($lotes as $lote)
                            <div class="flex justify-between items-center bg-zinc-50 dark:bg-vc-surface-alt/50 p-2 rounded-lg">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-zinc-900 dark:text-white">Lote: {{ $lote->lote }}</span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Vence: {{ $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Stock: {{ $lote->stock_actual }}</span>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 italic">No hay lotes activos</p>
                            @endforelse
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($productoVer->notes)
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider block mb-1">Notas / Observaciones</span>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">
                    {{ $productoVer->notes }}
                </p>
            </div>
            @endif

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button variant="ghost"><span x-text="$store.i18n.t('btn.close') === 'btn.close' ? 'Cerrar' : $store.i18n.t('btn.close')">Cerrar</span></flux:button>
                </flux:modal.close>
            </div>
        </div>
        @endif
    </flux:modal>
</div>

