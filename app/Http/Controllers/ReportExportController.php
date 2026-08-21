<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\MedicalRecord;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportExportController extends Controller
{
    private function getReportData(Request $request): array
    {
        $periodo = $request->get('periodo', 'mes_actual');
        $fecha_inicio = $request->get('fecha_inicio', '');
        $fecha_fin = $request->get('fecha_fin', '');

        if ($periodo === 'personalizado' && !empty($fecha_inicio) && !empty($fecha_fin)) {
            $startDate = Carbon::parse($fecha_inicio)->startOfDay();
            $endDate = Carbon::parse($fecha_fin)->endOfDay();
        } elseif ($periodo === 'hoy') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($periodo === 'semana_actual') {
            $startDate = Carbon::today()->startOfWeek();
            $endDate = Carbon::today()->endOfWeek()->endOfDay();
        } elseif ($periodo === 'anio_actual') {
            $startDate = Carbon::today()->startOfYear();
            $endDate = Carbon::today()->endOfYear()->endOfDay();
        } else { // mes_actual
            $startDate = Carbon::today()->startOfMonth();
            $endDate = Carbon::today()->endOfMonth()->endOfDay();
        }

        $sales = Sale::with(['cliente', 'detalles.producto'])
            ->where('status', 'PAGADO')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->get();

        $ventasPeriodo = (float) $sales->sum('total');
        $totalVentasCount = $sales->count();
        $ticketPromedio = $totalVentasCount > 0 ? $ventasPeriodo / $totalVentasCount : 0;

        $appointments = Appointment::with(['cliente', 'mascota.especie', 'veterinario'])
            ->whereBetween('fecha_hora', [$startDate, $endDate])
            ->orderBy('fecha_hora')
            ->get();

        $totalCitas = $appointments->count();
        $citasCompletadas = $appointments->where('status', 'COMPLETADA')->count();
        $citasCanceladas = $appointments->where('status', 'CANCELADA')->count();
        $citasPendientes = $appointments->whereIn('status', ['PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO'])->count();

        $topDetalles = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->select('sale_details.description', DB::raw('SUM(sale_details.quantity) as total_qty'), DB::raw('SUM(sale_details.subtotal) as total_revenue'))
            ->where('sales.status', 'PAGADO')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('sale_details.description')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

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

        return compact(
            'periodo', 'startDate', 'endDate',
            'ventasPeriodo', 'totalVentasCount', 'ticketPromedio',
            'sales', 'appointments',
            'totalCitas', 'citasCompletadas', 'citasCanceladas', 'citasPendientes',
            'topDetalles', 'productosStockBajo', 'lotesProximosVencerCount', 'valorizacionInventario'
        );
    }

    public function pdf(Request $request)
    {
        $data = $this->getReportData($request);
        $pdf = Pdf::loadView('pdf.reporte', $data);
        return $pdf->download('reporte_ejecutivo_' . $data['periodo'] . '_' . date('Ymd_His') . '.pdf');
    }

    public function excel(Request $request)
    {
        $data = $this->getReportData($request);
        $csv = "REPORTE ANALITICO EJECUTIVO - VETCORESSEN\n";
        $csv .= "Periodo: " . strtoupper(str_replace('_', ' ', $data['periodo'])) . "\n";
        $csv .= "Rango: " . $data['startDate']->format('d/m/Y') . " al " . $data['endDate']->format('d/m/Y') . "\n\n";

        // Resumen
        $csv .= "--- RESUMEN DE METRICAS CLAVE ---\n";
        $csv .= "Metrica,Valor\n";
        $csv .= "Ingresos Totales (S/)," . number_format($data['ventasPeriodo'], 2, '.', '') . "\n";
        $csv .= "Ticket Promedio (S/)," . number_format($data['ticketPromedio'], 2, '.', '') . "\n";
        $csv .= "Ventas Concretadas," . $data['totalVentasCount'] . "\n";
        $csv .= "Citas Atendidas," . $data['citasCompletadas'] . "\n";
        $csv .= "Citas Canceladas," . $data['citasCanceladas'] . "\n";
        $csv .= "Citas Pendientes," . $data['citasPendientes'] . "\n";
        $csv .= "Productos Stock Bajo," . $data['productosStockBajo'] . "\n";
        $csv .= "Lotes Proximos Vencer," . $data['lotesProximosVencerCount'] . "\n";
        $csv .= "Valorizacion Inventario (S/)," . number_format($data['valorizacionInventario'], 2, '.', '') . "\n\n";

        // Detalle de Ventas
        $csv .= "--- DETALLE DE VENTAS ---\n";
        $csv .= "ID Venta,Fecha,Cliente,Documento,Comprobante,Metodo Pago,Subtotal,IGV,Total (S/)\n";
        foreach ($data['sales'] as $sale) {
            $clienteNombre = $sale->cliente ? str_replace(',', ' ', $sale->cliente->nombre_completo) : 'Cliente General';
            $clienteDoc = $sale->cliente ? $sale->cliente->numero_documento : '-';
            $fecha = $sale->created_at->format('Y-m-d H:i');
            $csv .= "{$sale->id},{$fecha},{$clienteNombre},{$clienteDoc},{$sale->tipo_comprobante},{$sale->payment_method},{$sale->subtotal},{$sale->igv},{$sale->total}\n";
        }
        $csv .= "\n";

        // Detalle de Citas
        $csv .= "--- DETALLE DE CITAS MEDICAS ---\n";
        $csv .= "ID Cita,Fecha Programada,Cliente,Mascota,Motivo,Estado,Veterinario\n";
        foreach ($data['appointments'] as $appt) {
            $clienteNombre = $appt->cliente ? str_replace(',', ' ', $appt->cliente->nombre_completo) : '-';
            $mascotaNombre = $appt->mascota ? str_replace(',', ' ', $appt->mascota->name) : '-';
            $fecha = $appt->fecha_hora ? $appt->fecha_hora->format('Y-m-d H:i') : '-';
            $motivo = str_replace(',', ' ', $appt->reason ?? '-');
            $veterinario = $appt->veterinario ? str_replace(',', ' ', $appt->veterinario->name) : '-';
            $csv .= "{$appt->id},{$fecha},{$clienteNombre},{$mascotaNombre},{$motivo},{$appt->status},{$veterinario}\n";
        }
        $csv .= "\n";

        // Top Productos
        $csv .= "--- TOP PRODUCTOS VENDIDOS ---\n";
        $csv .= "Producto / Servicio,Cantidad Vendida,Total Facturado (S/)\n";
        foreach ($data['topDetalles'] as $top) {
            $itemNombre = str_replace(',', ' ', $top->description);
            $csv .= "{$itemNombre},{$top->total_qty},{$top->total_revenue}\n";
        }

        $filename = 'reporte_ejecutivo_' . $data['periodo'] . '_' . date('Ymd_His') . '.csv';

        return response("\xEF\xBB\xBF" . $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
