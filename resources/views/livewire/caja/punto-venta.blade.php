<div x-data>
    <x-slot:title>Point of Sale</x-slot:title>

    {{-- ═══ Header de Punto de Venta (Estándar Premium) ═══ --}}
    <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <span class="material-symbols-outlined text-2xl">point_of_sale</span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                    <span x-text="$store.i18n.t('sidebar.point_of_sale') || 'Punto de Venta'">Punto de Venta</span>
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('page.posSub') || 'Registro rápido de ventas, emisión de boletas, facturas y cobros'">
                    Registro rápido de ventas, emisión de boletas, facturas y cobros
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('caja.index') }}" wire:navigate class="btn-secondary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined icon-sm">receipt_long</span>
                <span x-text="$store.i18n.t('page.salesList') || 'Ver Ventas'">Ver Ventas</span>
            </a>
            <a href="{{ route('caja.arqueo') }}" wire:navigate class="btn-secondary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined icon-sm">account_balance_wallet</span>
                <span x-text="$store.i18n.t('cashier.title') || 'Arqueo de Caja'">Arqueo de Caja</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        @if(!$this->activeRegister)
            <div class="col-span-full">
                <flux:modal name="caja-cerrada" class="min-w-md overflow-hidden p-0!" unclosable>
                    <div class="h-24 bg-linear-to-br from-red-500 to-rose-600 flex items-center justify-center relative">
                        <div class="absolute inset-0 opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMSIvPgo8L3N2Zz4=')]"></div>
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center shadow-lg border border-white/30 z-10">
                            <span class="material-symbols-outlined text-4xl text-white">lock</span>
                        </div>
                    </div>
                    
                    <div class="p-6 text-center">
                        <flux:heading size="xl" class="mb-2">
                            <span x-text="$store.i18n.t('modal.cashClosed') || '¡Caja Cerrada!'">¡Caja Cerrada!</span>
                        </flux:heading>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6" x-text="$store.i18n.t('modal.cashClosedMsg') || 'Debe abrir la caja antes de procesar ventas.'">
                            Debe abrir la caja antes de procesar ventas.
                        </p>
                        
                        <div class="flex justify-center mt-6">
                            <a href="{{ route('caja.arqueo') }}" class="w-full sm:w-auto btn-primary btn-primary--emerald justify-center px-8 py-3">
                                <span class="material-symbols-outlined icon-sm">point_of_sale</span>
                                <span x-text="$store.i18n.t('btn.goToCashShift') || 'Ir a Arqueo de Caja'">Ir a Arqueo de Caja</span>
                            </a>
                        </div>
                    </div>
                </flux:modal>
                <div x-init="$nextTick(() => Flux.modal('caja-cerrada').show())"></div>
            </div>
        @endif

        {{-- ═══ Panel izquierdo - Búsqueda de productos (3 cols) ═══ --}}
        <div class="lg:col-span-3 space-y-4">
            {{-- Buscador (Autocomplete) --}}
            <div class="vc-panel">
                {{-- Filtros Rápidos (Píldoras) --}}
                <div class="mb-4 flex flex-wrap gap-2">
                    @php
                        $categorias = [
                            '' => ['icon' => 'grid_view', 'key' => 'misc.allProducts', 'fallback' => 'Todos'],
                            'MEDICAMENTO' => ['icon' => 'vaccines', 'key' => 'misc.medication', 'fallback' => 'Medicamento'],
                            'ALIMENTO' => ['icon' => 'pets', 'key' => 'misc.food', 'fallback' => 'Alimento'],
                            'ACCESORIO' => ['icon' => 'shopping_bag', 'key' => 'misc.accessory', 'fallback' => 'Accesorio'],
                            'SERVICIO' => ['icon' => 'medical_services', 'key' => 'misc.service', 'fallback' => 'Servicio'],
                        ];
                    @endphp
                    @foreach($categorias as $valor => $cat)
                        <button 
                            type="button" 
                            wire:click="$set('filtroTipo', '{{ $valor }}')"
                            class="px-3 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all {{ $filtroTipo === $valor ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
                        >
                            <span class="material-symbols-outlined text-[16px]">{{ $cat['icon'] }}</span>
                            <span x-text="$store.i18n.t('{{ $cat['key'] }}') || '{{ $cat['fallback'] }}'"></span>
                        </button>
                    @endforeach
                </div>

                {{-- Input Búsqueda --}}
                <div class="mb-4">
                    <flux:input
                        wire:model.live.debounce.300ms="buscarProducto"
                        class="w-full"
                        x-bind:placeholder="$store.i18n.t('placeholder.searchPOS')"
                    >
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">search</span>
                        </x-slot:iconLeading>
                    </flux:input>
                </div>
                
                {{-- Switch Modo Emergencia Interactivo --}}
                <label class="mb-4 rounded-xl p-3.5 border transition-all flex items-center justify-between gap-4 cursor-pointer select-none {{ $is_emergency ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-500/10 shadow-sm' : 'bg-zinc-100/70 dark:bg-zinc-800/40 border-zinc-200 dark:border-zinc-700/50 hover:border-zinc-300' }}">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $is_emergency ? 'bg-amber-500 text-white shadow-sm' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400' }}">
                            <span class="material-symbols-outlined text-[20px]">medical_services</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="block text-sm font-bold text-zinc-900 dark:text-zinc-100" x-text="$store.i18n.t('form.emergencyMode') || 'Modo de emergencia (permite venta sin stock)'">Modo de emergencia</span>
                                @if($is_emergency)
                                    <span class="badge badge-amber text-[10px] uppercase font-black tracking-wider animate-pulse">Activo</span>
                                @endif
                            </div>
                            <span class="block text-xs text-zinc-500 dark:text-zinc-400 mt-0.5" x-text="$store.i18n.t('form.emergencyModeHelp') || 'Úsalo solo en emergencias médicas'">Úsalo solo en emergencias médicas</span>
                        </div>
                    </div>
                    <div class="relative inline-flex items-center shrink-0">
                        <input type="checkbox" wire:model.live="is_emergency" class="sr-only peer">
                        <div class="w-11 h-6 bg-zinc-300 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-amber-500"></div>
                    </div>
                </label>

                {{-- Grid de Resultados --}}
                <div class="bg-zinc-50/50 dark:bg-vc-surface-alt/50 border border-zinc-200 dark:border-zinc-700/50 rounded-xl p-4 h-[600px] overflow-y-auto">
                    @if($productos->isEmpty())
                        <div class="h-full flex flex-col items-center justify-center text-center text-sm text-zinc-500">
                            <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-3xl opacity-50">search_off</span>
                            </div>
                            <p x-text="$store.i18n.t('empty.noProductsFound') || 'No se encontraron productos'"></p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($productos as $producto)
                                @php
                                    $typeConfig = match(strtoupper($producto->type)) {
                                        'SERVICIO' => ['icon' => 'medical_services', 'color' => 'text-blue-500', 'bg' => 'bg-blue-500/10 border-blue-500/20'],
                                        'MEDICAMENTO' => ['icon' => 'vaccines', 'color' => 'text-purple-500', 'bg' => 'bg-purple-500/10 border-purple-500/20'],
                                        'ALIMENTO' => ['icon' => 'pets', 'color' => 'text-amber-500', 'bg' => 'bg-amber-500/10 border-amber-500/20'],
                                        'ACCESORIO' => ['icon' => 'shopping_bag', 'color' => 'text-pink-500', 'bg' => 'bg-pink-500/10 border-pink-500/20'],
                                        default => ['icon' => 'inventory_2', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-500/10 border-emerald-500/20']
                                    };
                                    $sinStock = $producto->current_stock <= 0 && strtoupper($producto->type) !== 'SERVICIO';
                                @endphp
                                <button
                                    type="button"
                                    wire:click="agregarAlCarrito({{ $producto->id }})"
                                    class="relative text-left bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-700/80 rounded-xl p-3 hover:shadow-lg hover:border-emerald-500/50 dark:hover:border-emerald-500/50 transition-all flex flex-col h-full group active:scale-95 overflow-hidden {{ $sinStock && !$is_emergency ? 'opacity-60 cursor-not-allowed' : '' }}"
                                >
                                    <span class="material-symbols-outlined absolute -right-3 -bottom-3 text-[100px] opacity-[0.03] dark:opacity-5 pointer-events-none {{ $typeConfig['color'] }}">{{ $typeConfig['icon'] }}</span>
                                    
                                    <div class="flex items-start gap-3 w-full">
                                        <div class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center {{ $typeConfig['bg'] }} border">
                                            <span class="material-symbols-outlined text-xl {{ $typeConfig['color'] }}">{{ $typeConfig['icon'] }}</span>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[13px] font-bold text-zinc-900 dark:text-zinc-100 line-clamp-2 leading-tight mb-0.5 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $producto->name }}</p>
                                            <p class="text-[10px] font-medium text-zinc-500 uppercase tracking-wider mb-2" x-text="$store.i18n.t('misc.{{ strtolower($producto->type) }}') || '{{ $producto->type }}'">{{ $producto->type }}</p>
                                            
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                @if(strtoupper($producto->type) !== 'SERVICIO')
                                                    @if($sinStock)
                                                        @if($is_emergency)
                                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                                                <span x-text="($store.i18n.t('table.stock') || 'Stock') + ': 0 (' + ($store.i18n.t('appointment.statusEmergency') || 'Emergencia') + ')'">Stock: 0 (Emergencia)</span>
                                                            </span>
                                                        @else
                                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400" x-text="$store.i18n.t('misc.outOfStock') || 'Sin Stock'">
                                                                Sin Stock
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $producto->current_stock <= ($producto->minimum_stock ?? 0) ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                                            <span x-text="($store.i18n.t('table.stock') || 'Stock') + ': ' + '{{ round($producto->current_stock) }}'">Stock: {{ round($producto->current_stock) }}</span>
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400" x-text="$store.i18n.t('form.unlimited') || 'Ilimitado'">Ilimitado</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto pt-3 flex items-center justify-between w-full relative z-10">
                                        <span class="block text-lg font-black text-emerald-600 dark:text-emerald-400">
                                            S/ {{ number_format($producto->precio_final, 2) }}
                                        </span>
                                        <div class="w-8 h-8 rounded-full {{ $sinStock && !$is_emergency ? 'bg-zinc-300 dark:bg-zinc-700 text-zinc-500' : 'bg-emerald-500 text-white group-hover:bg-white dark:group-hover:bg-vc-surface group-hover:text-emerald-500 shadow-sm shadow-emerald-500/20' }} flex items-center justify-center transition-all border border-transparent group-hover:border-emerald-500 p-1">
                                            <span class="material-symbols-outlined text-lg font-bold">add</span>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══ Panel derecho - Carrito (2 cols) ═══ --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Carrito --}}
            <div class="vc-panel">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold font-display flex items-center gap-2" style="color: var(--vc-text);">
                        <span class="material-symbols-outlined text-emerald-400">shopping_cart</span>
                        <span x-text="$store.i18n.t('form.cart') || 'Carrito de Compras'">Carrito de Compras</span>
                        @if(count($carrito) > 0)
                            <span class="badge badge-emerald">{{ count($carrito) }}</span>
                        @endif
                    </h2>
                    @if(count($carrito) > 0)
                        <button type="button" x-on:click="Flux.modal('confirmar-vaciar').show()" class="text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full transition-all hover:bg-red-500 hover:text-white hover:border-transparent flex items-center gap-1 text-red-500 border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-500/10">
                            <span class="material-symbols-outlined text-[14px]">delete_sweep</span>
                            <span x-text="$store.i18n.t('btn.clearAll') || 'Vaciar Todo'">Vaciar Todo</span>
                        </button>
                    @endif
                </div>

                @if(empty($carrito))
                    <div class="vc-empty-state py-6">
                        <div class="vc-empty-icon">
                            <span class="material-symbols-outlined">remove_shopping_cart</span>
                        </div>
                        <p class="vc-empty-title" x-text="$store.i18n.t('empty.emptyCart') || 'El carrito está vacío'"></p>
                    </div>
                @else
                    <div class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                        @foreach($carrito as $index => $item)
                            <div class="vc-cart-item">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium truncate" style="color: var(--vc-text);">{{ $item['name'] }}</p>
                                    <p class="text-xs" style="color: var(--vc-emerald-light);">S/ {{ number_format($item['unit_price'], 2) }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button wire:click="disminuirCantidad({{ $index }})" class="w-6 h-6 rounded-md flex items-center justify-center text-xs border transition-colors hover:bg-zinc-100 dark:hover:bg-zinc-800" style="border-color: var(--vc-border); color: var(--vc-text-muted);">
                                        <span class="material-symbols-outlined text-sm">remove</span>
                                    </button>
                                    <span class="text-sm font-semibold w-6 text-center" style="color: var(--vc-text);">{{ $item['quantity'] }}</span>
                                    <button wire:click="aumentarCantidad({{ $index }})" class="w-6 h-6 rounded-md flex items-center justify-center text-xs border transition-colors hover:bg-zinc-100 dark:hover:bg-zinc-800" style="border-color: var(--vc-border); color: var(--vc-text-muted);">
                                        <span class="material-symbols-outlined text-sm">add</span>
                                    </button>
                                    <button type="button" wire:click="eliminarDelCarrito({{ $index }})" class="w-7 h-7 rounded-full bg-red-50 dark:bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center ml-1">
                                        <span class="material-symbols-outlined text-[14px] font-bold">close</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totales --}}
                    <div class="space-y-2 pt-3" style="border-top: 1px solid var(--vc-border);">
                        <div class="flex justify-between text-sm">
                            <span style="color: var(--vc-text-muted);" x-text="$store.i18n.t('form.subtotal') || 'Subtotal'">Subtotal</span>
                            <span style="color: var(--vc-text);">S/ {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span style="color: var(--vc-text-muted);" x-text="$store.i18n.t('form.igv') || 'IGV (18%)'">IGV (18%)</span>
                            <span style="color: var(--vc-text);">S/ {{ number_format($igv, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold pt-2" style="border-top: 1px solid var(--vc-border);">
                            <span style="color: var(--vc-text);" x-text="$store.i18n.t('form.total') || 'Total'">Total</span>
                            <span style="color: var(--vc-emerald-light);">S/ {{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Datos de pago --}}
            @if(count($carrito) > 0)
                <div class="vc-panel animate-fade-in mt-4">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-emerald-400">receipt_long</span>
                        <h3 class="text-sm font-bold uppercase tracking-wider" style="color: var(--vc-text);" x-text="$store.i18n.t('form.paymentData') || 'Datos de Pago'">Datos de Pago</h3>
                    </div>

                    <div class="space-y-4">
                        <flux:field>
                            <flux:label><span x-text="$store.i18n.t('form.clientOptional') || 'Cliente (Opcional)'">Cliente (Opcional)</span></flux:label>
                            @php
                                $clienteOptions = [['value' => '', 'label' => 'filter.allClients']];
                                foreach ($clientes as $c) {
                                    $clienteOptions[] = ['value' => (string)$c->id, 'label' => $c->nombre_completo];
                                }
                            @endphp
                            <x-vc-dropdown
                                wire:model.live="cliente_id"
                                :options="$clienteOptions"
                                :selected="$cliente_id"
                                placeholder="form.select"
                            />
                            
                            {{-- Info del cliente seleccionado --}}
                            @if($cliente_id)
                                @php
                                    $clienteSeleccionado = \App\Models\Customer::find($cliente_id);
                                @endphp
                                @if($clienteSeleccionado)
                                    <div class="mt-2 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-600 dark:text-zinc-400 flex flex-col gap-1.5 animate-fade-in">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[14px] text-zinc-400">badge</span>
                                            <span class="font-bold text-zinc-700 dark:text-zinc-300">Doc:</span> 
                                            {{ $clienteSeleccionado->numero_documento ?? 'No registrado' }} 
                                            <span class="px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700 text-[10px] font-bold">{{ $clienteSeleccionado->tipo_documento ?? 'S/N' }}</span>
                                        </div>
                                        @if($clienteSeleccionado->email)
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[14px] text-zinc-400">mail</span>
                                            <span class="font-bold text-zinc-700 dark:text-zinc-300">Email:</span> {{ $clienteSeleccionado->email }}
                                        </div>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </flux:field>

                        {{-- Comprobante y Método de Pago --}}
                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label><span x-text="$store.i18n.t('form.receiptType') || 'Tipo Comprobante'">Tipo Comprobante</span></flux:label>
                                @php
                                    $comprobanteOptions = [
                                        ['value' => 'BOLETA', 'label' => 'receipt.boleta'],
                                        ['value' => 'FACTURA', 'label' => 'receipt.factura'],
                                        ['value' => 'NOTA_VENTA', 'label' => 'receipt.saleNote'],
                                    ];
                                @endphp
                                <x-vc-dropdown
                                    wire:model="tipo_comprobante"
                                    :options="$comprobanteOptions"
                                    :selected="$tipo_comprobante"
                                    placeholder="form.select"
                                />
                            </flux:field>

                            <flux:field>
                                <flux:label><span x-text="$store.i18n.t('form.paymentMethod') || 'Método de Pago'">Método de Pago</span></flux:label>
                                <x-vc-dropdown
                                    wire:model.live="metodo_pago"
                                    :options="[
                                        ['value' => 'EFECTIVO', 'label' => 'payment.cash'],
                                        ['value' => 'TARJETA', 'label' => 'payment.card'],
                                        ['value' => 'YAPE_PLIN', 'label' => 'payment.yapePlin'],
                                        ['value' => 'TRANSFERENCIA', 'label' => 'payment.transfer'],
                                    ]"
                                    :selected="$metodo_pago"
                                    placeholder="form.select"
                                />
                            </flux:field>
                        </div>

                        @if($metodo_pago === 'EFECTIVO')
                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label><span x-text="$store.i18n.t('form.amountReceived') || 'Monto Recibido'">Monto Recibido</span></flux:label>
                                <flux:input type="number" step="0.01" wire:model.live.debounce.300ms="monto_recibido" placeholder="0.00" />
                                
                                {{-- Quick Action Buttons --}}
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <button type="button" wire:click="$set('monto_recibido', {{ $total }})" class="px-2 py-1 text-xs bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 rounded text-zinc-700 dark:text-zinc-300 font-medium transition-colors" x-text="$store.i18n.t('form.exact') || 'Exacto'">Exacto</button>
                                    <button type="button" wire:click="$set('monto_recibido', 20)" class="px-2 py-1 text-xs bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 rounded text-zinc-700 dark:text-zinc-300 font-medium transition-colors">S/ 20</button>
                                    <button type="button" wire:click="$set('monto_recibido', 50)" class="px-2 py-1 text-xs bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 rounded text-zinc-700 dark:text-zinc-300 font-medium transition-colors">S/ 50</button>
                                    <button type="button" wire:click="$set('monto_recibido', 100)" class="px-2 py-1 text-xs bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 rounded text-zinc-700 dark:text-zinc-300 font-medium transition-colors">S/ 100</button>
                                </div>
                            </flux:field>
                            <flux:field>
                                <flux:label><span x-text="$store.i18n.t('form.change') || 'Vuelto'">Vuelto</span></flux:label>
                                <div class="w-full h-10 px-3 flex items-center bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700 text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                    S/ {{ number_format($vuelto, 2) }}
                                </div>
                            </flux:field>
                        </div>
                        @endif

                        <flux:field>
                            <flux:label><span x-text="$store.i18n.t('form.notesOptional') || 'Notas (Opcional)'">Notas (Opcional)</span></flux:label>
                            <flux:textarea wire:model="notas" rows="2" placeholder="Ej: Observaciones de la venta..." />
                        </flux:field>
                    </div>

                    {{-- Botón procesar venta --}}
                    <div class="mt-6">
                        <button
                            type="button"
                            class="w-full btn-primary justify-center py-3 text-sm"
                            wire:click="procesarVenta"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove class="flex items-center gap-2">
                                <span class="material-symbols-outlined icon-sm">point_of_sale</span>
                                <span x-text="$store.i18n.t('btn.processSale') || 'Procesar Venta'">Procesar Venta</span>
                                <span class="font-bold border-l border-white/20 pl-2 ml-1">S/ {{ number_format($total, 2) }}</span>
                            </span>
                            <span wire:loading class="flex items-center gap-2">
                                <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                                <span x-text="$store.i18n.t('btn.processing') || 'Procesando...'">Procesando...</span>
                            </span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Confirmar Vaciar --}}
    <flux:modal name="confirmar-vaciar" class="min-w-88">
        <div class="p-4">
            <div class="mb-6 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center mb-4 text-red-600 dark:text-red-400">
                    <span class="material-symbols-outlined text-3xl">delete_sweep</span>
                </div>
                <flux:heading size="lg" class="mb-2">
                    <span x-text="$store.i18n.t('modal.clearCartTitle') || '¿Vaciar carrito?'">¿Vaciar carrito?</span>
                </flux:heading>
                <p class="text-sm text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('modal.clearCartMsg') || 'Se eliminarán todos los productos agregados a la venta actual.'">
                    Se eliminarán todos los productos agregados a la venta actual.
                </p>
            </div>
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'">Cancelar</span></flux:button>
                </flux:modal.close>
                <button type="button" wire:click="$set('carrito', []); recalcularTotales(); Flux.modal('confirmar-vaciar').close();" class="btn-danger justify-center px-4 py-2 flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm">delete</span>
                    <span x-text="$store.i18n.t('btn.clearAll') || 'Sí, vaciar'">Sí, vaciar</span>
                </button>
            </div>
        </div>
    </flux:modal>
</div>
