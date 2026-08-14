<div x-data>
    <x-slot:title>{{ $productoId ? 'Edit Product' : 'New Product' }}</x-slot:title>

    {{-- Cabecera --}}
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('inventario.index') }}" variant="ghost" size="sm" icon="arrow-left" />
        <div>
            <flux:heading size="xl" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500">inventory_2</span>
                <span x-text="$store.i18n.t('{{ $productoId ? 'page.editProduct' : 'page.newProduct' }}')"></span>
            </flux:heading>
        </div>
    </div>

    <form x-on:submit.prevent="{{ $productoId ? 'Flux.modal(\'confirmar-actualizacion\').show()' : '$wire.guardar()' }}" class="space-y-6">
        {{-- ═══ Información básica ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.basicInfo')"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-full">
                    <flux:label class="mb-2"><span x-text="$store.i18n.t('form.typeLabel', 'Tipo de producto')"></span></flux:label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach(['Medicamento', 'Alimento', 'Accesorio', 'Servicio'] as $t)
                        @php
                            $activeClass = match($t) {
                                'Servicio' => 'border-violet-500 bg-violet-50 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400',
                                'Medicamento' => 'border-amber-500 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400',
                                'Alimento' => 'border-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                                'Accesorio' => 'border-rose-500 bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400',
                                default => 'border-zinc-500 bg-zinc-50 dark:bg-zinc-500/10 text-zinc-700 dark:text-zinc-400'
                            };
                            $typeIcon = match($t) {
                                'Servicio' => 'medical_services',
                                'Medicamento' => 'medication',
                                'Alimento' => 'pets',
                                'Accesorio' => 'toys',
                                default => 'inventory_2'
                            };
                        @endphp
                        <button type="button"
                                wire:click="$set('tipo', '{{ $t }}')"
                                class="flex items-center justify-center gap-2 px-4 py-3 border rounded-xl text-sm font-medium transition-all duration-200"
                                :class="$wire.tipo === '{{ $t }}' ? '{{ $activeClass }} shadow-sm' : 'border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:border-zinc-300 dark:hover:border-zinc-600'">
                            <span class="material-symbols-outlined text-lg">
                                {{ $typeIcon }}
                            </span>
                            <span x-text="$store.i18n.t('inventory.{{ strtolower($t) }}') || '{{ $t }}'"></span>
                        </button>
                        @endforeach
                    </div>
                    <flux:error name="tipo" />
                </div>


                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.nameLabel')"></span></flux:label>
                    @php
                        $nameOptions = $nombresComunes->map(fn($n) => ['value' => $n, 'label' => $n])->toArray();
                    @endphp
                    <x-vc-dropdown
                        wire:key="dropdown-nombre-{{ $tipo }}"
                        wire:model.live="nombre"
                        :options="$nameOptions"
                        selected="{{ $nombre }}"
                        x-bind:placeholder="'Product name...'"
                        :allow-custom="true"
                    />
                    <flux:error name="nombre" />
                </flux:field>

                <flux:field x-show="$wire.tipo !== 'Servicio'" x-transition x-cloak>
                    <flux:label><span x-text="$store.i18n.t('form.category', 'Familia / Subcategoría')"></span></flux:label>
                    @php
                        $catOptions = $categorias->map(fn($c) => ['value' => $c, 'label' => $c])->toArray();
                    @endphp
                    <x-vc-dropdown
                        wire:key="dropdown-categoria-{{ $tipo }}"
                        wire:model.live="categoria"
                        :options="$catOptions"
                        selected="{{ $categoria }}"
                        x-bind:placeholder="$wire.tipo === 'Medicamento' ? 'Ej: Antibióticos...' : ($wire.tipo === 'Alimento' ? 'Ej: Seco, Húmedo...' : ($wire.tipo === 'Accesorio' ? 'Ej: Juguetes...' : 'Categoría'))"
                        :allow-custom="true"
                    />
                </flux:field>

                <flux:field class="col-span-full md:col-span-1" x-show="$wire.tipo !== 'Servicio'" x-transition x-cloak>
                    <flux:label class="flex justify-between w-full">
                        <span x-text="$store.i18n.t('form.barcode')"></span>
                        <button type="button" wire:click="generarCodigoBarras" class="text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">autorenew</span> Generar automático</button>
                    </flux:label>
                    <flux:input wire:model="codigo_barras" placeholder="EAN/UPC (Opcional)">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">barcode</span>
                        </x-slot:iconLeading>
                    </flux:input>
                </flux:field>

                <!-- Toggle Requiere Receta -->
                <div class="col-span-full" x-show="$wire.tipo === 'Medicamento'" x-transition x-cloak>
                    <div class="p-4 rounded-xl border border-blue-100 bg-blue-50/50 dark:border-blue-900/30 dark:bg-blue-900/10 flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-blue-900 dark:text-blue-300">Medicamento Controlado (Requiere Receta)</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-400/70">Activa esta opción si el producto debe recetarse en atención clínica antes de venderse.</p>
                        </div>
                        <flux:switch wire:model="requiere_receta" />
                    </div>
                </div>

                <!-- Medicamento: Principio Activo y Presentación -->
                <div class="col-span-1" x-show="$wire.tipo === 'Medicamento'" x-transition x-cloak>
                    <flux:field>
                        <flux:label><span x-text="$store.i18n.t('form.activeIngredient', 'Principio Activo')"></span></flux:label>
                        <flux:input wire:model="principio_activo" x-bind:placeholder="$store.i18n.t('placeholder.activeIngredient', 'Ej. Fluralaner, Meloxicam')">
                            <x-slot:iconLeading>
                                <span class="material-symbols-outlined text-[18px]">science</span>
                            </x-slot:iconLeading>
                        </flux:input>
                    </flux:field>
                </div>
                <div class="col-span-1" x-show="$wire.tipo === 'Medicamento'" x-transition x-cloak>
                    <flux:field>
                        <flux:label>Presentación</flux:label>
                        @php
                            $presentacionOptions = collect(['Caja', 'Frasco', 'Ampolla'])->map(fn($p) => ['value' => $p, 'label' => $p])->toArray();
                        @endphp
                        <x-vc-dropdown
                            wire:model="presentacion"
                            :options="$presentacionOptions"
                            selected="{{ $presentacion }}"
                            placeholder="Seleccione..."
                            :allow-custom="true"
                        />
                    </flux:field>
                </div>

                <!-- Alimento: Peso -->
                <div class="col-span-full md:col-span-1" x-show="$wire.tipo === 'Alimento'" x-transition x-cloak>
                    <flux:field>
                        <flux:label><span x-text="$store.i18n.t('form.weight', 'Peso (kg/gr)')"></span></flux:label>
                        <flux:input wire:model="peso" placeholder="Ej. 15 kg">
                            <x-slot:iconLeading>
                                <span class="material-symbols-outlined text-[18px]">scale</span>
                            </x-slot:iconLeading>
                        </flux:input>
                    </flux:field>
                </div>
            </div>
        </div>

        {{-- ═══ Precios ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="vc-section-icon">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <span class="vc-section-title" x-text="$store.i18n.t('form.pricing', 'Precio')">Precio</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.salePrice', 'Precio de Venta Final')"></span></flux:label>
                    <flux:input wire:model="precio_final" type="number" step="0.01" placeholder="0.00">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">sell</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <p class="text-xs text-zinc-500 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">info</span>
                        Ingresa el precio que pagará el cliente. El sistema calculará el IGV automáticamente.
                    </p>
                    <flux:error name="precio_final" />
                </flux:field>

                <div x-data="{ openAvanzado: false }">
                    <button type="button" @click="openAvanzado = !openAvanzado" class="text-sm font-medium text-vc-primary flex items-center gap-1 mt-7">
                        <span class="material-symbols-outlined text-[18px]" x-text="openAvanzado ? 'expand_less' : 'expand_more'"></span>
                        Opciones Avanzadas (IGV)
                    </button>
                    
                    <div x-show="openAvanzado" x-collapse class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <flux:field>
                            <flux:label><span x-text="$store.i18n.t('form.taxType', 'Afectación IGV')"></span></flux:label>
                            @php
                                $igvOptions = [
                                    ['value' => 'Gravado', 'label' => 'Gravado (18%)'],
                                    ['value' => 'Inafecto', 'label' => 'Inafecto'],
                                    ['value' => 'Exonerado', 'label' => 'Exonerado'],
                                ];
                            @endphp
                            <x-vc-dropdown
                                wire:key="igv-dropdown-{{ $productoId ?? 'new' }}"
                                wire:model="tipo_afectacion_igv"
                                :options="$igvOptions"
                                :selected="$tipo_afectacion_igv"
                            />
                            <p class="text-xs text-zinc-500 mt-1">Por defecto es Gravado. Solo cámbialo si el producto no paga IGV.</p>
                            <flux:error name="tipo_afectacion_igv" />
                        </flux:field>
                        <flux:field class="mt-4">
                            <flux:label class="flex justify-between w-full">
                                <span x-text="$store.i18n.t('form.brand', 'Marca')"></span>
                                <span class="text-xs text-zinc-500" x-text="$store.i18n.t('form.optional', '(Opcional)')"></span>
                            </flux:label>
                            <flux:input wire:model="brand" x-bind:placeholder="$store.i18n.t('placeholder.brand', 'Ej. Bravecto, Royal Canin')" />
                        </flux:field>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <flux:button href="{{ route('inventario.index') }}" variant="ghost" class="w-full sm:w-auto">
                <span x-text="$store.i18n.t('btn.cancel')"></span>
            </flux:button>
            <button type="{{ $productoId ? 'button' : 'submit' }}" 
                    class="w-full sm:w-auto {{ $productoId ? 'btn-violet' : 'btn-primary' }} justify-center" 
                    @if($productoId) x-on:click.prevent="$dispatch('modal-show', { name: 'confirmar-actualizacion' })" @endif
                    wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm" x-text="'{{ $productoId ? 'edit' : 'save' }}'"></span>
                    <span x-text="$store.i18n.t('{{ $productoId ? 'btn.update' : 'btn.register' }}')"></span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    <span x-text="$store.i18n.t('btn.saving')"></span>
                </span>
            </button>
        </div>
    </form>

    {{-- Modal de confirmacion de actualizacion --}}
    @if($productoId)
    <flux:modal :closable="false" name="confirmar-actualizacion" class="min-w-88 overflow-y-auto max-h-[85vh]">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-amber-100/50 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center border border-amber-200 dark:border-amber-500/30 shadow-sm shadow-amber-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">warning</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.confirmUpdate')">Confirmar Actualización</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.confirmUpdateMsg')">¿Estás seguro de que deseas guardar los cambios realizados en este producto?</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.cancel')"></span></flux:button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto btn-violet font-medium justify-center" wire:click="guardar">
                    <span x-text="$store.i18n.t('btn.update')"></span>
                </button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
