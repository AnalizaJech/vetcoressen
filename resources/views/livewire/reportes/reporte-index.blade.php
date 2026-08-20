<div>
    <x-slot:title>Reportes y Estadísticas</x-slot:title>

    <div class="space-y-6" 
         x-data="advancedReportCharts({
             ventasLabels: {{ json_encode($ventasChartLabels ?? []) }},
             ventasData: {{ json_encode($ventasChartData ?? []) }},
             citasData: [{{ $citasCompletadas }}, {{ $citasCanceladas }}, {{ $citasPendientes }}],
             topProductosLabels: {{ json_encode($topProductosLabels ?? []) }},
             topProductosData: {{ json_encode($topProductosData ?? []) }},
             pagosLabels: {{ json_encode($pagosChartLabels ?? []) }},
             pagosData: {{ json_encode($pagosChartData ?? []) }}
         })"
         x-init="init()"
         @report-charts-updated.window="handleChartsUpdated($event.detail)"
    >
        {{-- ═══ Header de Reportes y Estadísticas ═══ --}}
        <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 border border-purple-200/50 dark:border-purple-500/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <span class="material-symbols-outlined text-2xl">analytics</span>
                    </div>
                    <div>
                        <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                            <span x-text="$store.i18n.t('report.title') || 'Reportes y Estadísticas'">Reportes y Estadísticas</span>
                        </h1>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('report.subtitle') || 'Visualiza el rendimiento clínico y métricas clave'">
                            Visualiza el rendimiento clínico y métricas clave
                        </p>
                    </div>
                </div>
            </div>

            {{-- Acciones de Exportación --}}
            <div class="flex items-center gap-2.5">
                <a :href="'/reports/export/pdf?periodo=' + $wire.periodo + '&fecha_inicio=' + $wire.fecha_inicio + '&fecha_fin=' + $wire.fecha_fin" target="_blank" class="btn-primary bg-zinc-800 hover:bg-zinc-900 text-white border-zinc-800 text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined icon-sm">picture_as_pdf</span>
                    <span x-text="$store.i18n.t('btn.downloadPDF') || 'Descargar PDF'">Descargar PDF</span>
                </a>
                <a :href="'/reports/export/excel?periodo=' + $wire.periodo + '&fecha_inicio=' + $wire.fecha_inicio + '&fecha_fin=' + $wire.fecha_fin" target="_blank" class="btn-primary bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600 text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined icon-sm">table_view</span>
                    <span x-text="$store.i18n.t('btn.exportCSV') || 'Exportar CSV'">Exportar CSV</span>
                </a>
            </div>
        </div>

        {{-- ═══ Barra de Filtros Dinámicos Interactivos ═══ --}}
        <div class="vc-panel">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                {{-- Selector de Periodo --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('filter.period') || 'Periodo'">Periodo</label>
                    <x-vc-dropdown 
                        wire:model.live="periodo"
                        :options="[
                            ['value' => 'hoy', 'label' => 'report.today'],
                            ['value' => 'semana_actual', 'label' => 'report.thisWeek'],
                            ['value' => 'mes_actual', 'label' => 'report.thisMonth'],
                            ['value' => 'anio_actual', 'label' => 'report.thisYear'],
                            ['value' => 'personalizado', 'label' => 'report.customRange']
                        ]"
                        :selected="$periodo"
                        placeholder="filter.period"
                    />
                </div>

                {{-- Fecha Inicio (con date picker personalizado) --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('report.startDate') || 'Fecha Inicio'">Fecha Inicio</label>
                    <x-vc-date-picker 
                        wire:model.live="fecha_inicio" 
                        placeholder="report.startDate"
                    />
                </div>

                {{-- Fecha Fin (con date picker personalizado) --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('report.endDate') || 'Fecha Fin'">Fecha Fin</label>
                    <x-vc-date-picker 
                        wire:model.live="fecha_fin" 
                        placeholder="report.endDate"
                    />
                </div>

                {{-- Filtro por Categoría / Tipo de Producto --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('report.category') || 'Categoría'">Categoría</label>
                    <x-vc-dropdown 
                        wire:model.live="categoria"
                        :options="[
                            ['value' => '', 'label' => 'report.allCategories'],
                            ['value' => 'PRODUCTO', 'label' => 'inventory.product'],
                            ['value' => 'SERVICIO', 'label' => 'inventory.service'],
                            ['value' => 'MEDICAMENTO', 'label' => 'inventory.medication'],
                            ['value' => 'ALIMENTO', 'label' => 'inventory.food'],
                            ['value' => 'ACCESORIO', 'label' => 'inventory.accessory'],
                        ]"
                        :selected="$categoria"
                        placeholder="report.allCategories"
                    />
                </div>
            </div>
        </div>

        @php
            $textoPeriodo = match($periodo) {
                'hoy' => 'de Hoy',
                'semana_actual' => 'de la Semana',
                'mes_actual' => 'del Mes',
                'anio_actual' => 'del Año',
                default => 'del Periodo',
            };
        @endphp

        {{-- ═══ KPIs Principales ═══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Ingresos Totales --}}
            <div class="kpi-card kpi-card--emerald shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                        <span x-text="$store.i18n.t('report.income') || 'Ingresos'">Ingresos</span> 
                        <span x-text="$store.i18n.t('report.period_{{ $periodo }}') || '{{ $textoPeriodo }}'">{{ $textoPeriodo }}</span>
                    </span>
                    <div class="kpi-icon kpi-icon--emerald">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    S/ {{ number_format($ventasPeriodo, 2) }}
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    @if($porcentajeVentas != 0)
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-xs font-bold {{ $porcentajeVentas >= 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300' }}">
                            <span class="material-symbols-outlined text-[13px]">{{ $porcentajeVentas >= 0 ? 'trending_up' : 'trending_down' }}</span>
                            {{ number_format(abs($porcentajeVentas), 1) }}%
                        </span>
                    @endif
                    <span class="text-[11px] text-zinc-400">
                        <span x-text="{{ $totalVentasCount }} + ' ' + ($store.i18n.t('report.totalSalesCount') || 'ventas concretadas')">{{ $totalVentasCount }} ventas</span>
                    </span>
                </div>
            </div>

            {{-- Ticket Promedio --}}
            <div class="kpi-card kpi-card--blue shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.averageTicket') || 'Ticket Promedio'">Ticket Promedio</span>
                    <div class="kpi-icon kpi-icon--blue">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    S/ {{ number_format($ticketPromedio, 2) }}
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    @if($porcentajeTicket != 0)
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-xs font-bold {{ $porcentajeTicket >= 0 ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' }}">
                            <span class="material-symbols-outlined text-[13px]">{{ $porcentajeTicket >= 0 ? 'trending_up' : 'trending_down' }}</span>
                            {{ number_format(abs($porcentajeTicket), 1) }}%
                        </span>
                    @endif
                    <span class="text-[11px] text-zinc-400" x-text="$store.i18n.t('report.avgPerReceipt') || 'promedio por comprobante'">promedio por comprobante</span>
                </div>
            </div>

            {{-- Citas Atendidas --}}
            <div class="kpi-card kpi-card--purple shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.completedAppointments') || 'Citas Completadas'">Citas Completadas</span>
                    <div class="kpi-icon kpi-icon--purple">
                        <span class="material-symbols-outlined">event_available</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    {{ $citasCompletadas }}
                </h3>
                <p class="text-[11px] text-zinc-400 mt-2">
                    <span x-text="($store.i18n.t('report.ofTotal') || 'De un total de') + ' ' + {{ $totalCitas }} + ' ' + ($store.i18n.t('report.scheduledAppointmentsText') || 'citas programadas')">De un total de {{ $totalCitas }} citas</span>
                </p>
            </div>

            {{-- Valorización del Inventario --}}
            <div class="kpi-card kpi-card--amber shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.inventoryValuation') || 'Valorización Inventario'">Valorización Inventario</span>
                    <div class="kpi-icon kpi-icon--amber">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    S/ {{ number_format($valorizacionInventario, 2) }}
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="badge {{ $productosStockBajo > 0 ? 'badge-amber' : 'badge-emerald' }} text-[10px]">
                        <span x-text="{{ $productosStockBajo }} + ' ' + ($store.i18n.t('report.inAlert') || 'en alerta')">{{ $productosStockBajo }} en alerta</span>
                    </span>
                    <span class="text-[11px] text-zinc-400">
                        <span x-text="{{ $lotesProximosVencerCount }} + ' ' + ($store.i18n.t('report.batchesExpiring') || 'lotes x vencer')">{{ $lotesProximosVencerCount }} lotes x vencer</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- ═══ 4 Gráficos Interactivos (2 en línea) ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Gráfico 1: Evolución de Ventas --}}
            <div class="vc-panel">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-500">trending_up</span>
                        <span x-text="$store.i18n.t('report.salesEvolution') || 'Evolución de Ventas'">Evolución de Ventas</span>
                    </h3>
                    <span class="text-xs text-zinc-400" x-text="$store.i18n.t('report.incomeTimeline') || 'Ingresos en el tiempo'">Ingresos en el tiempo</span>
                </div>
                <div class="relative h-64 w-full" wire:ignore>
                    <canvas id="repVentasChart"></canvas>
                </div>
            </div>

            {{-- Gráfico 2: Distribución de Citas --}}
            <div class="vc-panel">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-purple-500">pie_chart</span>
                        <span x-text="$store.i18n.t('report.appointmentsStatus') || 'Estado de Citas'">Estado de Citas</span>
                    </h3>
                    <span class="text-xs text-zinc-400" x-text="$store.i18n.t('report.appointmentsDistribution') || 'Distribución operativa'">Distribución operativa</span>
                </div>
                <div class="relative h-64 w-full" wire:ignore>
                    <canvas id="repCitasChart"></canvas>
                </div>
            </div>

            {{-- Gráfico 3: Top 5 Productos / Servicios más Vendidos --}}
            <div class="vc-panel">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-500">bar_chart</span>
                        <span x-text="$store.i18n.t('report.topSoldItems') || 'Top 5 Productos / Servicios Más Vendidos'">Top 5 Productos / Servicios</span>
                    </h3>
                    <span class="text-xs text-zinc-400" x-text="$store.i18n.t('report.rankedByRevenue') || 'Por facturación'">Por facturación</span>
                </div>
                <div class="relative h-64 w-full" wire:ignore>
                    <canvas id="repTopProdChart"></canvas>
                </div>
            </div>

            {{-- Gráfico 4: Métodos de Pago --}}
            <div class="vc-panel">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500">credit_card</span>
                        <span x-text="$store.i18n.t('report.paymentMethods') || 'Métodos de Pago'">Métodos de Pago</span>
                    </h3>
                    <span class="text-xs text-zinc-400" x-text="$store.i18n.t('report.distributionByChannel') || 'Distribución por canal'">Distribución por canal</span>
                </div>
                <div class="relative h-64 w-full" wire:ignore>
                    <canvas id="repPagosChart"></canvas>
                </div>
            </div>
        </div>

        {{-- ═══ Tabla Detallada de Transacciones ═══ --}}
        <div class="vc-panel">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-500">receipt_long</span>
                        <span x-text="$store.i18n.t('report.transactionsDetail') || 'Detalle de Transacciones del Periodo'">Detalle de Transacciones</span>
                    </h3>
                    <p class="text-xs text-zinc-400" x-text="$store.i18n.t('report.transactionsSubtitle') || 'Registro cronológico de operaciones'">Registro cronológico de operaciones</p>
                </div>
            </div>

            <div class="overflow-x-auto max-h-[360px] overflow-y-auto vc-custom-scroll pr-1">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-100 dark:border-zinc-800 text-zinc-400 uppercase font-semibold">
                            <th class="pb-3" x-text="$store.i18n.t('table.saleNumber') || '# Venta'"># Venta</th>
                            <th class="pb-3" x-text="$store.i18n.t('table.client') || 'Cliente'">Cliente</th>
                            <th class="pb-3" x-text="$store.i18n.t('table.dateTime') || 'Fecha / Hora'">Fecha / Hora</th>
                            <th class="pb-3" x-text="$store.i18n.t('table.paymentMethod') || 'Método'">Método</th>
                            <th class="pb-3 text-right" x-text="$store.i18n.t('table.total') || 'Total'">Total</th>
                            <th class="pb-3 text-center" x-text="$store.i18n.t('table.status') || 'Estado'">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                        @forelse($sales as $v)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="py-3 font-mono font-medium text-zinc-700 dark:text-zinc-300">
                                    {{ $v->numero_comprobante ?? ('V-' . $v->id) }}
                                </td>
                                <td class="py-3 font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $v->cliente->nombre_completo ?? 'Cliente General' }}
                                </td>
                                <td class="py-3 text-zinc-500">
                                    {{ $v->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 text-zinc-600 dark:text-zinc-400">
                                    {{ $v->metodo_pago ?? 'Efectivo' }}
                                </td>
                                <td class="py-3 text-right font-extrabold text-emerald-600 dark:text-emerald-400 font-display">
                                    S/ {{ number_format($v->total, 2) }}
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge {{ $v->status === 'PAGADO' ? 'badge-emerald' : 'badge-red' }} text-[10px]">
                                        <span x-text="$store.i18n.t('status.{{ strtolower($v->status) }}') || '{{ $v->status }}'">{{ $v->status }}</span>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-zinc-400" x-text="$store.i18n.t('report.noData') || 'No hay ventas en este período.'">No hay ventas en este período.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function advancedReportCharts(initialData) {
        return {
            c1: null,
            c2: null,
            c3: null,
            c4: null,
            lastData: initialData || {},

            init() {
                const checkChartAndInit = () => {
                    if (typeof Chart !== 'undefined') {
                        this.initAllCharts(this.lastData);
                    } else {
                        setTimeout(checkChartAndInit, 50);
                    }
                };
                this.$nextTick(checkChartAndInit);

                window.addEventListener('language-changed', () => {
                    this.updateTranslations();
                });
            },

            handleChartsUpdated(payload) {
                const data = Array.isArray(payload) ? payload[0] : payload;
                if (data) {
                    this.lastData = data;
                    this.initAllCharts(data);
                }
            },

            updateTranslations() {
                if (this.c1) {
                    this.c1.data.datasets[0].label = Alpine.store('i18n')?.t('report.salesEvol') || 'Ingresos (S/)';
                    this.c1.update();
                }
                if (this.c2) {
                    this.c2.data.labels = [
                        Alpine.store('i18n')?.t('status.completed') || 'Completadas',
                        Alpine.store('i18n')?.t('status.cancelled') || 'Canceladas',
                        Alpine.store('i18n')?.t('status.pending') || 'Pendientes'
                    ];
                    this.c2.update();
                }
                if (this.c3) {
                    this.c3.data.datasets[0].label = Alpine.store('i18n')?.t('report.totalGenerated') || 'Total (S/)';
                    this.c3.update();
                }
                if (this.c4) {
                    this.c4.update();
                }
            },

            initAllCharts(data) {
                if (!data) return;
                this.initVentas(data);
                this.initCitas(data);
                this.initTopProductos(data);
                this.initPagos(data);
            },

            initVentas(data) {
                const ctx = document.getElementById('repVentasChart');
                if (!ctx) return;
                if (this.c1) this.c1.destroy();

                this.c1 = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.ventasLabels || [],
                        datasets: [{
                            label: Alpine.store('i18n')?.t('report.salesEvol') || 'Ingresos (S/)',
                            data: data.ventasData || [],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
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
                                callbacks: {
                                    label: (c) => ' S/ ' + Number(c.raw).toFixed(2)
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 10 }, color: '#71717a' } },
                            y: { beginAtZero: true, grid: { color: 'rgba(113, 113, 122, 0.08)' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 10 }, color: '#71717a' } }
                        }
                    }
                });
            },

            initCitas(data) {
                const ctx = document.getElementById('repCitasChart');
                if (!ctx) return;
                if (this.c2) this.c2.destroy();

                this.c2 = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            Alpine.store('i18n')?.t('status.completed') || 'Completadas',
                            Alpine.store('i18n')?.t('status.cancelled') || 'Canceladas',
                            Alpine.store('i18n')?.t('status.pending') || 'Pendientes'
                        ],
                        datasets: [{
                            data: data.citasData || [0, 0, 0],
                            backgroundColor: ['#10b981', '#ef4444', '#8b5cf6'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#71717a' } }
                        },
                        cutout: '70%'
                    }
                });
            },

            initTopProductos(data) {
                const ctx = document.getElementById('repTopProdChart');
                if (!ctx) return;
                if (this.c3) this.c3.destroy();

                this.c3 = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: (data.topProductosLabels || []).map(l => l.length > 20 ? l.substring(0, 20) + '...' : l),
                        datasets: [{
                            label: Alpine.store('i18n')?.t('report.totalGenerated') || 'Total (S/)',
                            data: data.topProductosData || [],
                            backgroundColor: '#3b82f6',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (c) => ' S/ ' + Number(c.raw).toFixed(2)
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 10 }, color: '#71717a' } },
                            y: { beginAtZero: true, grid: { color: 'rgba(113, 113, 122, 0.08)' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 10 }, color: '#71717a' } }
                        }
                    }
                });
            },

            initPagos(data) {
                const ctx = document.getElementById('repPagosChart');
                if (!ctx) return;
                if (this.c4) this.c4.destroy();

                this.c4 = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.pagosLabels || ['Efectivo', 'Tarjeta', 'Yape / Plin', 'Transferencia'],
                        datasets: [{
                            data: data.pagosData || [0, 0, 0, 0],
                            backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#71717a' } },
                            tooltip: {
                                callbacks: {
                                    label: (c) => ' S/ ' + Number(c.raw).toFixed(2)
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        };
    }
</script>
