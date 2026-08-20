<div>
    <x-slot:title>Dashboard</x-slot:title>

    <div class="space-y-6" x-data="dashboardCharts({ atenciones: {{ json_encode($atencionesGrafico) }}, ingresos: {{ json_encode($ingresosSemana) }} })" x-init="init()">
        {{-- ═══ Header de Bienvenida y Accesos Rápidos ═══ --}}
        <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <span class="material-symbols-outlined text-2xl">space_dashboard</span>
                    </div>
                    <div>
                        <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                            <span x-text="$store.i18n.t('dashboard.title') || 'Dashboard'">Dashboard</span>
                        </h1>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('dashboard.subtitle') || 'Resumen operativo, métricas clínicas e inteligencia preventiva'">
                            Resumen operativo, métricas clínicas e inteligencia preventiva
                        </p>
                    </div>
                </div>
            </div>

            {{-- Botones de Acción Rápida --}}
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('citas.crear') }}" wire:navigate class="btn-primary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined icon-sm">add</span>
                    <span x-text="$store.i18n.t('btn.newAppointment') || 'Nueva Cita'">Nueva Cita</span>
                </a>
                <a href="{{ route('caja.index') }}" wire:navigate class="btn-secondary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined icon-sm">point_of_sale</span>
                    <span x-text="$store.i18n.t('sidebar.point_of_sale') || 'Punto de Venta'">Punto de Venta</span>
                </a>
            </div>
        </div>

        {{-- ═══ KPIs Superiores (4 Tarjetas Principales) ═══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- KPI 1: Ventas del Día --}}
            <div class="kpi-card kpi-card--emerald shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('dashboard.salesToday') || 'Ventas de Hoy'">Ventas de Hoy</span>
                    <div class="kpi-icon kpi-icon--emerald">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    S/ {{ number_format($ventasHoy, 2) }}
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[11px] text-zinc-400">
                        <span x-text="{{ $totalVentasHoy }} + ' ' + ($store.i18n.t('dashboard.receiptsIssued') || 'comprobantes emitidos')">{{ $totalVentasHoy }} emitidos</span>
                    </span>
                </div>
            </div>

            {{-- KPI 2: Citas del Día --}}
            <div class="kpi-card kpi-card--blue shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('dashboard.appointmentsToday') || 'Citas de Hoy'">Citas de Hoy</span>
                    <div class="kpi-icon kpi-icon--blue">
                        <span class="material-symbols-outlined">calendar_today</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    {{ $citasHoyCount }}
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="badge badge-blue text-[10px]">
                        <span x-text="{{ $citasCompletadasHoy }} + ' ' + ($store.i18n.t('dashboard.attended') || 'atendidas')">{{ $citasCompletadasHoy }} atendidas</span>
                    </span>
                    <span class="text-[11px] text-zinc-400">
                        <span x-text="{{ max(0, $citasHoyCount - $citasCompletadasHoy) }} + ' ' + ($store.i18n.t('status.pending') || 'pendientes')">{{ max(0, $citasHoyCount - $citasCompletadasHoy) }} pendientes</span>
                    </span>
                </div>
            </div>

            {{-- KPI 3: Total Pacientes --}}
            <div class="kpi-card kpi-card--purple shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('dashboard.totalPatients') || 'Total Pacientes'">Total Pacientes</span>
                    <div class="kpi-icon kpi-icon--purple">
                        <span class="material-symbols-outlined">pets</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    {{ $totalMascotas }}
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[11px] text-zinc-400">
                        <span x-text="{{ $totalClientes }} + ' ' + ($store.i18n.t('dashboard.owners') || 'propietarios')">{{ $totalClientes }} propietarios</span>
                    </span>
                </div>
            </div>

            {{-- KPI 4: Estado de Caja --}}
            <div class="kpi-card kpi-card--amber shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('dashboard.cashRegisterStatus') || 'Estado de Caja'">Estado de Caja</span>
                    <div class="kpi-icon kpi-icon--amber">
                        <span class="material-symbols-outlined">point_of_sale</span>
                    </div>
                </div>
                @if($cajaAbierta)
                    <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                        S/ {{ number_format($cajaAbierta->monto_inicial, 2) }}
                    </h3>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="badge badge-emerald text-[10px]" x-text="$store.i18n.t('status.open') || 'ABIERTA'">ABIERTA</span>
                        <span class="text-[11px] text-zinc-400 truncate">{{ $cajaAbierta->usuario->name ?? 'Usuario' }}</span>
                    </div>
                @else
                    <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-400 tracking-tight font-display mb-1" x-text="$store.i18n.t('status.closed') || 'CERRADA'">
                        CERRADA
                    </h3>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="badge badge-gray text-[10px]" x-text="$store.i18n.t('dashboard.noActiveShift') || 'Sin turno activo'">Sin turno activo</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ═══ Fila 1 (2 en línea): Atenciones Médicas vs Citas Programadas ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Columna Izquierda (50%): Gráfico de Atenciones Médicas --}}
            <div class="vc-panel flex flex-col justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-500">bar_chart</span>
                            <span x-text="$store.i18n.t('dashboard.medicalAttentions') || 'Atenciones Médicas'">Atenciones Médicas</span>
                        </h2>
                        <p class="text-xs text-zinc-400 mt-0.5" x-text="$store.i18n.t('dashboard.attentionsSubtitle') || 'Comparativa de citas realizadas vs pendientes'">
                            Comparativa de citas realizadas vs pendientes
                        </p>
                    </div>

                    {{-- Filtro Dinámico de Atenciones --}}
                    <div class="w-full sm:w-44">
                        <x-vc-dropdown 
                            wire:model.live="filtroAtenciones"
                            :options="[
                                ['value' => 'hoy', 'label' => 'report.today'],
                                ['value' => 'semana', 'label' => 'dashboard.lastWeek'],
                                ['value' => 'mes', 'label' => 'dashboard.thisMonth'],
                                ['value' => 'anio', 'label' => 'dashboard.thisYear'],
                            ]"
                            :selected="$filtroAtenciones"
                            placeholder="dashboard.filter"
                        />
                    </div>
                </div>

                <div class="relative h-64 w-full" wire:ignore>
                    <canvas id="atencionesChart"></canvas>
                </div>
            </div>

            {{-- Columna Derecha (50%): Citas Programadas --}}
            <div class="vc-panel flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">calendar_month</span>
                            <span x-text="$store.i18n.t('dashboard.scheduledAppointments') || 'Citas Programadas'">Citas Programadas</span>
                        </h2>
                        <p class="text-xs text-zinc-400 mt-0.5" x-text="$store.i18n.t('dashboard.scheduledSubtitle') || 'Agenda clínica del periodo'">
                            Agenda clínica del periodo
                        </p>
                    </div>
                    <a href="{{ route('citas.index') }}" wire:navigate class="px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 border border-emerald-200/50 dark:border-emerald-800/40 transition-all flex items-center gap-1">
                        <span x-text="$store.i18n.t('btn.viewAll') || 'Ver todas'">Ver todas</span>
                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </a>
                </div>

                <div class="space-y-3 overflow-y-auto max-h-64 pr-1">
                    @forelse($citasHoy as $cita)
                        <div class="p-3 rounded-xl border border-zinc-100 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-800/30 flex items-center justify-between gap-3 hover:border-emerald-500/30 transition-all">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/20 flex flex-col items-center justify-center text-blue-600 dark:text-blue-400 shrink-0 font-bold">
                                    <span class="text-[10px] leading-tight uppercase">{{ \Carbon\Carbon::parse($cita->fecha_hora)->translatedFormat('M') }}</span>
                                    <span class="text-xs leading-none font-extrabold">{{ \Carbon\Carbon::parse($cita->fecha_hora)->format('d') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $cita->mascota->name ?? 'Mascota' }}
                                        <span class="text-zinc-400 font-normal">({{ $cita->cliente->nombre_completo ?? 'Cliente' }})</span>
                                    </h4>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[13px]">schedule</span>
                                        {{ \Carbon\Carbon::parse($cita->fecha_hora)->format('H:i') }} - {{ $cita->motivo ?? 'Consulta' }}
                                    </p>
                                </div>
                            </div>
                            <span class="badge {{ $cita->status === 'CONFIRMADA' ? 'badge-emerald' : ($cita->status === 'COMPLETADA' ? 'badge-blue' : 'badge-amber') }} text-[10px] shrink-0">
                                <span x-text="$store.i18n.t('status.{{ strtolower($cita->status) }}') || '{{ $cita->status }}'">{{ $cita->status }}</span>
                            </span>
                        </div>
                    @empty
                        <div class="py-12 text-center text-zinc-400 text-xs">
                            <span class="material-symbols-outlined text-3xl mb-1 text-zinc-300 dark:text-zinc-600">event_available</span>
                            <p x-text="$store.i18n.t('dashboard.noAppointmentsPeriod') || 'Sin citas programadas en este período'">Sin citas programadas en este período</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ═══ Fila 2 (2 en línea): Ingresos Monetarios vs Últimas Ventas ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Columna Izquierda (50%): Gráfico de Ingresos Monetarios --}}
            <div class="vc-panel flex flex-col justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-500">trending_up</span>
                            <span x-text="$store.i18n.t('dashboard.monetaryIncome') || 'Ingresos Monetarios'">Ingresos Monetarios</span>
                        </h2>
                        <p class="text-xs text-zinc-400 mt-0.5" x-text="$store.i18n.t('dashboard.incomeSubtitle') || 'Evolución de ingresos generados'">
                            Evolución de ingresos generados
                        </p>
                    </div>

                    {{-- Filtro Dinámico de Ingresos --}}
                    <div class="w-full sm:w-44">
                        <x-vc-dropdown 
                            wire:model.live="filtroTiempo"
                            :options="[
                                ['value' => 'hoy', 'label' => 'report.today'],
                                ['value' => 'semana', 'label' => 'dashboard.lastWeek'],
                                ['value' => 'mes', 'label' => 'dashboard.thisMonth'],
                                ['value' => 'anio', 'label' => 'dashboard.thisYear'],
                            ]"
                            :selected="$filtroTiempo"
                            placeholder="dashboard.filter"
                        />
                    </div>
                </div>

                <div class="relative h-64 w-full" wire:ignore>
                    <canvas id="ingresosChart"></canvas>
                </div>
            </div>

            {{-- Columna Derecha (50%): Últimas Ventas en Caja --}}
            <div class="vc-panel flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-500">receipt_long</span>
                            <span x-text="$store.i18n.t('dashboard.recentSales') || 'Últimas Ventas'">Últimas Ventas</span>
                        </h2>
                        <p class="text-xs text-zinc-400 mt-0.5" x-text="$store.i18n.t('dashboard.recentSalesSubtitle') || 'Transacciones recientes en caja'">
                            Transacciones recientes en caja
                        </p>
                    </div>
                    <a href="{{ route('caja.index') }}" wire:navigate class="px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 border border-emerald-200/50 dark:border-emerald-800/40 transition-all flex items-center gap-1">
                        <span x-text="$store.i18n.t('btn.viewAll') || 'Ver todas'">Ver todas</span>
                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </a>
                </div>

                <div class="space-y-3 overflow-y-auto max-h-64 pr-1">
                    @forelse($ultimasVentas as $venta)
                        <div class="p-3 rounded-xl border border-zinc-100 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-800/30 flex items-center justify-between gap-3 hover:border-emerald-500/30 transition-all">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0 font-bold text-xs">
                                    S/
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $venta->cliente->nombre_completo ?? 'Cliente General' }}
                                    </h4>
                                    <p class="text-[11px] text-zinc-400">
                                        {{ $venta->created_at->format('d/m/Y H:i') }} • {{ $venta->numero_comprobante ?? ('V-' . $venta->id) }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 font-display">
                                    S/ {{ number_format($venta->total, 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-zinc-400 text-xs">
                            <span class="material-symbols-outlined text-3xl mb-1 text-zinc-300 dark:text-zinc-600">receipt</span>
                            <p x-text="$store.i18n.t('dashboard.noSalesPeriod') || 'Sin ventas registradas en este período'">Sin ventas registradas en este período</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ═══ Fila 3 (2 en línea): Inteligencia Clínica y Predicción IA ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Enfermedades Frecuentes y Predicción de Fármacos --}}
            <div class="vc-panel flex flex-col justify-between">
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-500">psychology</span>
                            <span x-text="$store.i18n.t('dashboard.topDiseasesTitle') || 'Enfermedades Frecuentes & Predicción'">Enfermedades Frecuentes & Predicción</span>
                        </h2>
                        <span class="badge badge-emerald text-[10px]" x-text="$store.i18n.t('dashboard.aiPredictive') || 'IA Predictiva'">IA Predictiva</span>
                    </div>
                    <p class="text-[11px] text-zinc-400 mt-0.5" x-text="$store.i18n.t('dashboard.topDiseasesSubtitle') || 'Proyección de demanda de fármacos e insumos para abastecimiento preventivo'">
                        Proyección de demanda de fármacos e insumos para abastecimiento preventivo
                    </p>
                </div>

                <div class="space-y-3 overflow-y-auto max-h-96 pr-1">
                    @forelse($enfermedadesTop as $enf)
                        <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-800/30 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[15px] text-indigo-500">stethoscope</span>
                                    <span class="truncate">{{ $enf['nombre'] }}</span>
                                </h4>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($enf['tendencia'] === 'ALZA')
                                        <span class="badge badge-red text-[10px] font-bold flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[12px]">trending_up</span>
                                            <span x-text="$store.i18n.t('dashboard.trendUp') || 'ALZA'">ALZA</span>
                                        </span>
                                    @elseif($enf['tendencia'] === 'BAJA')
                                        <span class="badge badge-blue text-[10px] font-bold flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[12px]">trending_down</span>
                                            <span x-text="$store.i18n.t('dashboard.trendDown') || 'BAJA'">BAJA</span>
                                        </span>
                                    @else
                                        <span class="badge badge-gray text-[10px] font-bold flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[12px]">trending_flat</span>
                                            <span x-text="$store.i18n.t('dashboard.trendStable') || 'ESTABLE'">ESTABLE</span>
                                        </span>
                                    @endif

                                    <div class="px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200/50 dark:border-indigo-500/20 text-indigo-700 dark:text-indigo-300 text-[11px] font-bold">
                                        <span x-text="'~' + {{ $enf['proyeccion'] }} + ' ' + ($store.i18n.t('dashboard.projNextMonth') || 'casos prox. mes')">~{{ $enf['proyeccion'] }} casos prox. mes</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Insumos / Fármacos sugeridos para compras --}}
                            <div class="p-2 rounded-lg bg-white/70 dark:bg-zinc-900/60 border border-zinc-200/50 dark:border-zinc-800 text-[11px] text-zinc-600 dark:text-zinc-300 flex items-start gap-1.5">
                                <span class="material-symbols-outlined text-[14px] text-emerald-500 shrink-0 mt-0.5">medication</span>
                                <div>
                                    <span class="font-bold text-zinc-800 dark:text-zinc-200" x-text="($store.i18n.t('dashboard.suggestedSupplies') || 'Fármacos & Insumos Sugeridos') + ': '">Fármacos & Insumos: </span>
                                    <span>{{ $enf['medicamentos'] }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-zinc-400 text-xs">
                            <span class="material-symbols-outlined text-3xl mb-1 text-zinc-300 dark:text-zinc-600">health_and_safety</span>
                            <p x-text="$store.i18n.t('dashboard.noDiagnosesRecorded') || 'No hay diagnósticos registrados aún.'">No hay diagnósticos registrados aún.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Síntomas Más Frecuentes y Equipamiento Sugerido --}}
            <div class="vc-panel flex flex-col justify-between">
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-teal-500">ecg_heart</span>
                            <span x-text="$store.i18n.t('dashboard.topSymptomsTitle') || 'Síntomas Recurrentes & Equipamiento'">Síntomas Recurrentes & Equipamiento</span>
                        </h2>
                        <span class="badge badge-blue text-[10px]" x-text="$store.i18n.t('dashboard.diagnostics') || 'Diagnóstico'">Diagnóstico</span>
                    </div>
                    <p class="text-[11px] text-zinc-400 mt-0.5" x-text="$store.i18n.t('dashboard.topSymptomsSubtitle') || 'Kits de diagnóstico, pruebas laboratoriales y equipos médicos sugeridos'">
                        Kits de diagnóstico, pruebas laboratoriales y equipos médicos sugeridos
                    </p>
                </div>

                <div class="space-y-3 overflow-y-auto max-h-96 pr-1">
                    @forelse($sintomasTop as $sintoma)
                        <div class="p-3.5 rounded-xl border border-zinc-100 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-800/30 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[15px] text-teal-500">healing</span>
                                    <span class="truncate">{{ $sintoma['nombre'] }}</span>
                                </h4>
                                <span class="badge badge-gray text-[10px] font-bold shrink-0">
                                    <span x-text="{{ $sintoma['total'] }} + ' ' + ($store.i18n.t('dashboard.recurrences') || 'recurrencias')">{{ $sintoma['total'] }} recurrencias</span>
                                </span>
                            </div>

                            <div class="p-2 rounded-lg bg-white/70 dark:bg-zinc-900/60 border border-zinc-200/50 dark:border-zinc-800 text-[11px] text-zinc-600 dark:text-zinc-300 flex items-start gap-1.5">
                                <span class="material-symbols-outlined text-[14px] text-teal-500 shrink-0 mt-0.5">science</span>
                                <div>
                                    <span class="font-bold text-zinc-800 dark:text-zinc-200" x-text="($store.i18n.t('dashboard.suggestedEquipment') || 'Equipos & Pruebas Sugeridas') + ': '">Equipos & Pruebas: </span>
                                    <span>{{ $sintoma['insumos'] }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-zinc-400 text-xs">
                            <span class="material-symbols-outlined text-3xl mb-1 text-zinc-300 dark:text-zinc-600">medical_services</span>
                            <p x-text="$store.i18n.t('dashboard.noSymptomsRecorded') || 'No hay síntomas registrados aún.'">No hay síntomas registrados aún.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ═══ Fila 4: Alertas e Indicadores Rápidos ═══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Alertas de Stock Mínimo --}}
            <div class="vc-panel flex items-center gap-4 shadow-sm">
                <div class="kpi-icon kpi-icon--amber">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <div class="min-w-0">
                    <p class="text-lg font-extrabold font-display text-zinc-900 dark:text-zinc-100">{{ $alertasInventario }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate" x-text="$store.i18n.t('dashboard.stockAlerts') || 'Stock en Alerta'">Stock en Alerta</p>
                </div>
            </div>

            {{-- Lotes Próximos a Vencer --}}
            <div class="vc-panel flex items-center gap-4 shadow-sm">
                <div class="kpi-icon kpi-icon--red">
                    <span class="material-symbols-outlined">event_busy</span>
                </div>
                <div class="min-w-0">
                    <p class="text-lg font-extrabold font-display text-zinc-900 dark:text-zinc-100">{{ $lotesProximosVencer->count() }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate" x-text="$store.i18n.t('dashboard.expiringBatches') || 'Lotes por Vencer'">Lotes por Vencer</p>
                </div>
            </div>

            {{-- Tipo de Cambio --}}
            <div class="vc-panel flex items-center gap-4 shadow-sm">
                <div class="kpi-icon kpi-icon--emerald">
                    <span class="material-symbols-outlined">currency_exchange</span>
                </div>
                <div class="min-w-0">
                    <p class="text-lg font-extrabold font-display text-zinc-900 dark:text-zinc-100">S/ {{ number_format($tipoCambio, 2) }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate" x-text="$store.i18n.t('label.exchangeRate') || 'Tipo de Cambio (USD/PEN)'">Tipo de Cambio</p>
                </div>
            </div>

            {{-- Reloj Clínico en Tiempo Real --}}
            <div class="vc-panel flex items-center gap-4 shadow-sm"
                 x-data="{ 
                     hora: '', 
                     fecha: '',
                     init() {
                         const update = () => {
                             const now = new Date();
                             this.hora = now.toLocaleTimeString('es-PE', { hour12: false });
                             this.fecha = now.toLocaleDateString(Alpine.store('i18n')?.locale === 'en' ? 'en-US' : 'es-PE', { weekday: 'short', day: 'numeric', month: 'short' });
                         };
                         update();
                         setInterval(update, 1000);
                     }
                 }">
                <div class="kpi-icon kpi-icon--blue">
                    <span class="material-symbols-outlined">schedule</span>
                </div>
                <div class="min-w-0">
                    <p class="text-lg font-extrabold font-display text-zinc-900 dark:text-zinc-100" x-text="hora">--:--:--</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate capitalize" x-text="fecha">--/--</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function dashboardCharts(initialData) {
        return {
            atencionesChartInstance: null,
            ingresosChartInstance: null,
            cachedAtencionesRaw: initialData?.atenciones || null,
            cachedIngresosRaw: initialData?.ingresos || null,

            init() {
                const initCharts = () => {
                    if (typeof Chart !== 'undefined') {
                        this.initAtenciones();
                        this.initIngresos();
                    } else {
                        setTimeout(initCharts, 50);
                    }
                };
                this.$nextTick(initCharts);

                window.addEventListener('language-changed', () => {
                    this.refreshChartTranslations();
                });

                window.addEventListener('dashboard-charts-updated', (e) => {
                    const data = Array.isArray(e.detail) ? e.detail[0] : (e.detail?.detail ? (Array.isArray(e.detail.detail) ? e.detail.detail[0] : e.detail.detail) : e.detail);
                    if (data && data.atenciones) {
                        this.updateAtencionesData(data.atenciones);
                    }
                    if (data && data.ingresos) {
                        this.updateIngresosData(data.ingresos);
                    }
                });

                this.$watch('$wire.atencionesGrafico', (newVal) => {
                    if (newVal && Object.keys(newVal).length > 0) this.updateAtencionesData(newVal);
                });

                this.$watch('$wire.ingresosSemana', (newVal) => {
                    if (newVal && Array.isArray(newVal) && newVal.length > 0) this.updateIngresosData(newVal);
                });
            },

            getDict(key) {
                const store = Alpine.store('i18n');
                const isEn = (store?.locale || localStorage.getItem('vc_locale')) === 'en';
                const fallbackMap = {
                    'dashboard.sun': isEn ? 'Sun' : 'Dom',
                    'dashboard.mon': isEn ? 'Mon' : 'Lun',
                    'dashboard.tue': isEn ? 'Tue' : 'Mar',
                    'dashboard.wed': isEn ? 'Wed' : 'Mié',
                    'dashboard.thu': isEn ? 'Thu' : 'Jue',
                    'dashboard.fri': isEn ? 'Fri' : 'Vie',
                    'dashboard.sat': isEn ? 'Sat' : 'Sáb',
                    'dashboard.week1': isEn ? 'Week 1' : 'Semana 1',
                    'dashboard.week2': isEn ? 'Week 2' : 'Semana 2',
                    'dashboard.week3': isEn ? 'Week 3' : 'Semana 3',
                    'dashboard.week4': isEn ? 'Week 4' : 'Semana 4',
                    'dashboard.week5': isEn ? 'Week 5' : 'Semana 5',
                    'dashboard.jan': isEn ? 'Jan' : 'Ene',
                    'dashboard.feb': isEn ? 'Feb' : 'Feb',
                    'dashboard.mar': isEn ? 'Mar' : 'Mar',
                    'dashboard.apr': isEn ? 'Apr' : 'Abr',
                    'dashboard.may': isEn ? 'May' : 'May',
                    'dashboard.jun': isEn ? 'Jun' : 'Jun',
                    'dashboard.jul': isEn ? 'Jul' : 'Jul',
                    'dashboard.aug': isEn ? 'Aug' : 'Ago',
                    'dashboard.sep': isEn ? 'Sep' : 'Sep',
                    'dashboard.oct': isEn ? 'Oct' : 'Oct',
                    'dashboard.nov': isEn ? 'Nov' : 'Nov',
                    'dashboard.dec': isEn ? 'Dec' : 'Dic',
                    'status.completed': isEn ? 'Completed' : 'Realizadas',
                    'status.pending': isEn ? 'Pending' : 'Por Realizar',
                    'dashboard.incomeSoles': isEn ? 'Income (S/)' : 'Ingresos (S/)'
                };
                const val = store?.t(key);
                if (val && val !== key) return val;
                return fallbackMap[key] || '';
            },

            formatAtencionesLabels(raw) {
                if (!raw) return [];
                const keys = raw.keys || [];
                const labels = raw.labels || [];
                return labels.map((label, idx) => {
                    const key = keys[idx];
                    if (key) {
                        const translated = this.getDict(key);
                        if (translated) {
                            const parts = String(label).split(' ');
                            if (parts.length > 1 && parts[1].includes('/')) {
                                return `${translated} ${parts[1]}`;
                            }
                            return translated;
                        }
                    }
                    return label;
                });
            },

            formatIngresosLabels(raw) {
                if (!raw || !Array.isArray(raw)) return [];
                return raw.map(item => {
                    if (item.key) {
                        const translated = this.getDict(item.key);
                        if (translated) {
                            const isMonth = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'].some(m => item.key.includes(m));
                            if (item.date && !isMonth) {
                                return `${translated} ${item.date}`;
                            }
                            return translated;
                        }
                    }
                    return item.dia || item.date || '';
                });
            },

            refreshChartTranslations() {
                if (this.atencionesChartInstance && this.cachedAtencionesRaw) {
                    this.atencionesChartInstance.data.labels = this.formatAtencionesLabels(this.cachedAtencionesRaw);
                    this.atencionesChartInstance.data.datasets[0].label = this.getDict('status.completed') || 'Realizadas';
                    this.atencionesChartInstance.data.datasets[1].label = this.getDict('status.pending') || 'Por Realizar';
                    this.atencionesChartInstance.update();
                }
                if (this.ingresosChartInstance && this.cachedIngresosRaw) {
                    this.ingresosChartInstance.data.labels = this.formatIngresosLabels(this.cachedIngresosRaw);
                    this.ingresosChartInstance.data.datasets[0].label = this.getDict('dashboard.incomeSoles') || 'Ingresos (S/)';
                    this.ingresosChartInstance.update();
                }
            },

            updateAtencionesData(data) {
                this.cachedAtencionesRaw = JSON.parse(JSON.stringify(data));
                if (!this.atencionesChartInstance) {
                    this.initAtenciones();
                    return;
                }
                this.atencionesChartInstance.data.labels = this.formatAtencionesLabels(this.cachedAtencionesRaw);
                this.atencionesChartInstance.data.datasets[0].data = this.cachedAtencionesRaw.realizadas || [];
                this.atencionesChartInstance.data.datasets[1].data = this.cachedAtencionesRaw.pendientes || [];
                this.atencionesChartInstance.data.datasets[0].label = this.getDict('status.completed') || 'Realizadas';
                this.atencionesChartInstance.data.datasets[1].label = this.getDict('status.pending') || 'Por Realizar';
                this.atencionesChartInstance.update();
            },

            updateIngresosData(data) {
                this.cachedIngresosRaw = JSON.parse(JSON.stringify(data));
                if (!this.ingresosChartInstance) {
                    this.initIngresos();
                    return;
                }
                this.ingresosChartInstance.data.labels = this.formatIngresosLabels(this.cachedIngresosRaw);
                this.ingresosChartInstance.data.datasets[0].data = this.cachedIngresosRaw.map(d => d.total) || [];
                this.ingresosChartInstance.data.datasets[0].label = this.getDict('dashboard.incomeSoles') || 'Ingresos (S/)';
                this.ingresosChartInstance.update();
            },

            initAtenciones() {
                const ctx = document.getElementById('atencionesChart');
                if (!ctx) return;
                if (this.atencionesChartInstance) this.atencionesChartInstance.destroy();

                if (!this.cachedAtencionesRaw) {
                    this.cachedAtencionesRaw = JSON.parse(JSON.stringify(this.$wire.atencionesGrafico || { labels: [], keys: [], realizadas: [], pendientes: [] }));
                }

                this.atencionesChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.formatAtencionesLabels(this.cachedAtencionesRaw),
                        datasets: [
                            {
                                label: this.getDict('status.completed') || 'Realizadas',
                                data: this.cachedAtencionesRaw.realizadas || [],
                                backgroundColor: '#10b981',
                                borderRadius: 6,
                            },
                            {
                                label: this.getDict('status.pending') || 'Por Realizar',
                                data: this.cachedAtencionesRaw.pendientes || [],
                                backgroundColor: '#3b82f6',
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { font: { family: 'Plus Jakarta Sans', weight: '600', size: 11 }, color: '#71717a' }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 10 }, color: '#71717a' } },
                            y: { beginAtZero: true, grid: { color: 'rgba(113, 113, 122, 0.08)' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 10 }, color: '#71717a' } }
                        }
                    }
                });
            },

            initIngresos() {
                const ctx = document.getElementById('ingresosChart');
                if (!ctx) return;
                if (this.ingresosChartInstance) this.ingresosChartInstance.destroy();

                if (!this.cachedIngresosRaw) {
                    this.cachedIngresosRaw = JSON.parse(JSON.stringify(this.$wire.ingresosSemana || []));
                }

                this.ingresosChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.formatIngresosLabels(this.cachedIngresosRaw),
                        datasets: [{
                            label: this.getDict('dashboard.incomeSoles') || 'Ingresos (S/)',
                            data: this.cachedIngresosRaw.map(d => d.total) || [],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#10b981',
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: { label: (c) => ' S/ ' + Number(c.raw).toFixed(2) }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 10 }, color: '#71717a' } },
                            y: { beginAtZero: true, grid: { color: 'rgba(113, 113, 122, 0.08)' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 10 }, color: '#71717a' } }
                        }
                    }
                });
            }
        };
    }
</script>