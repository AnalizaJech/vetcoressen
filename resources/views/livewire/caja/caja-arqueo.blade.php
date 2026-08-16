<div>
    <x-slot:title>Arqueo de Caja</x-slot:title>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="kpi-icon kpi-icon--emerald">
                <span class="material-symbols-outlined">point_of_sale</span>
            </div>
            <div>
                <flux:heading size="xl"><span x-text="$store.i18n.t('sidebar.cashier') || 'Arqueo de Caja'"></span></flux:heading>
                <flux:subheading>Gestión de apertura y cierre de caja.</flux:subheading>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Estado Actual --}}
        <div class="vc-panel animate-fade-in">
            <h2 class="text-lg font-bold font-display mb-4" style="color: var(--vc-text);">Estado Actual</h2>

            @if($activeRegister)
                <div class="space-y-4">
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 rounded-lg flex items-center gap-3">
                        <span class="material-symbols-outlined">lock_open</span>
                        <div class="font-medium">Caja Abierta</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-zinc-500">Monto de Apertura</div>
                            <div class="text-lg font-semibold">S/ {{ number_format($activeRegister->opening_amount, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-zinc-500">Apertura</div>
                            <div class="text-sm">{{ $activeRegister->opened_at->format('d/m/Y h:i A') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-zinc-500">Ventas Registradas</div>
                            <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">S/ {{ number_format($calculated, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-zinc-500">Monto Esperado</div>
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">S/ {{ number_format($expected, 2) }}</div>
                        </div>
                    </div>

                    <flux:separator class="my-4" />

                    <div class="flex justify-end">
                        <flux:modal.trigger name="cerrar-caja" class="overflow-y-auto max-h-[85vh]">
                            <button type="button" class="btn-primary btn-primary--red justify-center px-6 py-2">
                                <span class="material-symbols-outlined icon-sm">lock</span>
                                Cerrar Caja
                            </button>
                        </flux:modal.trigger>
                    </div>

                    <flux:modal name="cerrar-caja" class="min-w-md overflow-y-auto max-h-[85vh] p-0!">
                        <form wire:submit.prevent="cerrarCaja">
                            <!-- Header decorativo -->
                            <div class="h-24 bg-linear-to-br from-red-500 to-rose-600 flex items-center justify-center relative">
                                <div class="absolute inset-0 opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMSIvPgo8L3N2Zz4=')]"></div>
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center shadow-lg border border-white/30 z-10">
                                    <span class="material-symbols-outlined text-4xl text-white">lock</span>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-center mb-1" style="color: var(--vc-text);">Cerrar Caja</h3>
                                <p class="text-sm text-center mb-6" style="color: var(--vc-text-muted);">Ingrese el monto real contado en caja para proceder al cierre.</p>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1" style="color: var(--vc-text);">Monto Real en Caja</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-zinc-500 sm:text-sm">S/</span>
                                            </div>
                                            <input type="number" step="0.01" wire:model="real_amount" class="w-full pl-8 pr-3 py-2 rounded-lg border focus:ring-2 outline-none transition-all" style="background: var(--vc-glass-bg); border-color: var(--vc-border); color: var(--vc-text);" placeholder="0.00" />
                                        </div>
                                        <flux:error name="real_amount" />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium mb-1" style="color: var(--vc-text);">Notas u Observaciones</label>
                                        <textarea wire:model="notes" rows="2" class="w-full px-3 py-2 rounded-lg border focus:ring-2 outline-none transition-all" style="background: var(--vc-glass-bg); border-color: var(--vc-border); color: var(--vc-text);" placeholder="Desfase por..."></textarea>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                                    <flux:modal.close class="w-full">
                                        <button type="button" class="w-full py-2.5 rounded-xl font-semibold border transition-all" style="border-color: var(--vc-border); color: var(--vc-text);"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></button>
                                    </flux:modal.close>
                                    <button type="submit" class="w-full btn-primary btn-primary--red justify-center py-2.5">Cerrar Definitivamente</button>
                                </div>
                            </div>
                        </form>
                    </flux:modal>
                </div>
            @else
                <div class="space-y-4">
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-lg flex items-center gap-3">
                        <span class="material-symbols-outlined">lock</span>
                        <div class="font-medium">Caja Cerrada</div>
                    </div>

                    <div class="flex justify-center mt-6">
                        <flux:modal.trigger name="abrir-caja" class="overflow-y-auto max-h-[85vh]">
                            <button class="w-full sm:w-auto btn-primary btn-primary--emerald justify-center px-8 py-3">
                                <span class="material-symbols-outlined icon-sm">lock_open</span>
                                Abrir Caja
                            </button>
                        </flux:modal.trigger>
                    </div>

                    <flux:modal name="abrir-caja" class="min-w-md overflow-y-auto max-h-[85vh] p-0!">
                        <form wire:submit.prevent="abrirCaja">
                            <!-- Header decorativo -->
                            <div class="h-24 bg-linear-to-br from-emerald-400 to-teal-500 flex items-center justify-center relative">
                                <div class="absolute inset-0 opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMSIvPgo8L3N2Zz4=')]"></div>
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center shadow-lg border border-white/30 z-10">
                                    <span class="material-symbols-outlined text-4xl text-white">point_of_sale</span>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-center mb-1" style="color: var(--vc-text);">Abrir Caja</h3>
                                <p class="text-sm text-center mb-6" style="color: var(--vc-text-muted);">Ingrese el monto base (sencillo) con el que inicia el turno.</p>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium mb-1" style="color: var(--vc-text);">Monto de Apertura</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-zinc-500 sm:text-sm">S/</span>
                                        </div>
                                        <input type="number" step="0.01" wire:model="opening_amount" class="w-full pl-8 pr-3 py-2 rounded-lg border focus:ring-2 outline-none transition-all" style="background: var(--vc-glass-bg); border-color: var(--vc-border); color: var(--vc-text);" placeholder="0.00" />
                                    </div>
                                    <flux:error name="opening_amount" />
                                </div>

                                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                                    <flux:modal.close class="w-full">
                                        <button type="button" class="w-full py-2.5 rounded-xl font-semibold border transition-all" style="border-color: var(--vc-border); color: var(--vc-text);"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></button>
                                    </flux:modal.close>
                                    <button type="submit" class="w-full btn-primary btn-primary--emerald justify-center py-2.5">Confirmar Apertura</button>
                                </div>
                            </div>
                        </form>
                    </flux:modal>
                </div>
            @endif
        </div>

        {{-- Historial --}}
        <div class="vc-panel animate-fade-in" style="animation-delay: 0.1s;">
            <h2 class="text-lg font-bold font-display mb-4" style="color: var(--vc-text);">Últimos Arqueos</h2>
            
            @if($registers->isEmpty())
                <div class="text-center text-zinc-500 py-8">
                    No hay registros anteriores.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($registers as $reg)
                        <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-medium">{{ $reg->opened_at->format('d/m/Y') }}</div>
                                <div>
                                    @if($reg->status === 'ABIERTA')
                                        <flux:badge color="emerald" size="sm">ABIERTA</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">CERRADA</flux:badge>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><span class="text-zinc-500">Apertura:</span> S/ {{ number_format($reg->opening_amount, 2) }}</div>
                                @if($reg->status === 'CERRADA')
                                    <div><span class="text-zinc-500">Esperado:</span> S/ {{ number_format($reg->opening_amount + $reg->calculated_amount, 2) }}</div>
                                    <div><span class="text-zinc-500">Real:</span> S/ {{ number_format($reg->real_amount, 2) }}</div>
                                    <div>
                                        <span class="text-zinc-500">Desfase:</span> 
                                        <span class="{{ $reg->difference < 0 ? 'text-red-600' : ($reg->difference > 0 ? 'text-blue-600' : 'text-emerald-600') }} font-medium">
                                            S/ {{ number_format($reg->difference, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            @if($reg->notes)
                                <div class="mt-2 text-xs text-zinc-500 bg-zinc-50 dark:bg-vc-surface-alt p-2 rounded">
                                    {{ $reg->notes }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
