<?php

namespace App\Livewire\Reportes;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\MedicalRecord;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Reportes y Estadísticas')]
class ReporteIndex extends Component
{
    public string $periodo = 'mes_actual'; // hoy, semana_actual, mes_actual, anio_actual, personalizado
    public string $fecha_inicio = '';
    public string $fecha_fin = '';
    public string $sucursal_id = '';
    public string $categoria = '';

    public function mount(): void
    {
        $this->fecha_inicio = Carbon::today()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = Carbon::today()->format('Y-m-d');
    }

    public function updatedPeriodo(): void
    {
        if ($this->periodo === 'hoy') {
            $this->fecha_inicio = Carbon::today()->format('Y-m-d');
            $this->fecha_fin = Carbon::today()->format('Y-m-d');
        } elseif ($this->periodo === 'semana_actual') {
            $this->fecha_inicio = Carbon::today()->startOfWeek()->format('Y-m-d');
            $this->fecha_fin = Carbon::today()->endOfWeek()->format('Y-m-d');
        } elseif ($this->periodo === 'mes_actual') {
            $this->fecha_inicio = Carbon::today()->startOfMonth()->format('Y-m-d');
            $this->fecha_fin = Carbon::today()->format('Y-m-d');
        } elseif ($this->periodo === 'anio_actual') {
            $this->fecha_inicio = Carbon::today()->startOfYear()->format('Y-m-d');
            $this->fecha_fin = Carbon::today()->format('Y-m-d');
        }

        $this->dispatchChartsUpdate();
    }

    public function updatedFechaInicio(): void
    {
        if ($this->periodo === 'personalizado') {
            $this->dispatchChartsUpdate();
        }
    }

    public function updatedFechaFin(): void
    {
        if ($this->periodo === 'personalizado') {
            $this->dispatchChartsUpdate();
        }
    }

    public function updatedSucursalId(): void
    {
        $this->dispatchChartsUpdate();
    }

    public function updatedCategoria(): void
    {
        $this->dispatchChartsUpdate();
    }

    public function aplicarFiltros(): void
    {
        $this->dispatchChartsUpdate();
    }

    public function limpiarFiltros(): void
    {
        $this->periodo = 'mes_actual';
        $this->fecha_inicio = Carbon::today()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = Carbon::today()->format('Y-m-d');
        $this->sucursal_id = '';
        $this->categoria = '';
        $this->dispatchChartsUpdate();
    }

    public function dispatchChartsUpdate(): void
    {
        $data = $this->getReportData();

        $payload = [
            'ventasLabels'        => $data['ventasChartLabels'],
            'ventasData'          => $data['ventasChartData'],
            'citasLabels'         => $data['citasChartLabels'],
            'citasData'           => $data['citasChartData'],
            'topProductosLabels'  => $data['topProductosLabels'],
            'topProductosData'    => $data['topProductosData'],
            'pagosLabels'         => $data['pagosChartLabels'],
            'pagosData'           => $data['pagosData'] ?? $data['pagosChartData'],
        ];

        $this->dispatch('report-charts-updated', $payload);
        $this->dispatch('charts-updated', $payload);
    }

    private function getReportData(): array
    {
        $now = Carbon::now();

        if ($this->periodo === 'personalizado' && !empty($this->fecha_inicio) && !empty($this->fecha_fin)) {
            $startDate = Carbon::parse($this->fecha_inicio)->startOfDay();
            $endDate = Carbon::parse($this->fecha_fin)->endOfDay();
            $daysDiff = max(1, $startDate->diffInDays($endDate));
            $prevStartDate = $startDate->copy()->subDays($daysDiff)->startOfDay();
            $prevEndDate = $startDate->copy()->subSecond();
        } elseif ($this->periodo === 'hoy') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
            $prevStartDate = Carbon::yesterday()->startOfDay();
            $prevEndDate = Carbon::yesterday()->endOfDay();
        } elseif ($this->periodo === 'semana_actual') {
            $startDate = Carbon::today()->startOfWeek();
            $endDate = Carbon::today()->endOfWeek()->endOfDay();
            $prevStartDate = Carbon::today()->subWeek()->startOfWeek();
            $prevEndDate = Carbon::today()->subWeek()->endOfWeek()->endOfDay();
        } elseif ($this->periodo === 'anio_actual') {
            $startDate = Carbon::today()->startOfYear();
            $endDate = Carbon::today()->endOfYear()->endOfDay();
            $prevStartDate = Carbon::today()->subYear()->startOfYear();
            $prevEndDate = Carbon::today()->subYear()->endOfYear()->endOfDay();
        } else {
            // mes_actual por defecto
            $startDate = Carbon::today()->startOfMonth();
            $endDate = Carbon::today()->endOfMonth()->endOfDay();
            $prevStartDate = Carbon::today()->subMonth()->startOfMonth();
            $prevEndDate = Carbon::today()->subMonth()->endOfMonth()->endOfDay();
        }

