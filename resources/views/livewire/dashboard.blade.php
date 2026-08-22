<div>
    <x-slot:title>Dashboard</x-slot:title>

    <div class="space-y-6">
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
                    <span x-text="$store.i18n.t('btn.newAppointment') || 'New Appointment'">New Appointment</span>
                </a>
                <a href="{{ route('caja.index') }}" wire:navigate class="btn-secondary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined icon-sm">point_of_sale</span>
                    <span x-text="$store.i18n.t('sidebar.point_of_sale') || 'Point of Sale'">Point of Sale</span>
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
                        {{ $totalVentasHoy }} <span x-text="$store.i18n.t('dashboard.operationsCompleted') || 'operaciones concretadas'">operaciones concretadas</span>
                    </span>
                </div>
            </div>

            {{-- KPI 2: Atenciones Realizadas vs Citas de Hoy --}}
            <div class="kpi-card kpi-card--blue shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('dashboard.attentionsToday') || 'Atenciones de Hoy'">Atenciones de Hoy</span>
                    <div class="kpi-icon kpi-icon--blue">
                        <span class="material-symbols-outlined">stethoscope</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    {{ $citasCompletadasHoy }} <span class="text-lg text-zinc-400 font-normal">/ {{ $citasHoyCount }}</span>
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[11px] text-zinc-400">
                        {{ $citasPendientes }} <span x-text="$store.i18n.t('dashboard.pendingScheduled') || 'pendientes en agenda'">pendientes en agenda</span>
                    </span>
                </div>
            </div>

            {{-- KPI 3: Mascotas Registradas y Activas --}}
            <div class="kpi-card kpi-card--amber shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('dashboard.patientsActive') || 'Pacientes Activos'">Pacientes Activos</span>
                    <div class="kpi-icon kpi-icon--amber">
                        <span class="material-symbols-outlined">pets</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    {{ $totalMascotas }}
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[11px] text-zinc-400">
                        {{ $totalClientes }} <span x-text="$store.i18n.t('dashboard.registeredOwners') || 'dueños registrados'">dueños registrados</span>
                    </span>
                </div>
            </div>

            {{-- KPI 4: Alertas de Stock y Lotes Críticos --}}
            <div class="kpi-card {{ $alertasInventario > 0 ? 'kpi-card--rose' : 'kpi-card--emerald' }} shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('dashboard.stockAlerts') || 'Alertas de Stock'">Alertas de Stock</span>
                    <div class="kpi-icon {{ $alertasInventario > 0 ? 'kpi-icon--rose' : 'kpi-icon--emerald' }}">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    {{ $alertasInventario }}
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[11px] text-zinc-400">
                        {{ $lotesProximosVencer->count() }} <span x-text="$store.i18n.t('dashboard.batchesNearExpiry') || 'lotes por vencer'">lotes por vencer</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- ═══ Banner Informativo: Estado de Caja (Solo si está cerrada) ═══ --}}
        @if(!$cajaAbierta)
            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200/60 dark:border-amber-500/20 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-xl">lock</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-900 dark:text-amber-200" x-text="$store.i18n.t('dashboard.cashRegisterClosed') || 'Caja del día cerrada'">Caja del día cerrada</h4>
                        <p class="text-[11px] text-amber-700 dark:text-amber-400" x-text="$store.i18n.t('dashboard.cashRegisterClosedDesc') || 'Abre turno en caja para registrar ventas'">Abre turno en caja para registrar ventas</p>
                    </div>
                </div>
                <a href="{{ route('caja.index') }}" wire:navigate class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-colors shrink-0 shadow-xs">
                    <span x-text="$store.i18n.t('dashboard.openRegister') || 'Abrir Caja'">Abrir Caja</span>
                </a>
            </div>
        @endif

        {{-- ═══ Fila 1 (2 en línea): Atenciones Médicas vs Citas Programadas ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Columna Izquierda (50%): Gráfico de Atenciones Médicas --}}
            <div class="vc-panel flex flex-col justify-between"
                 x-data="atencionesChartComponent()"
                 x-init="initChart(@js($atencionesGrafico))"
                 x-effect="updateData(@js($atencionesGrafico))"
            >
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
                            icon="calendar_month"
                        />
                    </div>
                </div>

                <div class="relative h-64 w-full" wire:ignore>
                    <canvas id="atencionesChart"></canvas>
                </div>
            </div>

            {{-- Columna Derecha (50%): Citas Programadas --}}
            <div class="vc-panel flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">calendar_month</span>
                            <span x-text="$store.i18n.t('dashboard.scheduledAppointments') || 'Scheduled Appointments'">Scheduled Appointments</span>
                        </h2>
                        <p class="text-xs text-zinc-400 mt-0.5" x-text="$store.i18n.t('dashboard.scheduledSubtitle') || 'Clinical schedule for the period'">
                            Clinical schedule for the period
                        </p>
                    </div>
                    <a href="{{ route('citas.index') }}" wire:navigate class="px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 border border-emerald-200/50 dark:border-emerald-800/40 transition-all flex items-center gap-1">
                        <span x-text="$store.i18n.t('btn.viewAll') || 'View all'">View all</span>
                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </a>
                </div>

                <div class="space-y-3 overflow-y-auto max-h-64 pr-1 flex-1">
                    @forelse($citasHoy as $cita)
                        <div class="p-3 rounded-xl border border-zinc-100 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-800/30 flex items-center justify-between gap-3 hover:border-emerald-500/30 transition-all">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/20 flex flex-col items-center justify-center text-blue-600 dark:text-blue-400 shrink-0 font-bold">
                                    <span class="text-[10px] leading-tight uppercase">{{ \Carbon\Carbon::parse($cita->fecha_hora)->translatedFormat('M') }}</span>
                                    <span class="text-xs leading-none font-extrabold">{{ \Carbon\Carbon::parse($cita->fecha_hora)->format('d') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $cita->mascota->name ?? 'Pet' }}
                                        <span class="text-zinc-400 font-normal">({{ $cita->cliente->nombre_completo ?? 'Client' }})</span>
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
                        <div class="py-8 text-center text-zinc-400 text-xs">
                            <span class="material-symbols-outlined text-3xl mb-1 text-zinc-300 dark:text-zinc-600">event_available</span>
                            <p x-text="$store.i18n.t('dashboard.noAppointmentsPeriod') || 'No scheduled appointments in this period'">No scheduled appointments in this period</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ═══ Fila 2 (2 en línea): Ingresos Monetarios vs Últimas Ventas ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Columna Izquierda (50%): Gráfico de Ingresos Monetarios --}}
            <div class="vc-panel flex flex-col justify-between"
                 x-data="ingresosChartComponent()"
                 x-init="initChart(@js($ingresosSemana))"
                 x-effect="updateData(@js($ingresosSemana))"
            >
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-500">trending_up</span>
                            <span x-text="$store.i18n.t('dashboard.monetaryIncome') || 'Monetary Income'">Monetary Income</span>
                        </h2>
                        <p class="text-xs text-zinc-400 mt-0.5" x-text="$store.i18n.t('dashboard.incomeSubtitle') || 'Evolution of generated income'">
                            Evolution of generated income
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
                            icon="calendar_month"
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
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.interaction.mode = 'index';
        Chart.defaults.interaction.intersect = false;
        Chart.defaults.plugins.tooltip.enabled = true;
    }

    function getDashboardDict(key) {
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
            'dashboard.incomeSoles': isEn ? 'Revenue (S/)' : 'Ingresos (S/)'
        };
        const val = store?.t(key);
        if (val && val !== key) return val;
        return fallbackMap[key] || '';
    }

    function formatAtencionesLabelsList(raw) {
        if (!raw) return [];
        const keys = raw.keys || [];
        const labels = raw.labels || [];
        return labels.map((label, idx) => {
            const key = keys[idx];
            if (key) {
                const translated = getDashboardDict(key);
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
    }

    function formatIngresosLabelsList(raw) {
        if (!raw || !Array.isArray(raw)) return [];
        return raw.map(item => {
            if (item.key) {
                const translated = getDashboardDict(item.key);
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
    }

    function atencionesChartComponent() {
        return {
            chart: null,
            cachedData: null,

            initChart(initialData) {
                this.cachedData = initialData;
                const waitForChart = () => {
                    const ctx = document.getElementById('atencionesChart');
                    if (typeof Chart !== 'undefined' && ctx) {
                        this.buildChart(ctx, this.cachedData);
                    } else {
                        setTimeout(waitForChart, 50);
                    }
                };
                this.$nextTick(waitForChart);

                window.addEventListener('language-changed', () => {
                    requestAnimationFrame(() => {
                        if (this.chart && this.cachedData) {
                            this.updateData(this.cachedData);
                        }
                    });
                });
            },

            updateData(data) {
                if (!data) return;
                this.cachedData = data;
                const ctx = document.getElementById('atencionesChart');
                if (!ctx) return;
                if (!this.chart) {
                    if (typeof Chart !== 'undefined') this.buildChart(ctx, data);
                    return;
                }

                const labels = formatAtencionesLabelsList(data);
                const compLabel = getDashboardDict('status.completed') || 'Realizadas';
                const pendLabel = getDashboardDict('status.pending') || 'Por Realizar';

                this.chart.data.labels = labels;
                this.chart.data.datasets[0].data = data.realizadas || [];
                this.chart.data.datasets[0].label = compLabel;
                this.chart.data.datasets[1].data = data.pendientes || [];
                this.chart.data.datasets[1].label = pendLabel;
                this.chart.update('none');
            },

            buildChart(canvas, data) {
                if (!canvas) return;
                const existing = Chart.getChart(canvas);
                if (existing) existing.destroy();
                if (this.chart) {
                    try { this.chart.destroy(); } catch(e) {}
                    this.chart = null;
                }
                const ctx = canvas.getContext('2d');
                const labels = formatAtencionesLabelsList(data);
                const compLabel = getDashboardDict('status.completed') || 'Realizadas';
                const pendLabel = getDashboardDict('status.pending') || 'Por Realizar';

                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: compLabel,
                                data: data?.realizadas || [],
                                backgroundColor: '#10b981',
                                borderRadius: 6,
                                maxBarThickness: 40,
                            },
                            {
                                label: pendLabel,
                                data: data?.pendientes || [],
                                backgroundColor: '#3b82f6',
                                borderRadius: 6,
                                maxBarThickness: 40,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
                        interaction: {
                            mode: 'index',
                            intersect: false,
                            axis: 'x'
                        },
                        hover: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { font: { family: 'Plus Jakarta Sans', weight: '600', size: 11 }, color: '#71717a' }
                            },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(24, 24, 27, 0.95)',
                                titleColor: '#ffffff',
                                bodyColor: '#e4e4e7',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                padding: 10,
                                cornerRadius: 8,
                                titleFont: { family: 'Plus Jakarta Sans', weight: '700', size: 12 },
                                bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                                callbacks: {
                                    label: function(context) {
                                        const val = context.raw !== undefined ? context.raw : (context.parsed?.y !== undefined ? context.parsed.y : 0);
                                        return ' ' + (context.dataset.label || '') + ': ' + val;
                                    }
                                }
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

    function ingresosChartComponent() {
        return {
            chart: null,
            cachedData: null,

            initChart(initialData) {
                this.cachedData = initialData;
                const waitForChart = () => {
                    const canvas = document.getElementById('ingresosChart');
                    if (typeof Chart !== 'undefined' && canvas) {
                        this.buildChart(canvas, this.cachedData);
                    } else {
                        setTimeout(waitForChart, 50);
                    }
                };
                this.$nextTick(waitForChart);

                window.addEventListener('language-changed', () => {
                    requestAnimationFrame(() => {
                        if (this.chart && this.cachedData) {
                            this.updateData(this.cachedData);
                        }
                    });
                });
            },

            updateData(data) {
                if (!data) return;
                this.cachedData = data;
                const canvas = document.getElementById('ingresosChart');
                if (!canvas) return;
                if (!this.chart) {
                    if (typeof Chart !== 'undefined') this.buildChart(canvas, data);
                    return;
                }

                const labels = formatIngresosLabelsList(data);
                const incomeLabel = getDashboardDict('dashboard.incomeSoles') || 'Ingresos (S/)';

                this.chart.data.labels = labels;
                this.chart.data.datasets[0].data = data.map(d => d.total) || [];
                this.chart.data.datasets[0].label = incomeLabel;
                this.chart.update('none');
            },

            buildChart(canvas, data) {
                if (!canvas) return;
                const existing = Chart.getChart(canvas);
                if (existing) existing.destroy();
                if (this.chart) {
                    try { this.chart.destroy(); } catch(e) {}
                    this.chart = null;
                }
                const ctx = canvas.getContext('2d');
                const labels = formatIngresosLabelsList(data);
                const incomeLabel = getDashboardDict('dashboard.incomeSoles') || 'Ingresos (S/)';

                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: incomeLabel,
                            data: Array.isArray(data) ? data.map(d => d.total) : [],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#10b981',
                            pointHoverBackgroundColor: '#10b981',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 2,
                            hitRadius: 30,
                            pointHitRadius: 30,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
                        interaction: {
                            mode: 'index',
                            intersect: false,
                            axis: 'x'
                        },
                        hover: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(24, 24, 27, 0.95)',
                                titleColor: '#ffffff',
                                bodyColor: '#e4e4e7',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                padding: 10,
                                cornerRadius: 8,
                                titleFont: { family: 'Plus Jakarta Sans', weight: '700', size: 12 },
                                bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                                callbacks: {
                                    label: function(context) {
                                        const val = context.raw !== undefined ? context.raw : (context.parsed?.y !== undefined ? context.parsed.y : 0);
                                        return ' ' + (context.dataset.label || 'Ingresos') + ': S/ ' + Number(val).toFixed(2);
                                    }
                                }
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
</div>