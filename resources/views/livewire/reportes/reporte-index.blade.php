<div x-data>
    <x-slot:title x-text="$store.i18n.t('page.reports') || 'Reportes'">Reportes</x-slot:title>

    <div class="animate-slide-up">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="kpi-icon kpi-icon--purple">
                    <span class="material-symbols-outlined">analytics</span>
                </div>
                <div>
                    <flux:heading size="xl" class="font-extrabold text-zinc-900 dark:text-zinc-100"><span x-text="$store.i18n.t('page.reportsTitle') || 'Reportes y Estadísticas'">Reportes y Estadísticas</span></flux:heading>
                    <flux:subheading><span x-text="$store.i18n.t('page.reportsSub') || 'Visualiza el rendimiento y métricas clave de la clínica.'">Visualiza el rendimiento y métricas clave de la clínica.</span></flux:subheading>
                </div>
            </div>
            <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-3">
                <div class="min-w-[200px]">
                    <x-vc-dropdown 
                        wire:model.live="periodo"
                        :options="[
                            ['value' => 'hoy', 'label' => 'report.today'],
                            ['value' => 'semana_actual', 'label' => 'report.thisWeek'],
                            ['value' => 'mes_actual', 'label' => 'report.thisMonth'],
                            ['value' => 'año_actual', 'label' => 'report.thisYear']
                        ]"
                        :selected="$periodo"
                        placeholder="filter.period"
                    />
                </div>
                <button type="button" wire:click="exportarPdf" class="btn-primary bg-zinc-700 hover:bg-zinc-800 border-zinc-700 print:hidden flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined icon-sm">picture_as_pdf</span>
                    <span x-text="$store.i18n.t('report.downloadPDF') || 'Descargar PDF'">Descargar PDF</span>
                </button>
                <button type="button" wire:click="exportarCsv" class="btn-primary bg-emerald-600 hover:bg-emerald-700 border-emerald-600 print:hidden flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined icon-sm">table_view</span>
                    <span x-text="$store.i18n.t('report.exportCSV') || 'Exportar Datos (CSV)'">Exportar Datos (CSV)</span>
                </button>
            </div>
        </div>

        <style>
            @media print {
                @page { size: A4; margin: 20mm; }
                body { background: white !important; }
                .print\:hidden, nav, header, aside { display: none !important; }
                /* Adjust grid for print */
                .grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 {
                    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                }
                .grid-cols-1.md\:grid-cols-2 {
                    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                }
                .kpi-card { border: 1px solid #e4e4e7 !important; break-inside: avoid; }
                .vc-panel { border: 1px solid #e4e4e7 !important; break-inside: avoid; }
            }
        </style>

        <div class="space-y-8">
            @php
                $textoPeriodo = match($periodo) {
                    'hoy' => 'de Hoy',
                    'semana_actual' => 'de la Semana',
                    'mes_actual' => 'del Mes',
                    'año_actual' => 'del Año',
                    default => 'del Periodo',
                };
            @endphp
            {{-- Sección Ventas --}}
            <section>
                <h3 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-100 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-vc-primary">point_of_sale</span>
                    <span x-text="$store.i18n.t('report.sales') || 'Ventas y Finanzas'">Ventas y Finanzas</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="kpi-card kpi-card--emerald">
                        <div class="flex justify-between items-start mb-4">
                            <div class="kpi-icon kpi-icon--emerald">
                                <span class="material-symbols-outlined">payments</span>
                            </div>
                            @if(isset($porcentajeVentas) && $ventasPeriodo > 0 || (isset($porcentajeVentas) && $porcentajeVentas != 0))
                                <div class="flex items-center gap-1 {{ $porcentajeVentas >= 0 ? 'text-emerald-600 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-400' : 'text-red-600 bg-red-100 dark:bg-red-500/20 dark:text-red-400' }} px-2 py-1 rounded-full text-xs font-bold">
                                    <span class="material-symbols-outlined text-[14px]">{{ $porcentajeVentas >= 0 ? 'trending_up' : 'trending_down' }}</span>
                                    {{ number_format(abs($porcentajeVentas), 1) }}%
                                </div>
                            @endif
                        </div>
                        <h3 class="text-3xl font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight font-display mb-1">
                            S/ {{ number_format($ventasPeriodo, 2) }}
                        </h3>
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider">
                            <span x-text="$store.i18n.t('report.income') || 'Ingresos'">Ingresos</span> 
                            <span x-text="$store.i18n.t('report.period_{{ $periodo }}') || '{{ $textoPeriodo }}'">{{ $textoPeriodo }}</span>
                        </p>
                    </div>

                    <div class="kpi-card kpi-card--blue">
                        <div class="flex justify-between items-start mb-4">
                            <div class="kpi-icon kpi-icon--blue">
                                <span class="material-symbols-outlined">receipt_long</span>
                            </div>
                            @if(isset($porcentajeTicket) && $ticketPromedio > 0 || (isset($porcentajeTicket) && $porcentajeTicket != 0))
                                <div class="flex items-center gap-1 {{ $porcentajeTicket >= 0 ? 'text-emerald-600 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-400' : 'text-red-600 bg-red-100 dark:bg-red-500/20 dark:text-red-400' }} px-2 py-1 rounded-full text-xs font-bold">
                                    <span class="material-symbols-outlined text-[14px]">{{ $porcentajeTicket >= 0 ? 'trending_up' : 'trending_down' }}</span>
                                    {{ number_format(abs($porcentajeTicket), 1) }}%
                                </div>
                            @endif
                        </div>
                        <h3 class="text-3xl font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight font-display mb-1">
                            S/ {{ number_format($ticketPromedio, 2) }}
                        </h3>
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider">
                            <span x-text="$store.i18n.t('report.averageTicket') || 'Ticket Promedio'">Ticket Promedio</span> 
                            <span x-text="$store.i18n.t('report.period_{{ $periodo }}') || '{{ $textoPeriodo }}'">{{ $textoPeriodo }}</span>
                        </p>
                    </div>
                </div>
            </section>

            {{-- Sección Citas --}}
            <section>
                <h3 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-100 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-500">calendar_month</span>
                    <span x-text="$store.i18n.t('report.appointmentsPerf') || 'Rendimiento de Citas'">Rendimiento de Citas</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="kpi-card kpi-card--purple">
                        <div class="flex justify-between items-start mb-4">
                            <div class="kpi-icon kpi-icon--purple">
                                <span class="material-symbols-outlined">event_available</span>
                            </div>
                            @if(isset($porcentajeCitas) && $citasCompletadas > 0 || (isset($porcentajeCitas) && $porcentajeCitas != 0))
                                <div class="flex items-center gap-1 {{ $porcentajeCitas >= 0 ? 'text-emerald-600 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-400' : 'text-red-600 bg-red-100 dark:bg-red-500/20 dark:text-red-400' }} px-2 py-1 rounded-full text-xs font-bold">
                                    <span class="material-symbols-outlined text-[14px]">{{ $porcentajeCitas >= 0 ? 'trending_up' : 'trending_down' }}</span>
                                    {{ number_format(abs($porcentajeCitas), 1) }}%
                                </div>
                            @endif
                        </div>
                        <h3 class="text-3xl font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight font-display mb-1">
                            {{ $citasCompletadas }}
                        </h3>
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider">
                            <span x-text="$store.i18n.t('report.completedAppts') || 'Citas Completadas'">Citas Completadas</span> 
                            <span x-text="$store.i18n.t('report.period_{{ $periodo }}') || '{{ $textoPeriodo }}'">{{ $textoPeriodo }}</span>
                        </p>
                    </div>

                    <div class="kpi-card kpi-card--red">
                        <div class="flex justify-between items-start mb-4">
                            <div class="kpi-icon kpi-icon--red">
                                <span class="material-symbols-outlined">event_busy</span>
                            </div>
                        </div>
                        <h3 class="text-3xl font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight font-display mb-1">
                            {{ $citasCanceladas }}
                        </h3>
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider">
                            <span x-text="$store.i18n.t('report.cancelledAppts') || 'Citas Canceladas'">Citas Canceladas</span> 
                            <span x-text="$store.i18n.t('report.period_{{ $periodo }}') || '{{ $textoPeriodo }}'">{{ $textoPeriodo }}</span>
                        </p>
                    </div>
                    
                    <div class="kpi-card kpi-card--blue">
                        <div class="flex justify-between items-start mb-4">
                            <div class="kpi-icon kpi-icon--blue">
                                <span class="material-symbols-outlined">person_add</span>
                            </div>
                            @if(isset($porcentajeNuevas) && $citasNuevas > 0 || (isset($porcentajeNuevas) && $porcentajeNuevas != 0))
                                <div class="flex items-center gap-1 {{ $porcentajeNuevas >= 0 ? 'text-emerald-600 bg-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-400' : 'text-red-600 bg-red-100 dark:bg-red-500/20 dark:text-red-400' }} px-2 py-1 rounded-full text-xs font-bold">
                                    <span class="material-symbols-outlined text-[14px]">{{ $porcentajeNuevas >= 0 ? 'trending_up' : 'trending_down' }}</span>
                                    {{ number_format(abs($porcentajeNuevas), 1) }}%
                                </div>
                            @endif
                        </div>
                        <h3 class="text-3xl font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight font-display mb-1">
                            {{ $citasNuevas }}
                        </h3>
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider">
                            <span x-text="$store.i18n.t('report.newReservations') || 'Nuevas Reservas'">Nuevas Reservas</span> 
                            <span x-text="$store.i18n.t('report.period_{{ $periodo }}') || '{{ $textoPeriodo }}'">{{ $textoPeriodo }}</span>
                        </p>
                    </div>
                </div>
            </section>

            {{-- Sección Inventario --}}
            <section>
                <h3 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-100 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">inventory_2</span>
                    <span x-text="$store.i18n.t('report.inventoryStatus') || 'Estado de Inventario'">Estado de Inventario</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="kpi-card kpi-card--amber">
                        <div class="flex justify-between items-start mb-4">
                            <div class="kpi-icon kpi-icon--amber">
                                <span class="material-symbols-outlined">warning</span>
                            </div>
                        </div>
                        <h3 class="text-3xl font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight font-display mb-1">
                            {{ $productosStockBajo }}
                        </h3>
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.lowStock') || 'Productos con Stock Bajo'">Productos con Stock Bajo</p>
                    </div>
                </div>
            </section>
            
            {{-- Gráficos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8" 
                 x-data="reportCharts({
                    ventasLabels: @js($ventasChartLabels),
                    ventasData: @js($ventasChartData),
                    citasLabels: @js($citasChartLabels),
                    citasData: @js($citasChartData)
                 })"
                 @charts-updated.window="updateCharts($event.detail)"
                 wire:ignore
            >
                <div class="vc-panel">
                    <h3 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-500">trending_up</span>
                        <span x-text="$store.i18n.t('report.salesEvol') || 'Evolución de Ventas'">Evolución de Ventas</span>
                    </h3>
                    <div class="relative h-72 w-full">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
                <div class="vc-panel">
                    <h3 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-purple-500">pie_chart</span>
                        <span x-text="$store.i18n.t('report.apptsStatus') || 'Estado de Citas'">Estado de Citas</span>
                    </h3>
                    <div class="relative h-72 w-full flex justify-center">
                        <canvas id="citasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@script
<script>
    Alpine.data('reportCharts', (initialData) => ({
        ventasChart: null,
        citasChart: null,
        
        init() {
            this.initVentasChart(initialData.ventasLabels, initialData.ventasData);
            this.initCitasChart(initialData.citasLabels, initialData.citasData);

            // Listen for livewire updates
            $wire.on('charts-updated', (payload) => {
                const data = Array.isArray(payload) ? payload[0] : payload;
                this.updateVentasChart(data.ventasLabels, data.ventasData);
                this.updateCitasChart(data.citasLabels, data.citasData);
            });
        },
        
        initVentasChart(labels, data) {
            const ctx = document.getElementById('ventasChart');
            if(!ctx) return;
            
            this.ventasChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: Alpine.store('i18n').t('report.salesEvol') || 'Ventas (S/)',
                        data: data,
                        borderColor: '#10b981', // emerald-500
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        },
        
        initCitasChart(labels, data) {
            labels = labels.map(l => {
                if(l === 'Completadas') return Alpine.store('i18n').t('report.completed') || l;
                if(l === 'Canceladas') return Alpine.store('i18n').t('report.cancelled') || l;
                if(l === 'Pendientes/Otras') return Alpine.store('i18n').t('report.pendingOther') || l;
                return l;
            });
            const ctx = document.getElementById('citasChart');
            if(!ctx) return;

            this.citasChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            '#10b981', // Completadas: emerald-500
                            '#ef4444', // Canceladas: red-500
                            '#f59e0b'  // Otras: amber-500
                        ],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        },

        updateVentasChart(labels, data) {
            if(this.ventasChart) {
                this.ventasChart.data.labels = labels;
                this.ventasChart.data.datasets[0].data = data;
                this.ventasChart.update();
            }
        },

        updateCitasChart(labels, data) {
            labels = labels.map(l => {
                if(l === 'Completadas') return Alpine.store('i18n').t('report.completed') || l;
                if(l === 'Canceladas') return Alpine.store('i18n').t('report.cancelled') || l;
                if(l === 'Pendientes/Otras') return Alpine.store('i18n').t('report.pendingOther') || l;
                return l;
            });
            if(this.citasChart) {
                this.citasChart.data.labels = labels;
                this.citasChart.data.datasets[0].data = data;
                this.citasChart.update();
            }
        }
    }));
</script>
@endscript
