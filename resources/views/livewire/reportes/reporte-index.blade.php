<script>
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.interaction.mode = 'index';
        Chart.defaults.interaction.intersect = false;
        Chart.defaults.plugins.tooltip.enabled = true;
    }

    window.vetReportCharts = window.vetReportCharts || {
        c1: null,
        c2: null,
        c3: null,
        c4: null
    };

    window.formatReportChartLabel = function(l, isEn) {
        if (!l) return '';
        const str = String(l).trim();
        
        const dayMap = {
            'lun': isEn ? 'Mon' : 'Lun',
            'mar': isEn ? 'Tue' : 'Mar',
            'mié': isEn ? 'Wed' : 'Mié',
            'mie': isEn ? 'Wed' : 'Mié',
            'jue': isEn ? 'Thu' : 'Jue',
            'vie': isEn ? 'Fri' : 'Vie',
            'sáb': isEn ? 'Sat' : 'Sáb',
            'sab': isEn ? 'Sat' : 'Sáb',
            'dom': isEn ? 'Sun' : 'Dom',
            'mon': isEn ? 'Mon' : 'Lun',
            'tue': isEn ? 'Tue' : 'Mar',
            'wed': isEn ? 'Wed' : 'Mié',
            'thu': isEn ? 'Thu' : 'Jue',
            'fri': isEn ? 'Fri' : 'Vie',
            'sat': isEn ? 'Sat' : 'Sáb',
            'sun': isEn ? 'Sun' : 'Dom',
            'lunes': isEn ? 'Monday' : 'Lunes',
            'martes': isEn ? 'Tuesday' : 'Martes',
            'miércoles': isEn ? 'Wednesday' : 'Miércoles',
            'miercoles': isEn ? 'Wednesday' : 'Miércoles',
            'jueves': isEn ? 'Thursday' : 'Jueves',
            'viernes': isEn ? 'Friday' : 'Viernes',
            'sábado': isEn ? 'Saturday' : 'Sábado',
            'sabado': isEn ? 'Saturday' : 'Sábado',
            'domingo': isEn ? 'Sunday' : 'Domingo',
            'monday': isEn ? 'Monday' : 'Lunes',
            'tuesday': isEn ? 'Tuesday' : 'Martes',
            'wednesday': isEn ? 'Wednesday' : 'Miércoles',
            'thursday': isEn ? 'Thursday' : 'Jueves',
            'friday': isEn ? 'Friday' : 'Viernes',
            'saturday': isEn ? 'Saturday' : 'Sábado',
            'sunday': isEn ? 'Sunday' : 'Domingo'
        };

        const monthMap = {
            'ene': isEn ? 'Jan' : 'Ene',
            'feb': isEn ? 'Feb' : 'Feb',
            'mar': isEn ? 'Mar' : 'Mar',
            'abr': isEn ? 'Apr' : 'Abr',
            'may': isEn ? 'May' : 'May',
            'jun': isEn ? 'Jun' : 'Jun',
            'jul': isEn ? 'Jul' : 'Jul',
            'ago': isEn ? 'Aug' : 'Ago',
            'sep': isEn ? 'Sep' : 'Sep',
            'set': isEn ? 'Sep' : 'Set',
            'oct': isEn ? 'Oct' : 'Oct',
            'nov': isEn ? 'Nov' : 'Nov',
            'dic': isEn ? 'Dec' : 'Dic',
            'jan': isEn ? 'Jan' : 'Ene',
            'apr': isEn ? 'Apr' : 'Abr',
            'aug': isEn ? 'Aug' : 'Ago',
            'dec': isEn ? 'Dec' : 'Dic',
            'enero': isEn ? 'January' : 'Enero',
            'febrero': isEn ? 'February' : 'Febrero',
            'marzo': isEn ? 'March' : 'Marzo',
            'abril': isEn ? 'April' : 'Abril',
            'mayo': isEn ? 'May' : 'Mayo',
            'junio': isEn ? 'June' : 'Junio',
            'julio': isEn ? 'July' : 'Julio',
            'agosto': isEn ? 'August' : 'Agosto',
            'septiembre': isEn ? 'September' : 'Septiembre',
            'setiembre': isEn ? 'September' : 'Setiembre',
            'octubre': isEn ? 'October' : 'Octubre',
            'noviembre': isEn ? 'November' : 'Noviembre',
            'diciembre': isEn ? 'December' : 'Diciembre',
            'january': isEn ? 'January' : 'Enero',
            'february': isEn ? 'February' : 'Febrero',
            'march': isEn ? 'March' : 'Marzo',
            'april': isEn ? 'April' : 'Abril',
            'june': isEn ? 'June' : 'Junio',
            'july': isEn ? 'July' : 'Julio',
            'august': isEn ? 'August' : 'Agosto',
            'september': isEn ? 'September' : 'Septiembre',
            'october': isEn ? 'October' : 'Octubre',
            'november': isEn ? 'November' : 'Noviembre',
            'december': isEn ? 'December' : 'Diciembre'
        };

        const parts = str.split(' ');
        if (parts.length === 2 && parts[1].includes('/')) {
            const prefix = parts[0].toLowerCase().replace('.', '');
            if (dayMap[prefix]) {
                return `${dayMap[prefix]} ${parts[1]}`;
            }
        }

        const lower = str.toLowerCase().replace('.', '');
        if (monthMap[lower]) return monthMap[lower];
        if (dayMap[lower]) return dayMap[lower];
        
        if (str.toLowerCase().startsWith('week ') || str.toLowerCase().startsWith('semana ')) {
            const num = str.match(/\d+/);
            const suffix = str.includes('(') ? ' ' + str.substring(str.indexOf('(')) : '';
            return (isEn ? 'Week ' : 'Semana ') + (num ? num[0] : '') + suffix;
        }

        if (str.includes('-')) {
            const p = str.split('-');
            if (p.length === 3) return p[2] + '/' + p[1];
        }

        return str;
    };

    window.renderAllReportCharts = function(data) {
        if (typeof Chart === 'undefined' || !data) return;
        const isEn = (window.Alpine?.store('i18n')?.locale || localStorage.getItem('vc_locale')) === 'en';

        // 1. Ventas
        const canvas1 = document.getElementById('repVentasChart');
        if (canvas1) {
            if (window.vetReportCharts.c1) {
                try { window.vetReportCharts.c1.destroy(); } catch(e) {}
                window.vetReportCharts.c1 = null;
            }
            const existing1 = Chart.getChart(canvas1);
            if (existing1) existing1.destroy();

            const ctx1 = canvas1.getContext('2d');
            const rawLabels = data.ventasLabels || [];
            const formattedLabels = rawLabels.map(l => window.formatReportChartLabel(l, isEn));
            const labelText = window.Alpine?.store('i18n')?.t('report.revenue') || (isEn ? 'Revenue (S/)' : 'Ingresos (S/)');

            window.vetReportCharts.c1 = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: formattedLabels,
                    datasets: [{
                        label: labelText,
                        data: data.ventasData || [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#10b981',
                        pointHoverBackgroundColor: '#10b981',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                        hitRadius: 25,
                        pointHitRadius: 25,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    hover: { mode: 'index', intersect: false },
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
                            callbacks: {
                                title: function(items) {
                                    if (!items || !items.length) return '';
                                    return items[0].label || '';
                                },
                                label: function(c) {
                                    const val = c.raw !== undefined ? c.raw : (c.parsed?.y !== undefined ? c.parsed.y : 0);
                                    return ' ' + labelText + ': S/ ' + Number(val).toFixed(2);
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

        // 2. Citas
        const canvas2 = document.getElementById('repCitasChart');
        if (canvas2) {
            if (window.vetReportCharts.c2) {
                try { window.vetReportCharts.c2.destroy(); } catch(e) {}
                window.vetReportCharts.c2 = null;
            }
            const existing2 = Chart.getChart(canvas2);
            if (existing2) existing2.destroy();

            const ctx2 = canvas2.getContext('2d');
            window.vetReportCharts.c2 = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: [
                        window.Alpine?.store('i18n')?.t('status.completed') || (isEn ? 'Completed' : 'Completadas'),
                        window.Alpine?.store('i18n')?.t('status.cancelled') || (isEn ? 'Cancelled' : 'Canceladas'),
                        window.Alpine?.store('i18n')?.t('status.pending') || (isEn ? 'Pending' : 'Pendientes')
                    ],
                    datasets: [{
                        data: data.citasData || [0, 0, 0],
                        backgroundColor: ['#10b981', '#ef4444', '#8b5cf6'],
                        borderWidth: 2,
                        borderColor: 'transparent'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: { font: { family: 'Plus Jakarta Sans', weight: '600', size: 11 }, color: '#71717a' }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(24, 24, 27, 0.95)',
                            titleColor: '#ffffff',
                            bodyColor: '#e4e4e7',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(c) {
                                    const val = c.raw !== undefined ? c.raw : (c.parsed !== undefined ? c.parsed : 0);
                                    const apptText = window.Alpine?.store('i18n')?.t('sidebar.appointments') || (isEn ? 'appointments' : 'citas');
                                    return ' ' + (c.label || '') + ': ' + val + ' ' + apptText;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // 3. Top Productos
        const canvas3 = document.getElementById('repTopProdChart');
        if (canvas3) {
            if (window.vetReportCharts.c3) {
                try { window.vetReportCharts.c3.destroy(); } catch(e) {}
                window.vetReportCharts.c3 = null;
            }
            const existing3 = Chart.getChart(canvas3);
            if (existing3) existing3.destroy();

            const ctx3 = canvas3.getContext('2d');
            window.vetReportCharts.c3 = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: (data.topProductosLabels || []).map(l => l.length > 22 ? l.substring(0, 22) + '...' : l),
                    datasets: [{
                        label: window.Alpine?.store('i18n')?.t('report.revenue') || (isEn ? 'Revenue (S/)' : 'Total (S/)'),
                        data: data.topProductosData || [],
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(24, 24, 27, 0.95)',
                            titleColor: '#ffffff',
                            bodyColor: '#e4e4e7',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(c) {
                                    const val = c.raw !== undefined ? c.raw : (c.parsed?.y !== undefined ? c.parsed.y : 0);
                                    const totalText = window.Alpine?.store('i18n')?.t('report.revenue') || (isEn ? 'Revenue' : 'Total');
                                    return ' ' + totalText + ': S/ ' + Number(val).toFixed(2);
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

        // 4. Métodos de Pago
        const canvas4 = document.getElementById('repPagosChart');
        if (canvas4) {
            if (window.vetReportCharts.c4) {
                try { window.vetReportCharts.c4.destroy(); } catch(e) {}
                window.vetReportCharts.c4 = null;
            }
            const existing4 = Chart.getChart(canvas4);
            if (existing4) existing4.destroy();

            const ctx4 = canvas4.getContext('2d');
            const paymentLabels = isEn 
                ? ['Cash', 'Card', 'Yape / Plin', 'Bank Transfer']
                : ['Efectivo', 'Tarjeta', 'Yape / Plin', 'Transferencia'];

            window.vetReportCharts.c4 = new Chart(ctx4, {
                type: 'doughnut',
                data: {
                    labels: paymentLabels,
                    datasets: [{
                        data: data.pagosData || [0, 0, 0, 0],
                        backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'],
                        borderWidth: 2,
                        borderColor: 'transparent'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: { font: { family: 'Plus Jakarta Sans', weight: '600', size: 11 }, color: '#71717a' }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(24, 24, 27, 0.95)',
                            titleColor: '#ffffff',
                            bodyColor: '#e4e4e7',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(c) {
                                    const val = c.raw !== undefined ? c.raw : (c.parsed !== undefined ? c.parsed : 0);
                                    return ' ' + (c.label || '') + ': S/ ' + Number(val).toFixed(2);
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    };

    window.advancedReportCharts = function(initialData) {
        return {
            cachedData: initialData || {},

            init() {
                const checkAndRender = () => {
                    if (typeof Chart !== 'undefined') {
                        window.renderAllReportCharts(this.cachedData);
                    } else {
                        setTimeout(checkAndRender, 50);
                    }
                };
                this.$nextTick(checkAndRender);

                window.addEventListener('language-changed', () => {
                    requestAnimationFrame(() => {
                        if (this.cachedData) window.renderAllReportCharts(this.cachedData);
                    });
                });

                window.addEventListener('report-charts-updated', (e) => {
                    const detail = Array.isArray(e.detail) ? e.detail[0] : (e.detail || {});
                    if (detail) {
                        this.updateCharts(detail);
                    }
                });

                window.addEventListener('charts-updated', (e) => {
                    const detail = Array.isArray(e.detail) ? e.detail[0] : (e.detail || {});
                    if (detail) {
                        this.updateCharts(detail);
                    }
                });
            },

            downloadReport(type) {
                // Acceder a propiedades Livewire directamente (no usar .get() que es async)
                const p = this.$wire.periodo || 'mes_actual';
                const fi = this.$wire.fecha_inicio || '';
                const ff = this.$wire.fecha_fin || '';
                const lang = Alpine.store('i18n')?.locale || localStorage.getItem('vc_locale') || 'en';
                const q = new URLSearchParams({
                    periodo: p,
                    fecha_inicio: fi,
                    fecha_fin: ff,
                    lang: lang
                }).toString();
                window.open(`/reports/export/${type}?${q}`, '_blank');
            },

            updateCharts(data) {
                if (!data) return;
                this.cachedData = data;
                window.renderAllReportCharts(data);
            }
        };
    };

    if (window.Alpine) {
        window.Alpine.data('advancedReportCharts', window.advancedReportCharts);
    }
</script>

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
         @report-charts-updated.window="const d = Array.isArray($event.detail) ? $event.detail[0] : ($event.detail || {}); if (d) updateCharts(d)"
         @charts-updated.window="const d = Array.isArray($event.detail) ? $event.detail[0] : ($event.detail || {}); if (d) updateCharts(d)"
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
                            <span x-text="$store.i18n.t('report.title') || 'Reports & Statistics'">Reports & Statistics</span>
                        </h1>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('report.subtitle') || 'View clinical performance and key metrics'">
                            View clinical performance and key metrics
                        </p>
                    </div>
                </div>
            </div>

            {{-- Acciones de Exportación Directa --}}
            <div class="flex items-center gap-2.5">
                <button 
                    type="button"
                    @click="downloadReport('pdf')" 
                    class="btn-primary bg-zinc-800 hover:bg-zinc-900 text-white border-zinc-800 text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm cursor-pointer"
                >
                    <span class="material-symbols-outlined icon-sm">picture_as_pdf</span>
                    <span x-text="$store.i18n.t('btn.downloadPDF') || 'Download PDF'">Download PDF</span>
                </button>
                <button 
                    type="button"
                    @click="downloadReport('excel')" 
                    class="btn-primary bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600 text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm cursor-pointer"
                >
                    <span class="material-symbols-outlined icon-sm">table_view</span>
                    <span x-text="$store.i18n.t('btn.downloadExcel') || 'Download Excel'">Download Excel</span>
                </button>
            </div>
        </div>

        {{-- ═══ Barra de Filtros Dinámicos Interactivos ═══ --}}
        <div class="vc-panel relative z-30">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                {{-- Selector de Periodo --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('filter.period') || 'Period'">Period</label>
                    <x-vc-dropdown 
                        wire:model.live="periodo"
                        :options="[
                            ['value' => 'hoy', 'label' => 'report.today'],
                            ['value' => 'semana_actual', 'label' => 'report.thisWeek'],
                            ['value' => 'mes_actual', 'label' => 'report.thisMonth'],
                            ['value' => 'anio_actual', 'label' => 'report.thisYear'],
                            ['value' => 'personalizado', 'label' => 'report.custom']
                        ]"
                        :selected="$periodo"
                        placeholder="filter.period"
                        icon="calendar_month"
                    />
                </div>

                {{-- Fecha Inicio --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('filter.startDate') || 'Start Date'">Start Date</label>
                    <x-vc-date-picker 
                        wire:model.live="fecha_inicio" 
                        :disabled="$periodo !== 'personalizado'"
                        placeholder="filter.startDate"
                    />
                </div>

                {{-- Fecha Fin --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('filter.endDate') || 'End Date'">End Date</label>
                    <x-vc-date-picker 
                        wire:model.live="fecha_fin" 
                        :disabled="$periodo !== 'personalizado'"
                        align="right"
                        placeholder="filter.endDate"
                    />
                </div>
            </div>
        </div>

        {{-- ═══ 4 KPI Cards Ejecutivos ═══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Ventas del Periodo --}}
            <div class="kpi-card kpi-card--emerald shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.totalRevenue') || 'Total Revenue'">Total Revenue</span>
                    <div class="kpi-icon kpi-icon--emerald">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    {{ $simboloMoneda }} {{ number_format($ventasPeriodo, 2) }}
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="badge badge-emerald text-[10px]">
                        {{ $totalVentasCount }} <span x-text="$store.i18n.t('report.sales') || 'sales'">sales</span>
                    </span>
                    <span class="text-[11px] text-zinc-400">
                        {{ $startDate->format('d/m') }} - {{ $endDate->format('d/m/Y') }}
                    </span>
                </div>
            </div>

            {{-- Ticket Promedio --}}
            <div class="kpi-card kpi-card--blue shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.averageTicket') || 'Average Ticket'">Average Ticket</span>
                    <div class="kpi-icon kpi-icon--blue">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    {{ $simboloMoneda }} {{ number_format($ticketPromedio, 2) }}
                </h3>
                <p class="text-[11px] text-zinc-400 mt-2">
                    <span x-text="$store.i18n.t('report.perTransaction') || 'Per transaction'">Per transaction</span>
                </p>
            </div>

            {{-- Citas Completadas --}}
            <div class="kpi-card kpi-card--purple shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.completedAppointments') || 'Completed Appointments'">Completed Appointments</span>
                    <div class="kpi-icon kpi-icon--purple">
                        <span class="material-symbols-outlined">event_available</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    {{ $citasCompletadas }}
                </h3>
                <p class="text-[11px] text-zinc-400 mt-2">
                    <span x-text="($store.i18n.locale === 'en' ? 'Out of ' : 'De un total de ') + ' ' + {{ $totalCitas }} + ' ' + ($store.i18n.t('sidebar.appointments') || 'appointments')">Total {{ $totalCitas }} appointments</span>
                </p>
            </div>

            {{-- Valorización del Inventario --}}
            <div class="kpi-card kpi-card--amber shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.lowStockProducts') || 'Low Stock Products'">Low Stock Products</span>
                    <div class="kpi-icon kpi-icon--amber">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight font-display mb-1">
                    {{ $productosStockBajo }}
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="badge {{ $productosStockBajo > 0 ? 'badge-amber' : 'badge-emerald' }} text-[10px]">
                        <span x-text="$store.i18n.t('report.requireRestock') || 'Require restock'">Require restock</span>
                    </span>
                    <span class="text-[11px] text-zinc-400">
                        <span x-text="{{ $lotesProximosVencerCount }} + ' ' + ($store.i18n.locale === 'en' ? 'expiring batches' : 'lotes x vencer')">{{ $lotesProximosVencerCount }} lotes x vencer</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- ═══ 4 Gráficos Interactivos ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Gráfico 1: Evolución de Ventas --}}
            <div class="vc-panel">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-500">trending_up</span>
                        <span x-text="$store.i18n.t('report.salesEvol') || 'Revenue Evolution'">Revenue Evolution</span>
                    </h3>
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
                        <span x-text="$store.i18n.t('report.appointmentStatus') || 'Appointments Status'">Appointments Status</span>
                    </h3>
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
                        <span x-text="$store.i18n.t('report.topProducts') || 'Top 5 Best-Selling Products & Services'">Top 5 Best-Selling Products & Services</span>
                    </h3>
                </div>
                <div class="relative h-64 w-full" wire:ignore>
                    <canvas id="repTopProdChart"></canvas>
                </div>
            </div>

            {{-- Gráfico 4: Métodos de Pago --}}
            <div class="vc-panel">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500">account_balance_wallet</span>
                        <span x-text="$store.i18n.t('report.paymentMethods') || 'Payment Methods'">Payment Methods</span>
                    </h3>
                    <span class="text-xs text-zinc-400" x-text="$store.i18n.t('report.collectionsDist') || 'Collections distribution'">Collections distribution</span>
                </div>
                <div class="relative h-64 w-full" wire:ignore>
                    <canvas id="repPagosChart"></canvas>
                </div>
            </div>
        </div>

        {{-- ═══ 2 Tablas Detalladas y Compactas (Ventas y Citas) ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Tabla: Últimas Ventas del Periodo --}}
            <div class="vc-panel">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-500">point_of_sale</span>
                        <span x-text="$store.i18n.t('report.periodSales') || 'Period Sales'">Period Sales</span>
                    </h3>
                    <span class="text-xs text-zinc-400 font-medium" x-text="$store.i18n.locale === 'en' ? 'Latest 5 records' : 'Últimos 5 registros'">Latest 5 records</span>
                </div>

                <div class="space-y-2.5 max-h-72 overflow-y-auto vc-custom-scroll pr-1">
                    @forelse($ventasDetalle as $venta)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-700/50 hover:bg-zinc-100/80 dark:hover:bg-zinc-800 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0">
                                    #{{ $venta->id }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $venta->cliente?->nombre_completo ?? 'Cliente General' }}
                                    </p>
                                    <p class="text-[10px] text-zinc-400">
                                        {{ $venta->created_at->format('d/m/Y H:i') }} &bull; {{ $venta->tipo_comprobante }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-extrabold text-zinc-900 dark:text-zinc-100">
                                    {{ $simboloMoneda }} {{ number_format($venta->total, 2) }}
                                </span>
                                <p class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                    {{ $venta->payment_method }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-zinc-400 text-xs">
                            <span class="material-symbols-outlined text-3xl mb-1 text-zinc-300 dark:text-zinc-600">receipt_long</span>
                            <p x-text="$store.i18n.t('report.noSalesInPeriod') || 'No sales in this period'">No sales in this period</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Tabla: Citas del Periodo --}}
            <div class="vc-panel">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-base font-extrabold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="material-symbols-outlined text-purple-500">calendar_month</span>
                        <span x-text="$store.i18n.t('report.periodAppointments') || 'Period Appointments'">Period Appointments</span>
                    </h3>
                    <span class="text-xs text-zinc-400 font-medium" x-text="$store.i18n.locale === 'en' ? 'Latest 5 records' : 'Últimos 5 registros'">Latest 5 records</span>
                </div>

                <div class="space-y-2.5 max-h-72 overflow-y-auto vc-custom-scroll pr-1">
                    @forelse($citasDetalle as $cita)
                        @php
                            $statusBg = match($cita->status) {
                                'COMPLETADA' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                'CANCELADA' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                'CONFIRMADA' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                'EN_PROGRESO' => 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
                                default => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                            };
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-700/50 hover:bg-zinc-100/80 dark:hover:bg-zinc-800 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-lg">pets</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $cita->mascota?->name ?? 'Mascota' }} &bull; <span class="font-normal text-zinc-500">{{ $cita->cliente?->nombre_completo }}</span>
                                    </p>
                                    <p class="text-[10px] text-zinc-400 truncate">
                                        {{ $cita->fecha_hora?->format('d/m/Y H:i') }} &bull; {{ $cita->reason ?? 'Consulta General' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $statusBg }}">
                                    <span x-text="$store.i18n.t('status.' + '{{ strtolower($cita->status) }}') || '{{ $cita->status }}'">{{ $cita->status }}</span>
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-zinc-400 text-xs">
                            <span class="material-symbols-outlined text-3xl mb-1 text-zinc-300 dark:text-zinc-600">event_busy</span>
                            <p x-text="$store.i18n.t('report.noAppointmentsInPeriod') || 'No appointments in this period'">No appointments in this period</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
