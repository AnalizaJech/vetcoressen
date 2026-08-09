<div>
    <x-slot:title>Dashboard</x-slot:title>

    <div x-data="{
        alertasPendientes: {{ $alertasInventario + $lotesProximosVencer->count() }},
        alertaIds: '{{ $productosEnAlerta->pluck("id")->sort()->join(",") }},{{ $lotesProximosVencer->pluck("id")->sort()->join(",") }}',
        citasProximasCount: {{ $citasProximas->count() }},
        citasIds: '{{ $citasProximas->pluck("id")->sort()->join(",") }}',
        mostrarAlerta() {
            if (this.citasProximasCount > 0) {
                const vistasCitas = sessionStorage.getItem('alerta_citas_ids') || '';
                if (vistasCitas !== this.citasIds) {
                    setTimeout(() => {
                        Flux.modal('alerta-citas-proximas').show();
                        sessionStorage.setItem('alerta_citas_ids', this.citasIds);
                    }, 250);
                    return; // Solo muestra una alerta al cargar
                }
            }

            if (this.alertasPendientes > 0) {
                const vistas = sessionStorage.getItem('alerta_stock_ids') || '';
                if (vistas !== this.alertaIds) {
                    setTimeout(() => {
                        Flux.modal('alerta-stock-minimo').show();
                        sessionStorage.setItem('alerta_stock_ids', this.alertaIds);
                    }, 250);
                }
            }
        }
    }" x-init="mostrarAlerta()">

    {{-- Cabecera con icono --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="kpi-icon kpi-icon--emerald">
                <span class="material-symbols-outlined">dashboard</span>
            </div>
            <div>
                <flux:heading size="xl"><span>Dashboard</span></flux:heading>
                <flux:subheading><span>Resumen general de tu veterinaria</span></flux:subheading>
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('citas.crear') }}" class="btn-primary btn-primary--red justify-center text-xs px-3 py-1.5">
                <span class="material-symbols-outlined icon-sm">event</span>
                <span>Nueva Cita</span>
            </a>
            <a href="{{ route('caja.venta') }}" class="btn-primary btn-primary--blue justify-center text-xs px-3 py-1.5">
                <span class="material-symbols-outlined icon-sm">point_of_sale</span>
                <span>Nueva Venta</span>
            </a>
            <a href="{{ route('inventario.index') }}" class="btn-primary btn-primary--amber justify-center text-xs px-3 py-1.5">
                <span class="material-symbols-outlined icon-sm">inventory_2</span>
                <span>Inventario</span>
            </a>
        </div>
    </div>

    {{-- ═══ KPIs Premium ═══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Ingresos del día --}}
        <div class="kpi-card kpi-card--emerald animate-fade-in">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wide" style="color: var(--vc-text-muted);">
                    {{ match($filtroTiempo) { 'hoy' => 'Ingresos Hoy', 'semana' => 'Ingresos Última Semana', 'mes' => 'Ingresos Este Mes', 'anio' => 'Ingresos Este Año', default => 'Ingresos' } }}
                </span>
                <div class="kpi-icon kpi-icon--emerald">
                    <span class="material-symbols-outlined">payments</span>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-extrabold font-display" style="color: var(--vc-text);">
                S/ {{ number_format($ingresosDia, 2) }}
            </p>
            <p class="text-xs mt-1.5" style="color: var(--vc-text-muted);">
                {{ $ingresosDia > 0 ? 'Ventas concretadas' : 'Aún no hay ventas' }}
            </p>
        </div>

        {{-- Citas pendientes --}}
        <div class="kpi-card kpi-card--blue animate-fade-in" style="animation-delay: 0.05s;">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wide" style="color: var(--vc-text-muted);">Citas Pendientes</span>
                <div class="kpi-icon kpi-icon--blue">
                    <span class="material-symbols-outlined">calendar_month</span>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-extrabold font-display" style="color: var(--vc-text);">{{ $citasPendientes }}</p>
            <p class="text-xs mt-1.5" style="color: var(--vc-text-muted);">
                {{ $citasPendientes > 0 ? 'Para hoy' : 'Sin citas para hoy' }}
            </p>
        </div>

        {{-- Alertas de inventario --}}
        <div class="kpi-card kpi-card--amber animate-fade-in" style="animation-delay: 0.1s; {{ $alertasInventario > 0 ? 'border-color: rgba(245, 158, 11, 0.4);' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wide" style="color: var(--vc-text-muted);">Alertas de Inventario</span>
                <div class="kpi-icon kpi-icon--amber">
                    <span class="material-symbols-outlined">category</span>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-extrabold font-display" style="color: var(--vc-text);">{{ $alertasInventario }}</p>
            <p class="text-xs mt-1.5" style="color: var(--vc-text-muted);">
                {{ $alertasInventario > 0 ? 'Stock crítico' : 'Stock normal' }}
            </p>
        </div>

        {{-- En atención ahora (proxy: citas en progreso) --}}
        <div class="kpi-card kpi-card--red animate-fade-in" style="animation-delay: 0.15s; {{ $internados > 0 ? 'border-color: rgba(239, 68, 68, 0.4);' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wide" style="color: var(--vc-text-muted);">En atención ahora</span>
                <div class="kpi-icon kpi-icon--red">
                    <span class="material-symbols-outlined">local_hospital</span>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-extrabold font-display" style="color: var(--vc-text);">{{ $internados }}</p>
            <p class="text-xs mt-1.5" style="color: var(--vc-text-muted);">
                {{ $internados > 0 ? 'Mascotas en consulta' : 'Ninguna atención en progreso' }}
            </p>
        </div>
    </div>

    {{-- ═══ Gráfico semanal + Últimas ventas ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">

        {{-- Gráfico de ingresos semanales --}}
        <div class="vc-panel animate-slide-up" style="background: linear-gradient(135deg, var(--vc-glass-bg), rgba(16, 185, 129, 0.03)); position: relative; overflow: hidden;">
            {{-- Decoración premium --}}
            <div style="position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-lg font-extrabold font-display text-zinc-900 dark:text-zinc-100">Ingresos</h2>
                    {{-- Mini KPI badge con total del periodo --}}
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold" style="background: rgba(16, 185, 129, 0.1); color: var(--vc-emerald-light);">
                            <span class="material-symbols-outlined text-[14px]">trending_up</span>
                            S/ {{ number_format(collect($ingresosSemana)->sum('total'), 2) }}
                        </span>
                        <span class="text-[10px]" style="color: var(--vc-text-muted);">
                            {{ match($filtroTiempo) { 'hoy' => 'acumulado hoy', 'semana' => 'esta semana', 'mes' => 'este mes', 'anio' => 'este año', default => 'total' } }}
                        </span>
                    </div>
                </div>
                <div class="w-32">
                    <x-vc-dropdown 
                        wire:model.live="filtroTiempo"
                        :options="[
                            ['value' => 'hoy', 'label' => 'Hoy'],
                            ['value' => 'semana', 'label' => 'Última Semana'],
                            ['value' => 'mes', 'label' => 'Este Mes'],
                            ['value' => 'anio', 'label' => 'Este Año']
                        ]"
                        :selected="$filtroTiempo"
                        placeholder="Filtrar"
                    />
                </div>
            </div>

            <div class="w-full overflow-x-auto custom-scrollbar" x-data="{ hoveredBar: null }">
                <div class="flex items-end gap-3 min-w-max pb-2" style="height: 180px;">
                    @foreach($ingresosSemana as $idx => $dia)
                        @php
                            $porcentaje = $maxIngreso > 0 ? ($dia['total'] / $maxIngreso) * 100 : 0;
                            $alturaMin = $dia['total'] > 0 ? max($porcentaje, 8) : 4;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1.5 group cursor-pointer" style="min-width: 40px;"
                             x-on:mouseenter="hoveredBar = {{ $idx }}"
                             x-on:mouseleave="hoveredBar = null">
                            {{-- Valor con animación de hover --}}
                            <span class="text-xs font-medium transition-all duration-200"
                                  x-bind:class="hoveredBar === {{ $idx }} ? 'scale-110 font-bold' : ''"
                                  x-bind:style="hoveredBar === {{ $idx }} ? 'color: var(--vc-emerald-light);' : 'color: var(--vc-text-muted);'">
                                {{ $dia['total'] > 0 ? 'S/' . number_format($dia['total'], 0) : '-' }}
                            </span>
                            {{-- Barra con efecto hover premium --}}
                            <div
                                class="w-full rounded-lg transition-all duration-300"
                                x-bind:style="hoveredBar === {{ $idx }}
                                    ? 'height: {{ $alturaMin }}%; min-height: 4px; background: {{ $dia['total'] > 0 ? 'linear-gradient(to top, #059669, #34d399)' : 'rgba(255,255,255,0.05)' }}; box-shadow: 0 0 16px rgba(16, 185, 129, 0.4); min-width: 24px; transform: scaleY(1.05);'
                                    : 'height: {{ $alturaMin }}%; min-height: 4px; background: {{ $dia['total'] > 0 ? 'linear-gradient(to top, var(--vc-emerald-dark), var(--vc-emerald-light))' : 'rgba(255,255,255,0.05)' }}; box-shadow: {{ $dia['total'] > 0 ? 'var(--vc-shadow-glow)' : 'none' }}; min-width: 24px;'"
                            ></div>
                            <span class="text-[10px] font-medium" style="color: var(--vc-text-muted);">{{ $dia['dia'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Últimas ventas --}}
        <div class="vc-panel animate-slide-up" style="animation-delay: 0.1s; background: linear-gradient(135deg, var(--vc-glass-bg), rgba(59, 130, 246, 0.02)); position: relative; overflow: hidden;">
            {{-- Decoración premium --}}
            <div style="position: absolute; top: -40px; right: -40px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(59, 130, 246, 0.06) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-extrabold font-display text-zinc-900 dark:text-zinc-100">Últimas Ventas</h2>
                    <span class="text-[10px]" style="color: var(--vc-text-muted);">{{ $ultimasVentas->count() }} transacciones recientes</span>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('caja.index') }}" class="text-xs font-medium flex items-center gap-1 transition-colors hover:text-emerald-600" style="color: var(--vc-emerald-light);"><span>Ver todas</span> <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
                </div>
            </div>

            @if($ultimasVentas->isEmpty())
                <div class="vc-empty-state">
                    <div class="vc-empty-icon">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                    <p class="vc-empty-title">Sin ventas</p>
                    <p class="vc-empty-text">No hay ventas registradas recientemente.</p>
                </div>
            @else
                <div class="space-y-2 max-h-[300px] overflow-y-auto pr-2 scrollbar-thin">
                    @foreach($ultimasVentas as $venta)
                        <div class="flex items-center justify-between p-3 rounded-xl transition-all duration-200 hover:shadow-md cursor-default group" style="background: var(--vc-glass-bg); border: 1px solid var(--vc-border);">
                            <div class="flex items-center gap-3">
                                {{-- Avatar circular --}}
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 text-xs font-bold uppercase" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05)); color: var(--vc-emerald-light);">
                                    {{ substr($venta->cliente?->first_name ?? 'C', 0, 1) }}{{ substr($venta->cliente?->last_name ?? 'G', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold" style="color: var(--vc-text);">{{ $venta->cliente?->nombre_completo ?? 'Cliente general' }}</p>
                                    <p class="text-[10px]" style="color: var(--vc-text-muted);">{{ $venta->created_at->format('d/m/Y h:i A') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold" style="color: var(--vc-emerald-light);">S/ {{ number_format($venta->total, 2) }}</span>
                                @php
                                    $estadoDetalles = match($venta->status) {
                                        'PAGADO' => ['class' => 'badge-emerald', 'icon' => 'check_circle'],
                                        'PENDIENTE' => ['class' => 'badge-amber', 'icon' => 'schedule'],
                                        'ANULADO' => ['class' => 'badge-red', 'icon' => 'cancel'],
                                        default => ['class' => 'badge-blue', 'icon' => 'info'],
                                    };
                                @endphp
                                <span class="badge {{ $estadoDetalles['class'] }} text-[10px] flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">{{ $estadoDetalles['icon'] }}</span>{{ $venta->status }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ Próximas citas ═══ --}}
    <div class="vc-panel animate-slide-up mb-8" style="animation-delay: 0.2s; background: linear-gradient(135deg, var(--vc-glass-bg), rgba(139, 92, 246, 0.02)); position: relative; overflow: hidden;">
        {{-- Decoración premium --}}
        <div style="position: absolute; top: -40px; right: -40px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(139, 92, 246, 0.06) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-lg font-extrabold font-display text-zinc-900 dark:text-zinc-100">Citas Programadas</h2>
                <span class="text-[10px]" style="color: var(--vc-text-muted);">{{ $citasHoy->count() }} citas en el periodo</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-32">
                    <x-vc-dropdown 
                        wire:model.live="filtroTiempoCitas"
                        :options="[
                            ['value' => 'hoy', 'label' => 'Hoy'],
                            ['value' => 'semana', 'label' => 'Esta Semana'],
                            ['value' => 'mes', 'label' => 'Este Mes'],
                            ['value' => 'anio', 'label' => 'Este Año']
                        ]"
                        :selected="$filtroTiempoCitas"
                        placeholder="Filtrar"
                    />
                </div>
                <a href="{{ route('citas.index') }}" class="text-xs font-medium flex items-center gap-1 transition-colors hover:text-emerald-600" style="color: var(--vc-emerald-light);"><span>Ver todas</span> <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
            </div>
        </div>

        @if($citasHoy->isEmpty())
            <div class="vc-empty-state py-6">
                <div class="vc-empty-icon">
                    <span class="material-symbols-outlined">event_available</span>
                </div>
                <p class="vc-empty-title">Sin citas</p>
                <p class="vc-empty-text">No hay citas para el periodo seleccionado</p>
            </div>
        @else
            <div class="space-y-2 max-h-72 overflow-y-auto custom-scrollbar pr-2">
                @foreach($citasHoy as $citaHoy)
                    <div class="flex items-center justify-between p-3 rounded-xl transition-all duration-200 hover:shadow-md group" style="background: var(--vc-glass-bg); border: 1px solid var(--vc-border);">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--color-vc-amber-light, rgba(245, 158, 11, 0.1)); color: var(--color-vc-amber);">
                                <span class="material-symbols-outlined">schedule</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold" style="color: var(--vc-text);">{{ $citaHoy->fecha_hora->format('d/m H:i') }} - {{ $citaHoy->mascota?->name ?? '-' }}</p>
                                <p class="text-xs line-clamp-1" style="color: var(--vc-text-muted);">{{ $citaHoy->veterinario?->name ?? '-' }} · <span>{{ $citaHoy->reason ? "'".$citaHoy->reason."'" : 'Sin motivo especificado' }}</span></p>
                            </div>
                        </div>
                        @php
                            $estadoDetallesCita = match($citaHoy->status) {
                                'PENDIENTE' => ['class' => 'badge-amber', 'icon' => 'schedule'],
                                'CONFIRMADA' => ['class' => 'badge-blue', 'icon' => 'event_available'],
                                'EN_PROGRESO' => ['class' => 'badge-purple', 'icon' => 'running_with_errors'],
                                default => ['class' => 'badge-zinc', 'icon' => 'info'],
                            };
                        @endphp
                        <span class="badge {{ $estadoDetallesCita['class'] }} shrink-0 flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">{{ $estadoDetallesCita['icon'] }}</span>{{ $citaHoy->status }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ═══ Estadísticas rápidas ═══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="vc-panel flex items-center gap-4 animate-fade-in">
            <div class="kpi-icon kpi-icon--emerald">
                <span class="material-symbols-outlined">group</span>
            </div>
            <div>
                <p class="text-2xl font-extrabold font-display" style="color: var(--vc-text);">{{ $totalClientes }}</p>
                <p class="text-xs" style="color: var(--vc-text-muted);">Clientes activos</p>
            </div>
        </div>

        <div class="vc-panel flex items-center gap-4 animate-fade-in" style="animation-delay: 0.05s;">
            <div class="kpi-icon kpi-icon--amber">
                <span class="material-symbols-outlined">pets</span>
            </div>
            <div>
                <p class="text-2xl font-extrabold font-display" style="color: var(--vc-text);">{{ $totalMascotas }}</p>
                <p class="text-xs" style="color: var(--vc-text-muted);">Mascotas registradas</p>
            </div>
        </div>

        <div class="vc-panel flex items-center gap-4 animate-fade-in" style="animation-delay: 0.1s;">
            <div class="kpi-icon kpi-icon--blue">
                <span class="material-symbols-outlined">currency_exchange</span>
            </div>
            <div>
                <p class="text-2xl font-extrabold font-display" style="color: var(--vc-text);">{{ $tipoCambio > 0 ? number_format($tipoCambio, 3) : '-' }}</p>
                <p class="text-xs" style="color: var(--vc-text-muted);">Tipo de Cambio (USD/PEN)</p>
            </div>
        </div>

        <div class="vc-panel flex items-center gap-4 animate-fade-in" style="animation-delay: 0.15s;" 
             x-data="{ 
                 date: '', 
                 time: '', 
                 country: '' 
             }" 
             x-init="
                 const updateTime = () => {
                     const lang = (window.Alpine && Alpine.store('i18n') ? Alpine.store('i18n').locale : document.documentElement.lang) || 'es';
                     const now = new Date();
                     date = now.toLocaleDateString(lang, { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
                     time = now.toLocaleTimeString(lang, { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
                     try {
                         let region = 'PE';
                         if (navigator.languages && navigator.languages.length > 0) {
                             const loc = new Intl.Locale(navigator.languages[0]);
                             if (loc.region) region = loc.region;
                         }
                         country = new Intl.DisplayNames([lang], {type: 'region'}).of(region);
                     } catch(e) {
                         country = 'Perú';
                     }
                 };
                 updateTime();
                 setInterval(updateTime, 1000);
                 window.addEventListener('language-changed', updateTime);
             ">
            <div class="kpi-icon kpi-icon--blue" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                <span class="material-symbols-outlined">public</span>
            </div>
            <div>
                <p class="text-sm font-bold capitalize" style="color: var(--vc-text);"><span x-text="time"></span></p>
                <p class="text-[10px] capitalize" style="color: var(--vc-text-muted);">
                    <span x-text="date"></span>
                </p>
            </div>
        </div>
    </div>

    {{-- ═══ Modal Premium de Alerta de Stock ═══ --}}
    @if($alertasInventario > 0 || $lotesProximosVencer->count() > 0)
    <flux:modal :closable="false" name="alerta-stock-minimo" class="min-w-md overflow-hidden !p-0">
        <!-- Header decorativo -->
        <div class="h-24 bg-linear-to-br from-amber-500 to-orange-600 flex items-center justify-center relative">
            <div class="absolute inset-0 opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMSIvPgo8L3N2Zz4=')]"></div>
            <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center shadow-lg border border-white/30 z-10">
                <span class="material-symbols-outlined text-4xl text-white">warning</span>
            </div>
        </div>

        <!-- Contenido del Modal -->
            <div class="p-6 text-center max-h-[70vh] overflow-y-auto">
                <flux:heading size="xl" class="mb-2"><span>¡Atención de Inventario!</span></flux:heading>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">
                    <span>Se han detectado</span> <strong class="text-amber-600 dark:text-amber-500">{{ $alertasInventario }} productos</strong> <span>en stock crítico y</span> <strong class="text-amber-600 dark:text-amber-500">{{ $lotesProximosVencer->count() }} lotes</strong> <span>próximos a vencer.</span>
                </p>

                @if($alertasInventario > 0)
                <div class="bg-amber-50/50 dark:bg-amber-500/10 rounded-xl p-4 mb-4 border border-amber-100 dark:border-amber-500/20 text-left">
                    <h4 class="text-xs font-bold uppercase text-amber-500 mb-3 tracking-wider">Productos Críticos</h4>
                    <ul class="space-y-3">
                        @foreach($productosEnAlerta->take(3) as $productoAlerta)
                        <li class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-zinc-400 text-xl">
                                    {{ $productoAlerta->type === 'Medicamento' ? 'vaccines' : ($productoAlerta->type === 'Alimento' ? 'pets' : 'shopping_bag') }}
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 line-clamp-1" title="{{ $productoAlerta->name }}">{{ Str::limit($productoAlerta->name, 30) }}</p>
                                    <p class="text-xs text-zinc-500">{{ $productoAlerta->type }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-amber font-bold text-xs flex items-center gap-1 justify-end"><span class="material-symbols-outlined text-[14px]">warning</span> <span>Stock:</span> {{ round($productoAlerta->current_stock) }}</span>
                                <p class="text-[10px] text-zinc-400 mt-1"><span>Min:</span> {{ round($productoAlerta->minimum_stock) }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @if($alertasInventario > 3)
                        <p class="text-xs text-center text-red-500/70 mt-3 font-medium">Y {{ $alertasInventario - 3 }} productos más...</p>
                    @endif
                </div>
                @endif

                @if($lotesProximosVencer->count() > 0)
                <div class="bg-amber-50/50 dark:bg-amber-500/10 rounded-xl p-4 mb-6 border border-amber-100 dark:border-amber-500/20 text-left">
                    <h4 class="text-xs font-bold uppercase text-amber-500 mb-3 tracking-wider">Lotes Próximos a Vencer</h4>
                    <ul class="space-y-3">
                        @foreach($lotesProximosVencer->take(5) as $loteAlerta)
                        @php
                            $diasParaVencer = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($loteAlerta->fecha_vencimiento)->startOfDay(), false);
                            if ($diasParaVencer < 0) {
                                $vencimientoClase = 'badge-red';
                                $vencimientoIcono = 'error';
                                $textColor = 'text-red-500';
                            } elseif ($diasParaVencer <= 30) {
                                $vencimientoClase = 'badge-red';
                                $vencimientoIcono = 'event_busy';
                                $textColor = 'text-red-400';
                            } elseif ($diasParaVencer <= 60) {
                                $vencimientoClase = 'badge-amber';
                                $vencimientoIcono = 'warning';
                                $textColor = 'text-amber-500';
                            } else {
                                $vencimientoClase = 'badge-blue';
                                $vencimientoIcono = 'event_note';
                                $textColor = 'text-blue-500';
                            }
                        @endphp
                        <li class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined {{ $textColor }} text-xl">{{ $vencimientoIcono }}</span>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 line-clamp-1" title="{{ $loteAlerta->product?->name }}">{{ Str::limit($loteAlerta->product?->name ?? 'Producto', 30) }}</p>
                                    <p class="text-xs text-zinc-500"><span>Lote:</span> {{ $loteAlerta->lote }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge {{ $vencimientoClase }} font-bold text-xs flex items-center gap-1 justify-end"><span class="material-symbols-outlined text-[14px]">{{ $vencimientoIcono }}</span> {{ \Carbon\Carbon::parse($loteAlerta->fecha_vencimiento)->format('d/m/Y') }}</span>
                                <p class="text-[10px] text-zinc-400 mt-1"><span>Stock:</span> {{ round($loteAlerta->stock_actual) }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @if($lotesProximosVencer->count() > 5)
                        <p class="text-xs text-center text-amber-500/70 mt-3 font-medium">Y {{ $lotesProximosVencer->count() - 5 }} lotes más...</p>
                    @endif
                </div>
                @endif

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-4">
                    <flux:modal.close class="w-full sm:w-auto">
                        <flux:button variant="ghost" class="w-full sm:w-auto flex items-center gap-2 justify-center">
                            <span class="material-symbols-outlined icon-sm">close</span>
                            <span>Entendido</span>
                        </flux:button>
                    </flux:modal.close>
                    <a href="{{ route('inventario.index') }}" class="w-full sm:w-auto">
                        <button type="button" class="btn-primary btn-primary--amber w-full sm:w-auto justify-center px-4 py-2 font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined icon-sm">archive</span>
                            <span>Reponer Stock</span>
                        </button>
                    </a>
                </div>
            </div>
    </flux:modal>
    @endif

    @if($citasProximas->count() > 0)
    <flux:modal name="alerta-citas-proximas" class="min-w-[22rem] sm:min-w-[24rem]">
        <div class="text-center pb-2">
            <div class="mx-auto w-16 h-16 bg-blue-100 dark:bg-blue-500/20 text-blue-500 rounded-full flex items-center justify-center mb-4 shadow-sm border border-blue-200 dark:border-blue-500/30">
                <span class="material-symbols-outlined text-3xl">event_upcoming</span>
            </div>
            
            <h2 class="text-xl font-extrabold font-display text-zinc-900 dark:text-zinc-100 mb-2">
                Citas Próximas
            </h2>
            
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">
                Tienes {{ $citasProximas->count() }} cita(s) programada(s) para las próximas 2 horas.
            </p>

            <div class="bg-blue-50/50 dark:bg-blue-500/10 rounded-xl p-4 mb-6 border border-blue-100 dark:border-blue-500/20 text-left">
                <ul class="space-y-3">
                    @foreach($citasProximas as $citaProxima)
                    <li class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-blue-500 text-xl">schedule</span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 line-clamp-1" title="{{ $citaProxima->mascota?->name }}">{{ Str::limit($citaProxima->mascota?->name ?? 'Mascota', 30) }}</p>
                                <p class="text-xs text-zinc-500">{{ $citaProxima->veterinario?->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-blue font-bold text-xs flex items-center gap-1 justify-end">{{ $citaProxima->fecha_hora->format('H:i') }}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-4">
                <flux:modal.close class="w-full sm:w-auto">
                    <button type="button" class="w-full sm:w-auto px-4 py-2 text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl font-medium transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined icon-sm">close</span>
                        <span>Cerrar</span>
                    </button>
                </flux:modal.close>
                <a href="{{ route('citas.index') }}" class="w-full sm:w-auto">
                    <button type="button" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white rounded-xl shadow-sm hover:shadow transition-all px-4 py-2 font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined icon-sm">calendar_month</span>
                        <span>Ir a Citas</span>
                    </button>
                </a>
            </div>
        </div>
    </flux:modal>
    @endif
    </div>
</div>
