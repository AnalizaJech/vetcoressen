<div x-data>
    <x-slot:title>Inventory</x-slot:title>

<div class="animate-slide-up">
    {{-- ═══ Header de Inventario (Estándar Premium) ═══ --}}
    <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <span class="material-symbols-outlined text-2xl">inventory_2</span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                    <span x-text="$store.i18n.t('page.inventory') || 'Inventario'">Inventario</span>
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('page.inventorySub') || 'Gestión de productos, medicamentos y servicios clínicos'">
                    Gestión de productos, medicamentos y servicios clínicos
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('inventario.entrada') }}" wire:navigate class="btn-secondary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined icon-sm">add_box</span>
                <span x-text="$store.i18n.t('btn.receiveOrder') || 'Recepcionar Pedido'">Recepcionar Pedido</span>
            </a>
            <a href="{{ route('inventario.crear') }}" wire:navigate class="btn-primary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newProduct') || 'Nuevo Producto'">Nuevo Producto</span>
            </a>
        </div>
    </div>

    {{-- ═══ Barra de Filtros Dinámicos (Estilo Reportes con Labels) ═══ --}}
    <div class="vc-panel mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
            {{-- Filtro de Producto --}}
            <div>
                <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('filter.product') || 'Producto'">
                    Producto
                </label>
                <x-vc-dropdown
                    wire:model.live="filtroProducto"
                    :options="$productosOptions"
                    :selected="$filtroProducto"
                    placeholder="filter.allProducts"
                    icon="inventory_2"
                    searchable
                />
            </div>

            {{-- Filtro de Tipo / Categoría --}}
            <div>
                <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('filter.category') || 'Categoría / Tipo'">
                    Categoría / Tipo
                </label>
                <x-vc-dropdown
                    wire:model.live="filtroTipo"
                    :options="[
                        ['value' => '', 'label' => 'filter.allTypes'],
                        ['value' => 'Medicamento', 'label' => 'inventory.medication'],
                        ['value' => 'Alimento', 'label' => 'inventory.food'],
                        ['value' => 'Accesorio', 'label' => 'inventory.accessory'],
                        ['value' => 'Servicio', 'label' => 'inventory.service'],
                    ]"
                    :selected="$filtroTipo"
                    placeholder="filter.allTypes"
                    icon="category"
                />
            </div>

            {{-- Solo Stock Bajo --}}
            <div>
                <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('filter.stockStatus') || 'Stock Status'">
                    Stock Status
                </label>
                <label class="flex items-center gap-3 px-3 bg-white dark:bg-vc-surface-alt rounded-xl border border-zinc-200 dark:border-zinc-700 h-10 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors select-none"
                       x-data="{ active: @entangle('soloStockBajo') }">
                    {{-- Toggle switch premium --}}
                    <button type="button" role="switch" 
                            :aria-checked="active ? 'true' : 'false'"
                            @click="active = !active"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
                            :class="active ? 'bg-emerald-500' : 'bg-zinc-300 dark:bg-zinc-600'">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out"
                              :class="active ? 'translate-x-4' : 'translate-x-0'"></span>
                    </button>
                    <span class="text-xs font-medium whitespace-nowrap pointer-events-none text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('misc.lowStockOnly') || 'Low Stock Only'">Low Stock Only</span>
                </label>
            </div>
        </div>
    </div>

    <x-vc-table-layout 
        :data="$productos"
        :searchable="false"
        icon="inventory_2"
        emptyTitle="Sin productos"
        emptyTitleKey="table.empty"
        emptyText="No hay productos registrados."
        emptyTextKey="table.emptyText"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            @foreach($productos as $producto)
                <div class="vc-card flex flex-col justify-between p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow relative">
                    {{-- Avatar y Nombre --}}
                    <div class="flex items-center gap-4 mb-5 min-w-0">
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
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 truncate" title="{{ $producto->name }}">{{ $producto->name }}</h3>
                            <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badgeClasses }}">
                                    <span class="material-symbols-outlined text-[12px]">{{ $iconName }}</span>
                                    <span x-text="$store.i18n.t('inventory.{{ strtolower($producto->type) }}') || '{{ $producto->type }}'"></span>
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset {{ $producto->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-700/10 dark:bg-emerald-400/10 dark:text-emerald-400 dark:ring-emerald-400/20' : 'bg-zinc-50 text-zinc-700 ring-zinc-700/10 dark:bg-zinc-400/10 dark:text-zinc-400 dark:ring-zinc-400/20' }}">
                                    <span class="material-symbols-outlined text-[10px]">{{ $producto->is_active ? 'check_circle' : 'cancel' }}</span>
                                    <span x-text="$store.i18n.t('{{ $producto->is_active ? 'status.activo' : 'status.inactivo' }}')"></span>
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
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.lotExp') || 'Lote / Venc.'">Lote / Venc.</p>
                                <div class="mt-0.5">
                                    @php
                                        $loteProximo = $producto->productBatches->where('stock_actual', '>', 0)->sortBy('fecha_vencimiento')->first();
                                        $diasParaVencer = $loteProximo?->fecha_vencimiento
                                            ? now()->startOfDay()->diffInDays($loteProximo->fecha_vencimiento->copy()->startOfDay(), false)
                                            : null;
                                        $loteEnAlerta = $diasParaVencer !== null && $diasParaVencer <= 90;
                                    @endphp
                                    @if($loteProximo && $loteProximo->fecha_vencimiento)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $loteEnAlerta ? ($diasParaVencer < 0 ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400') : 'text-zinc-700 dark:text-zinc-300' }}">
                                            <span class="material-symbols-outlined text-[13px]">{{ $loteEnAlerta ? ($diasParaVencer < 0 ? 'event_busy' : 'event_upcoming') : 'inventory_2' }}</span>
                                            {{ $loteProximo->lote }}
                                        </span>
                                        <span class="text-[10px] {{ $loteEnAlerta ? ($diasParaVencer < 0 ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-amber-600 dark:text-amber-400 font-semibold') : 'text-zinc-500' }} block">{{ $loteProximo->fecha_vencimiento->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 italic" x-text="$store.i18n.t('misc.notAvailable') || 'S/L'">S/L</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-1.5 items-center">
                        <button type="button" class="vc-btn-action vc-btn-view"
                            @click="$wire.ver({{ $producto->id }}).then(() => Flux.modal('ver-producto').show())">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                        <a href="{{ route('inventario.editar', $producto) }}" class="vc-btn-action vc-btn-edit">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <button type="button" class="vc-btn-action vc-btn-delete"
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
    <flux:modal :closable="false" name="confirmar-eliminar" class="min-w-88 overflow-y-auto max-h-[85vh]">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">warning</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.deleteProduct') || 'Eliminar Producto'">Eliminar Producto</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.deleteProductMsg') || 'Esta acción no se puede revertir y perderás toda la información asociada a este registro.'">Esta acción no se puede revertir y perderás toda la información asociada a este registro.</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <button type="button" class="btn-secondary w-full sm:w-auto text-xs px-4 py-2 flex items-center justify-center gap-1.5">
                        <span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'">Cancelar</span>
                    </button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto btn-danger text-xs px-4 py-2 flex items-center justify-center gap-1.5" wire:click="eliminar" x-on:click="$dispatch('modal-close', { name: 'confirmar-eliminar' })">
                    <span x-text="$store.i18n.t('btn.delete') || 'Eliminar'">Eliminar</span>
                </button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Ver Producto --}}
    <flux:modal :closable="false" name="ver-producto" class="w-[90vw] md:w-full max-w-2xl overflow-y-auto max-h-[85vh]">
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
                        {{ $productoVer->categoria ?? '' }} <span x-show="!('{{ $productoVer->categoria }}')" x-text="$store.i18n.t('form.noCategory') || 'Sin categoría'">Sin categoría</span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <span class="badge {{ $productoVer->is_active ? 'badge-emerald' : 'badge-zinc' }}">
                        <span class="material-symbols-outlined text-xs mr-1">{{ $productoVer->is_active ? 'check_circle' : 'cancel' }}</span>
                        <span x-text="$store.i18n.t('{{ $productoVer->is_active ? 'status.activo' : 'status.inactivo' }}')"></span>
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
                        <span class="material-symbols-outlined text-[14px] mr-1">{{ $iconName }}</span> <span x-text="$store.i18n.t('inventory.{{ strtolower($productoVer->type) }}') || '{{ $productoVer->type }}'"></span>
                    </flux:badge>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Columna 1: Info General --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">info</span>
                        <span x-text="$store.i18n.t('form.basicInfo') || 'Información Básica'">Información Básica</span>
                    </h3>
                    
                    @if($productoVer->type === 'Medicamento')
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider" x-text="$store.i18n.t('form.activeIngredient') || 'Principio Activo'">Principio Activo</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $productoVer->principio_activo ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider" x-text="$store.i18n.t('form.presentation') || 'Presentación'">Presentación</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $productoVer->presentacion ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="material-symbols-outlined text-sm {{ $productoVer->requiere_receta ? 'text-red-500' : 'text-emerald-500' }}">
                                {{ $productoVer->requiere_receta ? 'prescription' : 'check_circle' }}
                            </span>
                            <span class="text-sm font-medium {{ $productoVer->requiere_receta ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                <span x-text="$store.i18n.t('{{ $productoVer->requiere_receta ? 'form.requiresPrescription' : 'form.overTheCounter' }}')"></span>
                            </span>
                        </div>
                    @elseif($productoVer->type === 'Alimento')
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider" x-text="$store.i18n.t('form.weightPresentation') || 'Peso / Presentación'">Peso / Presentación</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $productoVer->weight ?? '-' }}</span>
                        </div>
                    @endif
                    
                    @if($productoVer->type !== 'Servicio')
                    <div class="flex flex-col">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider" x-text="$store.i18n.t('form.barcode') || 'Código de Barras'">Código de Barras</span>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-zinc-400">barcode</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-white font-mono">{{ $productoVer->codigo_barras ?? '-' }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Columna 2: Stock y Precio --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider mb-2 flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">payments</span> <span x-text="$store.i18n.t('form.pricesAndStock') || 'Precios y Stock'">Precios y Stock</span></h3>
                    
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('form.salePrice') || 'Precio de Venta'">Precio de Venta</span>
                            <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">S/ {{ number_format($productoVer->precio_final, 2) }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $productoVer->afecto_igv ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' : 'bg-zinc-200 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300' }}">
                                <span x-text="$store.i18n.t('{{ $productoVer->afecto_igv ? 'form.withIGV' : 'form.withoutIGV' }}')"></span>
                            </span>
                        </div>
                    </div>

                    @if(trim(strtoupper($productoVer->type)) !== 'SERVICIO')
                    <div class="flex justify-between items-center pt-2">
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider" x-text="$store.i18n.t('table.stock') || 'Stock Actual'">Stock Actual</span>
                            <span class="text-lg font-bold {{ $productoVer->current_stock <= $productoVer->minimum_stock ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-white' }}">
                                {{ round($productoVer->current_stock) }}
                            </span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider" x-text="$store.i18n.t('form.minStock') || 'Stock Mínimo'">Stock Mínimo</span>
                            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ floatval($productoVer->minimum_stock) }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if(in_array(trim(strtoupper($productoVer->type)), ['MEDICAMENTO', 'ALIMENTO']))
                    <div class="pt-4 mt-2 border-t border-zinc-200 dark:border-zinc-700">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider mb-2 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">inventory</span> <span x-text="$store.i18n.t('form.activeBatches') || 'Lotes Activos'">Lotes Activos</span></span>
                        <div class="space-y-2">
                            @php
                                $lotes = $productoVer->productBatches->where('stock_actual', '>', 0)->sortBy('fecha_vencimiento');
                            @endphp
                            @forelse($lotes as $lote)
                            <div class="flex justify-between items-center bg-zinc-50 dark:bg-vc-surface-alt/50 p-2 rounded-lg">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-zinc-900 dark:text-white"><span x-text="$store.i18n.t('form.batch') || 'Lote'">Lote</span>: {{ $lote->lote }}</span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400"><span x-text="$store.i18n.t('form.expires') || 'Vence'">Vence</span>: {{ $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300"><span x-text="$store.i18n.t('table.stock') || 'Stock'">Stock</span>: {{ $lote->stock_actual }}</span>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 italic" x-text="$store.i18n.t('form.noActiveBatches') || 'No hay lotes activos'">No hay lotes activos</p>
                            @endforelse
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($productoVer->notes)
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <span class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold tracking-wider mb-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">notes</span> <span x-text="$store.i18n.t('form.notes') || 'Notas / Observaciones'">Notas / Observaciones</span></span>
                <p class="text-sm text-zinc-700 dark:text-zinc-300">
                    {{ $productoVer->notes }}
                </p>
            </div>
            @endif

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:modal.close class="overflow-y-auto max-h-[85vh]">
                    <flux:button variant="ghost">
                        <span x-text="$store.i18n.t('btn.close') === 'btn.close' ? 'Cerrar' : $store.i18n.t('btn.close')">Cerrar</span>
                    </flux:button>
                </flux:modal.close>
            </div>
        </div>
        @endif
    </flux:modal>
</div>

