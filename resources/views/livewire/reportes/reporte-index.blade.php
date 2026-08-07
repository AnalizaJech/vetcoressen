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
            <div class="w-full sm:w-auto min-w-[200px]">
                <x-vc-dropdown 
                    wire:model.live="periodo"
                    :options="[
                        ['value' => 'hoy', 'label' => 'filter.today'],
                        ['value' => 'semana_actual', 'label' => 'filter.thisWeek'],
                        ['value' => 'mes_actual', 'label' => 'filter.thisMonth'],
                        ['value' => 'año_actual', 'label' => 'filter.thisYear']
                    ]"
                    :selected="$periodo"
                    placeholder="filter.period"
                />
            </div>
        </div>

        <div class="space-y-8">
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
                        </div>
                        <h3 class="text-3xl font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight font-display mb-1">
                            S/ {{ number_format($ventasPeriodo, 2) }}
                        </h3>
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.periodIncome') || 'Ingresos del Periodo'">Ingresos del Periodo</p>
                    </div>

                    <div class="kpi-card kpi-card--blue">
                        <div class="flex justify-between items-start mb-4">
                            <div class="kpi-icon kpi-icon--blue">
                                <span class="material-symbols-outlined">receipt_long</span>
                            </div>
                        </div>
                        <h3 class="text-3xl font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight font-display mb-1">
                            S/ {{ number_format($ticketPromedio, 2) }}
                        </h3>
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.averageTicket') || 'Ticket Promedio'">Ticket Promedio</p>
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
                        </div>
                        <h3 class="text-3xl font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight font-display mb-1">
                            {{ $citasCompletadas }}
                        </h3>
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.completedAppts') || 'Citas Completadas'">Citas Completadas</p>
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
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.cancelledAppts') || 'Citas Canceladas'">Citas Canceladas</p>
                    </div>
                    
                    <div class="kpi-card kpi-card--blue">
                        <div class="flex justify-between items-start mb-4">
                            <div class="kpi-icon kpi-icon--blue">
                                <span class="material-symbols-outlined">person_add</span>
                            </div>
                        </div>
                        <h3 class="text-3xl font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight font-display mb-1">
                            {{ $citasNuevas }}
                        </h3>
                        <p class="text-sm font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('report.newBookings') || 'Nuevas Reservas'">Nuevas Reservas</p>
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
            
            <div class="vc-panel mt-8">
                <div class="text-center py-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-zinc-100 dark:bg-vc-surface-alt/50 mb-4">
                        <span class="material-symbols-outlined text-3xl text-zinc-400">bar_chart</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-100" x-text="$store.i18n.t('report.advCharts') || 'Gráficos Avanzados'">Gráficos Avanzados</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 max-w-md mx-auto mt-2" x-text="$store.i18n.t('report.advChartsSub') || 'Próximamente se integrarán gráficos interactivos para un mejor análisis de la evolución de las ventas y citas en el tiempo.'">
                        Próximamente se integrarán gráficos interactivos para un mejor análisis de la evolución de las ventas y citas en el tiempo.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
