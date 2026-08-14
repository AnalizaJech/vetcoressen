<div x-data>
    <x-slot:title>Cashier</x-slot:title>

    {{-- Cabecera con icono --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="kpi-icon kpi-icon--cyan">
                <span class="material-symbols-outlined">point_of_sale</span>
            </div>
            <div>
                <flux:heading size="xl"><span x-text="$store.i18n.t('page.cashier')"></span></flux:heading>
                <flux:subheading><span x-text="$store.i18n.t('page.cashierSub')"></span></flux:subheading>
            </div>
        </div>
        <div class="w-full sm:w-auto mt-2 sm:mt-0 flex flex-col sm:flex-row items-center gap-3">
            <div class="w-40">
                <x-vc-dropdown 
                    wire:model.live="filtroTiempo"
                    :options="[
                        ['value' => 'hoy', 'label' => 'filter.today'],
                        ['value' => 'semana', 'label' => 'filter.thisWeek'],
                        ['value' => 'mes', 'label' => 'filter.thisMonth'],
                        ['value' => 'anio', 'label' => 'filter.thisYear']
                    ]"
                    :selected="$filtroTiempo"
                    placeholder="filter.filter"
                />
            </div>
            <a href="{{ route('caja.venta') }}" class="w-full sm:w-auto btn-primary justify-center">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newSale')"></span>
            </a>
        </div>
    </div>

    {{-- ═══ KPIs del día ═══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total del día --}}
        <div class="kpi-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wide" style="color: var(--vc-text-muted);" x-text="$store.i18n.t('kpi.total.{{ $filtroTiempo ?? 'all' }}')">
                    Total
                </span>
                <div class="kpi-icon kpi-icon--emerald">
                    <span class="material-symbols-outlined">payments</span>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-extrabold font-display" style="color: var(--vc-text);">S/ {{ number_format($totalVentasHoy, 2) }}</p>
            <p class="text-xs mt-1.5" style="color: var(--vc-text-muted);">{{ $cantidadVentasHoy }} <span x-text="$store.i18n.t('kpi.sales.all')"></span></p>
        </div>

        {{-- Efectivo --}}
        <div class="kpi-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wide" style="color: var(--vc-text-muted);" x-text="$store.i18n.t('kpi.cash')"></span>
                <div class="kpi-icon kpi-icon--blue">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-extrabold font-display" style="color: var(--vc-text);">S/ {{ number_format($totalEfectivo, 2) }}</p>
        </div>

        {{-- Tarjeta --}}
        <div class="kpi-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wide" style="color: var(--vc-text-muted);" x-text="$store.i18n.t('kpi.card')"></span>
                <div class="kpi-icon kpi-icon--purple">
                    <span class="material-symbols-outlined">credit_card</span>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-extrabold font-display" style="color: var(--vc-text);">S/ {{ number_format($totalTarjeta, 2) }}</p>
        </div>

        {{-- Digital --}}
        <div class="kpi-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wide" style="color: var(--vc-text-muted);" x-text="$store.i18n.t('kpi.digital')"></span>
                <div class="kpi-icon kpi-icon--cyan">
                    <span class="material-symbols-outlined">smartphone</span>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-extrabold font-display" style="color: var(--vc-text);">S/ {{ number_format($totalDigital, 2) }}</p>
        </div>
    </div>

    {{-- ═══ Tabla de ventas del día ═══ --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold font-display" style="color: var(--vc-text);" x-text="$store.i18n.t('kpi.sales.{{ $filtroTiempo ?? 'all' }}')">
                Ventas
            </h2>
        </div>

    <x-vc-table-layout 
        :data="$ventasRecientes"
        icon="receipt_long"
        emptyTitle="Sin ventas hoy"
        emptyText="Aún no se han registrado ventas en el turno actual."
        :searchable="false"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mt-4">
            @foreach($ventasRecientes as $venta)
                <div class="vc-card flex flex-col justify-between p-4 rounded-xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-100">{{ $venta->tipo_comprobante }}</h3>
                                <p class="text-[11px] text-zinc-500 flex items-center gap-1 mt-0.5">
                                    <span class="material-symbols-outlined text-[13px]">schedule</span>
                                    {{ $venta->created_at->format('d/m/Y h:i A') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-extrabold text-emerald-600 dark:text-emerald-400">S/ {{ number_format($venta->total, 2) }}</p>
                            @php
                                $estadoClase = match($venta->status) {
                                    'PAGADO' => 'badge-emerald',
                                    'ANULADO' => 'badge-red',
                                    default => 'badge-amber',
                                };
                            @endphp
                            <span class="badge {{ $estadoClase }} text-[10px] px-1.5 py-0 mt-1" x-text="$store.i18n.t('status.{{ $venta->status }}')">{{ $venta->status }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-400 truncate">
                            <span class="material-symbols-outlined text-[14px]">person</span>
                            <span class="truncate">{{ $venta->cliente?->nombre_completo ?? 'Walk-in' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-400 truncate">
                            <span class="material-symbols-outlined text-[14px]">wallet</span>
                            <span class="truncate">{{ str_replace('_', ' ', $venta->payment_method) }}</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-2">
                        <button wire:click="verVenta({{ $venta->id }})" x-on:click="Flux.modal('ver-venta-modal').show()" class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                            <span x-text="$store.i18n.t('btn.view') || 'Ver'">Ver</span>
                        </button>
                        <a href="{{ route('caja.voucher', $venta->id) }}" target="_blank" class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors" title="Ver Voucher">
                            <span class="material-symbols-outlined text-sm">receipt</span>
                            Voucher
                        </a>
                        @if($venta->status === 'PAGADO')
                            <button wire:click="$set('ventaAnularId', {{ $venta->id }})" x-on:click="Flux.modal('anular-venta-modal').show()" class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                                <span class="material-symbols-outlined text-sm">cancel</span>
                                <span x-text="$store.i18n.t('btn.cancelSale') || 'Anular'">Anular</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $ventasRecientes->links() }}
        </div>
    </x-vc-table-layout>
    </div>

    {{-- Modal para anular venta --}}
    <flux:modal name="anular-venta-modal" class="min-w-88">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">cancel</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.confirmCancel') || 'Confirmar Anulación'">Confirmar Anulación</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.confirmCancelMsg') || '¿Está seguro que desea anular esta venta y devolver el stock? Esta acción generará una Nota de Crédito.'">¿Está seguro que desea anular esta venta y devolver el stock? Esta acción generará una Nota de Crédito.</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:modal.close class="w-full sm:w-auto flex-1">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'">Cancelar</span></flux:button>
                </flux:modal.close>
                <button type="button" wire:click="anularVentaConfirmada" class="btn-primary btn-primary--red w-full sm:w-auto flex-1 flex justify-center items-center gap-2" x-on:click="$dispatch('modal-close', { name: 'anular-venta-modal' })">
                    <span class="material-symbols-outlined icon-sm">delete</span>
                    <span x-text="$store.i18n.t('btn.cancelSale') || 'Anular Venta'">Anular Venta</span>
                </button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal para Ver Detalles de Venta --}}
    <flux:modal name="ver-venta-modal" class="w-[90vw] md:w-full max-w-2xl p-0 overflow-y-auto max-h-[85vh]">
        @if($ventaVer)
            <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50 dark:bg-zinc-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-display font-bold text-zinc-900 dark:text-white">
                            <span x-text="$store.i18n.t('table.sale') || 'Venta'">Venta</span> #{{ str_pad($ventaVer->id, 6, '0', STR_PAD_LEFT) }}
                        </h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">event</span>
                            {{ $ventaVer->created_at->format('d/m/Y h:i A') }}
                        </p>
                    </div>
                </div>
                <div>
                    <span class="badge {{ $ventaVer->status === 'PAGADO' ? 'badge-emerald' : ($ventaVer->status === 'ANULADO' ? 'badge-red' : 'badge-amber') }}">
                        <span x-text="$store.i18n.t('status.{{ strtolower($ventaVer->status) }}') || '{{ $ventaVer->status }}'"></span>
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-bold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase mb-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">person</span> 
                                <span x-text="$store.i18n.t('table.client') || 'Cliente'">Cliente</span>
                            </p>
                            <p class="font-medium text-zinc-800 dark:text-zinc-200">
                                {{ $ventaVer->cliente?->nombre_completo ?? 'Walk-in / Cliente General' }}
                            </p>
                            @if($ventaVer->cliente)
                                <p class="text-xs text-zinc-500">{{ $ventaVer->cliente->numero_documento }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-bold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase mb-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">payments</span> 
                                <span x-text="$store.i18n.t('table.paymentMethod') || 'Método de Pago'">Método de Pago</span>
                            </p>
                            <p class="font-medium text-zinc-800 dark:text-zinc-200">
                                <span x-text="$store.i18n.t('payment.{{ strtolower(str_replace(' ', '_', $ventaVer->payment_method)) }}') || '{{ str_replace('_', ' ', $ventaVer->payment_method) }}'"></span>
                            </p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-bold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase mb-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">support_agent</span> 
                                <span x-text="$store.i18n.t('table.cashier') || 'Cajero'">Cajero</span>
                            </p>
                            <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ $ventaVer->cajero->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold tracking-wider text-zinc-400 dark:text-zinc-500 uppercase mb-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">receipt</span> 
                                <span x-text="$store.i18n.t('table.voucher') || 'Comprobante'">Comprobante</span>
                            </p>
                            <p class="font-medium text-zinc-800 dark:text-zinc-200">
                                <span x-text="$store.i18n.t('receipt.{{ strtolower($ventaVer->tipo_comprobante) }}') || '{{ $ventaVer->tipo_comprobante }}'"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-zinc-400">shopping_cart</span> 
                    <span x-text="$store.i18n.t('table.productDetails') || 'Detalle de Productos'">Detalle de Productos</span>
                </h3>
                <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300">
                            <tr>
                                <th class="py-2 px-3 font-medium" x-text="$store.i18n.t('table.productService') || 'Producto/Servicio'">Producto/Servicio</th>
                                <th class="py-2 px-3 font-medium text-center" x-text="$store.i18n.t('table.qty') || 'Cant'">Cant</th>
                                <th class="py-2 px-3 font-medium text-right" x-text="$store.i18n.t('table.unitPrice') || 'P. Unit'">P. Unit</th>
                                <th class="py-2 px-3 font-medium text-right" x-text="$store.i18n.t('table.total') || 'Total'">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($ventaVer->detalles as $detalle)
                                <tr class="bg-white dark:bg-vc-surface">
                                    <td class="py-2 px-3 text-zinc-800 dark:text-zinc-200">{{ $detalle->producto?->name ?? 'Desconocido' }}</td>
                                    <td class="py-2 px-3 text-center text-zinc-600 dark:text-zinc-400">{{ $detalle->quantity }}</td>
                                    <td class="py-2 px-3 text-right text-zinc-600 dark:text-zinc-400">S/ {{ number_format($detalle->precio_final_unitario, 2) }}</td>
                                    <td class="py-2 px-3 text-right font-medium text-zinc-800 dark:text-zinc-200">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-zinc-50 dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-700 font-bold">
                            <tr>
                                <td colspan="3" class="py-3 px-3 text-right text-zinc-600 dark:text-zinc-400"><span x-text="$store.i18n.t('table.grandTotal') || 'Total General:'">Total General:</span></td>
                                <td class="py-3 px-3 text-right text-emerald-600 dark:text-emerald-400 text-base">S/ {{ number_format($ventaVer->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <flux:modal.close class="overflow-y-auto max-h-[85vh]">
                        <flux:button variant="ghost" class="flex items-center gap-1">
                            <span x-text="$store.i18n.t('btn.close') || 'Cerrar'">Cerrar</span>
                        </flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @else
            <div class="p-6 text-center text-zinc-500"><span x-text="$store.i18n.t('form.loadingDetails') || 'Cargando detalles...'">Cargando detalles...</span></div>
        @endif
    </flux:modal>
</div>