        // Consulta de ventas filtradas
        $salesQuery = Sale::with(['cliente', 'cajero', 'detalles.producto'])
            ->where('status', 'PAGADO')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if (!empty($this->categoria)) {
            $salesQuery->whereHas('detalles.producto', function ($q) {
                $q->where('type', $this->categoria);
            });
        }

        $sales = $salesQuery->get();

        // Ventas período anterior (para cálculo de tendencia %)
        $prevSalesQuery = Sale::where('status', 'PAGADO')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate]);
        if (!empty($this->categoria)) {
            $prevSalesQuery->whereHas('detalles.producto', function ($q) {
                $q->where('type', $this->categoria);
            });
        }
        $prevSales = $prevSalesQuery->get();

        $ventasPeriodo = (float) $sales->sum('total');
        $prevVentasPeriodo = (float) $prevSales->sum('total');
        $porcentajeVentas = $prevVentasPeriodo > 0 ? (($ventasPeriodo - $prevVentasPeriodo) / $prevVentasPeriodo) * 100 : ($ventasPeriodo > 0 ? 100 : 0);

        $totalVentasCount = $sales->count();
        $ticketPromedio = $totalVentasCount > 0 ? $ventasPeriodo / $totalVentasCount : 0;
        $prevTicketPromedio = $prevSales->count() > 0 ? $prevVentasPeriodo / $prevSales->count() : 0;
        $porcentajeTicket = $prevTicketPromedio > 0 ? (($ticketPromedio - $prevTicketPromedio) / $prevTicketPromedio) * 100 : ($ticketPromedio > 0 ? 100 : 0);

        // 1. Gráfico de evolución de ventas
        $ventasChartLabels = [];
        $ventasChartData = [];

        if ($this->periodo === 'hoy') {
            for ($i = 0; $i < 24; $i++) {
                $time = sprintf('%02d:00', $i);
                $ventasChartLabels[] = $time;
                $ventasChartData[$time] = 0;
            }
            foreach ($sales as $s) {
                $time = $s->created_at->format('H:00');
                if (isset($ventasChartData[$time])) {
                    $ventasChartData[$time] += (float) $s->total;
                }
            }
            $ventasChartData = array_values($ventasChartData);
        } elseif ($this->periodo === 'anio_actual') {
            for ($i = 1; $i <= 12; $i++) {
                $month = str_pad($i, 2, '0', STR_PAD_LEFT);
                $monthName = Carbon::createFromFormat('m', $month)->translatedFormat('M');
                $ventasChartLabels[] = ucfirst($monthName);
                $ventasChartData[$month] = 0;
            }
            foreach ($sales as $s) {
                $month = $s->created_at->format('m');
                if (isset($ventasChartData[$month])) {
                    $ventasChartData[$month] += (float) $s->total;
                }
            }
            $ventasChartData = array_values($ventasChartData);
        } elseif ($this->periodo === 'semana_actual') {
            $start = clone $startDate;
            for ($i = 0; $i < 7; $i++) {
                $date = $start->copy()->addDays($i);
                $ventasChartLabels[] = $date->translatedFormat('D d/m');
                $ventasChartData[$date->format('Y-m-d')] = 0;
            }
            foreach ($sales as $s) {
                $date = $s->created_at->format('Y-m-d');
                if (isset($ventasChartData[$date])) {
                    $ventasChartData[$date] += (float) $s->total;
                }
            }
            $ventasChartData = array_values($ventasChartData);
        } else {
            // Mes o Personalizado
            $days = $startDate->diffInDays($endDate) + 1;
            if ($days <= 31) {
                for ($i = 0; $i < $days; $i++) {
                    $date = $startDate->copy()->addDays($i);
                    $ventasChartLabels[] = $date->format('d/m');
                    $ventasChartData[$date->format('Y-m-d')] = 0;
                }
                foreach ($sales as $s) {
                    $date = $s->created_at->format('Y-m-d');
                    if (isset($ventasChartData[$date])) {
                        $ventasChartData[$date] += (float) $s->total;
                    }
                }
                $ventasChartData = array_values($ventasChartData);
            } else {
                // Agrupar por semanas si son más de 31 días
                $weeks = ceil($days / 7);
                for ($i = 0; $i < $weeks; $i++) {
                    $wStart = $startDate->copy()->addWeeks($i);
                    $wEnd = $wStart->copy()->endOfWeek();
                    $ventasChartLabels[] = 'Week ' . ($i + 1) . ' (' . $wStart->format('d/m') . ')';
                    $ventasChartData[$i] = 0;

                    foreach ($sales as $s) {
                        if ($s->created_at >= $wStart && $s->created_at <= $wEnd) {
                            $ventasChartData[$i] += (float) $s->total;
                        }
                    }
                }
                $ventasChartData = array_values($ventasChartData);
            }
        }

        // 2. Métodos de Pago
        $pagosMap = [
            'EFECTIVO'       => 'Cash',
            'TARJETA'        => 'Card',
            'YAPE_PLIN'      => 'Yape / Plin',
            'TRANSFERENCIA'  => 'Bank Transfer',
        ];
        $pagosTotales = [];
        foreach ($pagosMap as $key => $label) {
            $pagosTotales[$label] = (float) $sales->where('payment_method', $key)->sum('total');
        }
        $pagosChartLabels = array_keys($pagosTotales);
        $pagosChartData = array_values($pagosTotales);

        // 3. Citas Médicas
        $appointmentsQuery = Appointment::with(['cliente', 'mascota.especie', 'veterinario'])
            ->whereBetween('fecha_hora', [$startDate, $endDate]);

        $appointments = $appointmentsQuery->get();
        $prevAppointments = Appointment::whereBetween('fecha_hora', [$prevStartDate, $prevEndDate])->get();

        $totalCitas = $appointments->count();
        $citasCompletadas = $appointments->where('status', 'COMPLETADA')->count();
        $prevCitasCompletadas = $prevAppointments->where('status', 'COMPLETADA')->count();
        $porcentajeCitas = $prevCitasCompletadas > 0 ? (($citasCompletadas - $prevCitasCompletadas) / $prevCitasCompletadas) * 100 : ($citasCompletadas > 0 ? 100 : 0);

        $citasCanceladas = $appointments->where('status', 'CANCELADA')->count();
        $citasPendientes = $appointments->whereIn('status', ['PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO'])->count();

        $citasChartLabels = ['Completed', 'Cancelled', 'Pending'];
        $citasChartData = [$citasCompletadas, $citasCanceladas, $citasPendientes];

        // 4. Top Productos y Servicios más vendidos
        $topDetallesQuery = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->leftJoin('products', 'sale_details.product_id', '=', 'products.id')
            ->select('sale_details.description', DB::raw('SUM(sale_details.quantity) as total_qty'), DB::raw('SUM(sale_details.subtotal) as total_revenue'))
            ->where('sales.status', 'PAGADO')
            ->whereBetween('sales.created_at', [$startDate, $endDate]);

        if (!empty($this->categoria)) {
            $topDetallesQuery->where(function($q) {
                $q->where('products.type', $this->categoria)
                  ->orWhere('products.categoria', $this->categoria);
            });
        }

        if (!empty($this->sucursal_id)) {
            $topDetallesQuery->where('sales.clinic_id', $this->sucursal_id);
        }

        $topDetalles = $topDetallesQuery
            ->groupBy('sale_details.description')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        $topProductosLabels = $topDetalles->pluck('description')->toArray();
        $topProductosData = $topDetalles->pluck('total_revenue')->map(fn($v) => (float)$v)->toArray();

        // 5. Inventario y Alertas
        $productosStockBajo = Product::where('type', '!=', 'SERVICIO')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->count();

        $lotesProximosVencerCount = \App\Models\ProductBatch::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', now()->addDays(90))
            ->where('stock_actual', '>', 0)
            ->count();

        $valorizacionInventario = Product::where('type', '!=', 'SERVICIO')
            ->where('is_active', true)
            ->selectRaw('SUM(current_stock * precio_final) as valor')
            ->value('valor') ?? 0;

        // 6. Diagnósticos y Motivos más frecuentes en el período
        $topDiagnosticos = DB::table('medical_records')
            ->select('diagnostico_presuntivo', DB::raw('count(*) as total'))
            ->whereNotNull('diagnostico_presuntivo')
            ->where('diagnostico_presuntivo', '!=', '')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('diagnostico_presuntivo')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $sucursales = Branch::where('is_active', true)->get();

        $ventasDetalle = $sales->take(5);
        $citasDetalle = $appointments->take(5);
        $simboloMoneda = \App\Models\Clinic::first()?->simbolo_moneda ?? 'S/';

        return compact(
            'startDate', 'endDate',
            'ventasPeriodo', 'porcentajeVentas',
            'ticketPromedio', 'porcentajeTicket', 'totalVentasCount',
            'sales', 'appointments', 'ventasDetalle', 'citasDetalle',
            'totalCitas', 'citasCompletadas', 'porcentajeCitas',
            'citasCanceladas', 'citasPendientes',
            'productosStockBajo', 'lotesProximosVencerCount', 'valorizacionInventario',
            'ventasChartLabels', 'ventasChartData',
            'citasChartLabels', 'citasChartData',
            'topProductosLabels', 'topProductosData', 'topDetalles',
            'pagosChartLabels', 'pagosChartData',
            'topDiagnosticos', 'sucursales', 'simboloMoneda'
        );
    }

    public function exportarPdf()
    {
        $data = $this->getReportData();
        $data['periodo'] = $this->periodo;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte_ejecutivo_' . $this->periodo . '_' . date('Ymd_His') . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function exportarExcel()
    {
        $data = $this->getReportData();
        $periodoNombre = strtoupper(str_replace('_', ' ', $this->periodo));
        $rangoFechas = $data['startDate']->format('d/m/Y') . ' al ' . $data['endDate']->format('d/m/Y');

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $html .= '<style>
            body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #1e293b; }
            .title { font-size: 16pt; font-weight: bold; color: #065f46; }
            .subtitle { font-size: 11pt; color: #64748b; margin-bottom: 12px; }
            .section-header { background-color: #059669; color: #ffffff; font-weight: bold; font-size: 12pt; text-align: left; padding: 6px; }
            .th-header { background-color: #f1f5f9; color: #0f172a; font-weight: bold; border: 1px solid #cbd5e1; padding: 6px; }
            .td-cell { border: 1px solid #e2e8f0; padding: 5px; }
            .td-num { border: 1px solid #e2e8f0; padding: 5px; text-align: right; }
            .td-center { border: 1px solid #e2e8f0; padding: 5px; text-align: center; }
            .kpi-title { font-weight: bold; background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 6px; }
            .kpi-val { font-weight: bold; color: #047857; text-align: right; background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 6px; }
        </style></head><body>';

        $html .= '<table>';
        $html .= '<tr><td colspan="6" class="title">VETCORESSEN - REPORTE ESTADÍSTICO EJECUTIVO</td></tr>';
        $html .= "<tr><td colspan=\"6\" class=\"subtitle\">Período: <strong>{$periodoNombre}</strong> | Rango: <strong>{$rangoFechas}</strong></td></tr>";
        $html .= '<tr><td colspan="6"></td></tr>';

        // 1. Resumen de Métricas Clave
        $html .= '<tr><td colspan="6" class="section-header">1. RESUMEN DE MÉTRICAS CLAVE</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">Ingresos Totales del Período</td><td colspan="2" class="kpi-val">S/ ' . number_format($data['ventasPeriodo'], 2) . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">Ticket Promedio por Venta</td><td colspan="2" class="kpi-val">S/ ' . number_format($data['ticketPromedio'], 2) . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">Total de Ventas Concretadas</td><td colspan="2" class="kpi-val">' . $data['totalVentasCount'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">Citas Médicas Atendidas / Completadas</td><td colspan="2" class="kpi-val">' . $data['citasCompletadas'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">Citas Pendientes / En Progreso</td><td colspan="2" class="kpi-val">' . $data['citasPendientes'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">Citas Canceladas</td><td colspan="2" class="kpi-val">' . $data['citasCanceladas'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">Productos con Stock Bajo</td><td colspan="2" class="kpi-val">' . $data['productosStockBajo'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">Lotes Próximos a Vencer (< 90 días)</td><td colspan="2" class="kpi-val">' . $data['lotesProximosVencerCount'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">Valorización de Inventario Activo</td><td colspan="2" class="kpi-val">S/ ' . number_format($data['valorizacionInventario'], 2) . '</td></tr>';
        $html .= '<tr><td colspan="6"></td></tr>';

        // 2. Detalle de Ventas
        $html .= '<tr><td colspan="6" class="section-header">2. DETALLE DE VENTAS DEL PERÍODO</td></tr>';
        $html .= '<tr>
            <th class="th-header">ID Venta</th>
            <th class="th-header">Fecha y Hora</th>
            <th class="th-header">Cliente</th>
            <th class="th-header">Comprobante</th>
            <th class="th-header">Método Pago</th>
            <th class="th-header" style="text-align: right;">Total (S/)</th>
        </tr>';
        foreach ($data['sales'] as $sale) {
            $clienteNombre = htmlspecialchars($sale->cliente ? $sale->cliente->nombre_completo : 'Cliente General');
            $fecha = $sale->created_at->format('d/m/Y H:i');
            $totalFormateado = number_format($sale->total, 2);
            $html .= "<tr>
                <td class=\"td-center\">#{$sale->id}</td>
                <td class=\"td-center\">{$fecha}</td>
                <td class=\"td-cell\">{$clienteNombre}</td>
                <td class=\"td-center\">{$sale->tipo_comprobante}</td>
                <td class=\"td-center\">{$sale->payment_method}</td>
                <td class=\"td-num\">S/ {$totalFormateado}</td>
            </tr>";
        }
        $html .= '<tr><td colspan="6"></td></tr>';

        // 3. Detalle de Citas Médicas
        $html .= '<tr><td colspan="6" class="section-header">3. DETALLE DE CITAS MÉDICAS</td></tr>';
        $html .= '<tr>
            <th class="th-header">ID Cita</th>
            <th class="th-header">Fecha Programada</th>
            <th class="th-header">Cliente</th>
            <th class="th-header">Mascota</th>
            <th class="th-header">Motivo</th>
            <th class="th-header">Estado</th>
        </tr>';
        foreach ($data['appointments'] as $appt) {
            $clienteNombre = htmlspecialchars($appt->cliente ? $appt->cliente->nombre_completo : '-');
            $mascotaNombre = htmlspecialchars($appt->mascota ? $appt->mascota->name : '-');
            $fecha = $appt->fecha_hora ? $appt->fecha_hora->format('d/m/Y H:i') : '-';
            $motivo = htmlspecialchars($appt->reason ?? '-');
            $html .= "<tr>
                <td class=\"td-center\">#{$appt->id}</td>
                <td class=\"td-center\">{$fecha}</td>
                <td class=\"td-cell\">{$clienteNombre}</td>
                <td class=\"td-cell\">{$mascotaNombre}</td>
                <td class=\"td-cell\">{$motivo}</td>
                <td class=\"td-center\">{$appt->status}</td>
            </tr>";
        }
        $html .= '<tr><td colspan="6"></td></tr>';

        // 4. Top Productos
        if (count($data['topDetalles']) > 0) {
            $html .= '<tr><td colspan="6" class="section-header">4. TOP PRODUCTOS Y SERVICIOS MÁS VENDIDOS</td></tr>';
            $html .= '<tr>
                <th class="th-header" colspan="3">Producto / Servicio</th>
                <th class="th-header" colspan="1" style="text-align: center;">Cantidad Vendida</th>
                <th class="th-header" colspan="2" style="text-align: right;">Total Facturado (S/)</th>
            </tr>';
            foreach ($data['topDetalles'] as $top) {
                $itemDesc = htmlspecialchars($top->description);
                $revFormateado = number_format($top->total_revenue, 2);
                $html .= "<tr>
                    <td class=\"td-cell\" colspan=\"3\">{$itemDesc}</td>
                    <td class=\"td-center\" colspan=\"1\">{$top->total_qty}</td>
                    <td class=\"td-num\" colspan=\"2\">S/ {$revFormateado}</td>
                </tr>";
            }
        }

        $html .= '</table></body></html>';

        return response()->streamDownload(function () use ($html) {
            echo "\xEF\xBB\xBF" . $html;
        }, 'reporte_ejecutivo_' . $this->periodo . '_' . date('Ymd_His') . '.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function exportarCsv()
    {
        $data = $this->getReportData();
        $csv = "REPORTE ANALITICO EJECUTIVO - VETCORESSEN\n";
        $csv .= "Periodo: " . strtoupper(str_replace('_', ' ', $this->periodo)) . "\n";
        $csv .= "Rango: " . $data['startDate']->format('d/m/Y') . " al " . $data['endDate']->format('d/m/Y') . "\n\n";

        // Resumen
        $csv .= "--- RESUMEN DE METRICAS CLAVE ---\n";
        $csv .= "Metrica,Valor\n";
        $csv .= "Ingresos Totales (S/)," . number_format($data['ventasPeriodo'], 2, '.', '') . "\n";
        $csv .= "Ticket Promedio (S/)," . number_format($data['ticketPromedio'], 2, '.', '') . "\n";
        $csv .= "Ventas Concretadas," . $data['totalVentasCount'] . "\n";
        $csv .= "Citas Atendidas," . $data['citasCompletadas'] . "\n";
        $csv .= "Citas Canceladas," . $data['citasCanceladas'] . "\n";
        $csv .= "Citas Pendientes / En Progreso," . $data['citasPendientes'] . "\n";
        $csv .= "Productos con Stock Bajo," . $data['productosStockBajo'] . "\n";
        $csv .= "Lotes Proximos a Vencer (90d)," . $data['lotesProximosVencerCount'] . "\n";
        $csv .= "Valorizacion de Inventario (S/)," . number_format($data['valorizacionInventario'], 2, '.', '') . "\n\n";

        // Detalle de Ventas
        $csv .= "--- DETALLE DE VENTAS CONCRETADAS ---\n";
        $csv .= "ID Venta,Fecha y Hora,Cliente,Documento,Tipo Comprobante,Metodo Pago,Subtotal,IGV,Total (S/)\n";
        foreach ($data['sales'] as $sale) {
            $clienteNombre = $sale->cliente ? str_replace(',', ' ', $sale->cliente->nombre_completo) : 'Cliente General';
            $clienteDoc = $sale->cliente ? $sale->cliente->numero_documento : '-';
            $fecha = $sale->created_at->format('Y-m-d H:i');
            $csv .= "{$sale->id},{$fecha},{$clienteNombre},{$clienteDoc},{$sale->tipo_comprobante},{$sale->payment_method},{$sale->subtotal},{$sale->igv},{$sale->total}\n";
        }
        $csv .= "\n";

        // Detalle de Citas
        $csv .= "--- DETALLE DE CITAS MEDICAS ---\n";
        $csv .= "ID Cita,Fecha Programada,Cliente,Mascota,Especie,Motivo,Estado,Veterinario\n";
        foreach ($data['appointments'] as $appt) {
            $clienteNombre = $appt->cliente ? str_replace(',', ' ', $appt->cliente->nombre_completo) : '-';
            $mascotaNombre = $appt->mascota ? str_replace(',', ' ', $appt->mascota->name) : '-';
            $especie = $appt->mascota ? ($appt->mascota->especie->name ?? '-') : '-';
            $fecha = $appt->fecha_hora ? $appt->fecha_hora->format('Y-m-d H:i') : '-';
            $motivo = str_replace(',', ' ', $appt->reason ?? '-');
            $veterinario = $appt->veterinario ? str_replace(',', ' ', $appt->veterinario->name) : '-';
            $csv .= "{$appt->id},{$fecha},{$clienteNombre},{$mascotaNombre},{$especie},{$motivo},{$appt->status},{$veterinario}\n";
        }
        $csv .= "\n";

        // Top Productos
        $csv .= "--- TOP PRODUCTOS Y SERVICIOS MAS VENDIDOS ---\n";
        $csv .= "Item / Producto,Cantidad Vendida,Total Facturado (S/)\n";
        foreach ($data['topDetalles'] as $top) {
            $itemNombre = str_replace(',', ' ', $top->description);
            $csv .= "{$itemNombre},{$top->total_qty},{$top->total_revenue}\n";
        }

        return response()->streamDownload(function () use ($csv) {
            echo "\xEF\xBB\xBF"; // UTF-8 BOM para apertura nativa en Excel
            echo $csv;
        }, 'reporte_ejecutivo_' . $this->periodo . '_' . date('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render()
    {
        $data = $this->getReportData();

        return view('livewire.reportes.reporte-index', $data);
    }
}
