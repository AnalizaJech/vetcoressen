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
        $this->periodo = 'personalizado';
        $this->dispatchChartsUpdate();
    }

    public function updatedFechaFin(): void
    {
        $this->periodo = 'personalizado';
        $this->dispatchChartsUpdate();
    }

    public function updatedSucursalId(): void
    {
        $this->dispatchChartsUpdate();
    }

    public function updatedCategoria(): void
    {
        $this->dispatchChartsUpdate();
    }

    private function dispatchChartsUpdate(): void
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
                    $ventasChartLabels[] = 'Sem ' . ($i + 1) . ' (' . $wStart->format('d/m') . ')';
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
            'EFECTIVO'       => 'Efectivo',
            'TARJETA'        => 'Tarjeta',
            'YAPE_PLIN'      => 'Yape / Plin',
            'TRANSFERENCIA'  => 'Transferencia',
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

        $citasChartLabels = ['Completadas', 'Canceladas', 'Pendientes / En Progreso'];
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

        return compact(
            'startDate', 'endDate',
            'ventasPeriodo', 'porcentajeVentas',
            'ticketPromedio', 'porcentajeTicket', 'totalVentasCount',
            'sales', 'appointments',
            'totalCitas', 'citasCompletadas', 'porcentajeCitas',
            'citasCanceladas', 'citasPendientes',
            'productosStockBajo', 'lotesProximosVencerCount', 'valorizacionInventario',
            'ventasChartLabels', 'ventasChartData',
            'citasChartLabels', 'citasChartData',
            'topProductosLabels', 'topProductosData', 'topDetalles',
            'pagosChartLabels', 'pagosChartData',
            'topDiagnosticos', 'sucursales'
        );
    }

    public function exportarPdf()
    {
        $data = $this->getReportData();
        $data['periodo'] = $this->periodo;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_ejecutivo_' . $this->periodo . '_' . date('Ymd_His') . '.pdf');
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
