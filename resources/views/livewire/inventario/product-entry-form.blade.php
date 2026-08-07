<div x-data>
    <x-slot:title>Recepcionar Pedido (Entrada de Stock)</x-slot:title>

    {{-- Cabecera --}}
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('inventario.index') }}" variant="ghost" size="sm" icon="arrow-left" />
        <div>
            <flux:heading size="xl" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500">add_box</span>
                Recepcionar Pedido (Entrada de Lote)
            </flux:heading>
        </div>
    </div>

    <form wire:submit="guardar" class="space-y-6">
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <span class="vc-section-title">Detalles del Ingreso</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Producto <span class="text-red-500">*</span></flux:label>
                    @php
                        $prodOptions = $productos->map(function($p) {
                            $label = $p->name;
                            if ($p->type === 'Medicamento' && ($p->presentacion || $p->principio_activo)) {
                                $label .= ' - ' . $p->presentacion . ($p->principio_activo ? ' ('.$p->principio_activo.')' : '');
                            }
                            $label .= ' (Stock: ' . round($p->current_stock) . ')';
                            return ['value' => $p->id, 'label' => $label];
                        })->toArray();
                    @endphp
                    <x-vc-dropdown
                        wire:model.live="producto_id"
                        :options="$prodOptions"
                        :selected="$producto_id"
                        x-bind:placeholder="$store.i18n.t('form.select_product', 'Selecciona un producto...')"
                        icon="inventory_2"
                        searchable
                    />
                    <flux:error name="producto_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Proveedor</flux:label>
                    @php
                        $provOptions = $proveedores->map(function($p) {
                            return ['value' => $p->id, 'label' => $p->name];
                        })->toArray();
                    @endphp
                    <x-vc-dropdown
                        wire:model="supplier_id"
                        :options="$provOptions"
                        :selected="$supplier_id"
                        x-bind:placeholder="$store.i18n.t('form.select_supplier', 'Seleccione proveedor (opcional)')"
                        icon="local_shipping"
                        searchable
                        :allow-custom="true"
                    />
                    <flux:error name="supplier_id" />
                </flux:field>
            </div>

                <div class="grid grid-cols-3 gap-2 md:gap-4">
                    <flux:field>
                        <flux:label>Cantidad Ingresada <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="cantidad" type="number" min="1" x-bind:placeholder="$store.i18n.t('form.qty_example', 'Ej. 50')">
                            <x-slot:iconLeading>
                                <span class="material-symbols-outlined text-[18px]">numbers</span>
                            </x-slot:iconLeading>
                        </flux:input>
                        <flux:error name="cantidad" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Costo de Compra (Unidad) <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="costo_unitario" type="number" step="0.01" min="0" placeholder="0.00">
                            <x-slot:iconLeading>
                                <span class="material-symbols-outlined text-[18px]">payments</span>
                            </x-slot:iconLeading>
                        </flux:input>
                        <flux:error name="costo_unitario" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Precio de Venta General</flux:label>
                        <flux:input wire:model="precio_venta" type="number" step="0.01" min="0" x-bind:placeholder="$store.i18n.t('form.optional', 'Opcional')">
                            <x-slot:iconLeading>
                                <span class="material-symbols-outlined text-[18px]">sell</span>
                            </x-slot:iconLeading>
                        </flux:input>
                        <flux:error name="precio_venta" />
                    </flux:field>
                </div>
        </div>

        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">event_available</span>
                </div>
                <span class="vc-section-title">Datos de Trazabilidad (Lote)</span>
            </div>

            <div class="flex items-start gap-3 p-3 mb-4 bg-amber-50 dark:bg-amber-500/10 rounded-lg border border-amber-200 dark:border-amber-900/30">
                <span class="material-symbols-outlined text-amber-500">info</span>
                <p class="text-sm text-amber-800 dark:text-amber-400">Si el producto es un Medicamento o Alimento, el número de lote y fecha de vencimiento son <strong>obligatorios</strong>.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Número de Lote</flux:label>
                    <flux:input wire:model="lote" placeholder="LOTE-001">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">qr_code</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="lote" />
                </flux:field>

                <flux:field>
                    <flux:label>Fecha de Vencimiento</flux:label>
                    <x-vc-date-picker wire:model.live="fecha_vencimiento" x-bind:placeholder="$store.i18n.t('form.expiration_date', 'Seleccionar fecha...')" />
                    <flux:error name="fecha_vencimiento" />
                </flux:field>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <flux:button href="{{ route('inventario.index') }}" variant="ghost" class="w-full sm:w-auto">
                Cancelar
            </flux:button>
            <button type="submit" class="w-full sm:w-auto btn-primary justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm">save</span>
                    Registrar Entrada
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    Guardando...
                </span>
            </button>
        </div>
    </form>
</div>
