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

    private function getTranslations(Request $request)
    {
        $lang = $request->query('lang') 
            ?? $request->cookie('vc_locale') 
            ?? session('locale') 
            ?? 'es';
            
        if (!in_array($lang, ['es', 'en'])) {
            $lang = 'es';
        }
        
        $jsonPath = public_path("locales/{$lang}.json");
        $translations = [];
        if (file_exists($jsonPath)) {
            $translations = json_decode(file_get_contents($jsonPath), true);
        }

        $t = function ($key, $default = null) use ($translations) {
            $keys = explode('.', $key);
            $value = $translations;
            foreach ($keys as $k) {
                if (isset($value[$k])) {
                    $value = $value[$k];
                } else {
                    return $default !== null ? $default : $key;
                }
            }
            return is_string($value) ? $value : ($default !== null ? $default : $key);
        };

        return [$t, $lang];
    }

    public function pdf(Request $request)
    {
        $data = $this->getReportData($request);
        [$t, $lang] = $this->getTranslations($request);
        $data['t'] = $t;
        $data['lang'] = $lang;

        $pdf = Pdf::loadView('pdf.reporte', $data);
        return $pdf->download('reporte_ejecutivo_' . $data['periodo'] . '_' . date('Ymd_His') . '.pdf');
    }

    public function excel(Request $request)
    {
        $data = $this->getReportData($request);
        [$t, $lang] = $this->getTranslations($request);

        $periodoKey = match($data['periodo']) {
            'hoy' => 'report.today',
            'semana_actual' => 'report.thisWeek',
            'mes_actual' => 'report.thisMonth',
            'anio_actual' => 'report.thisYear',
            'personalizado' => 'report.custom',
            default => 'report.thisMonth',
        };
        $periodoNombre = strtoupper($t($periodoKey, str_replace('_', ' ', $data['periodo'])));
        $rangoFechas = $data['startDate']->format('d/m/Y') . ' ' . $t('report.to', 'al') . ' ' . $data['endDate']->format('d/m/Y');

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
        $html .= '<tr><td colspan="6" class="title">VETCORESSEN - ' . strtoupper($t('report.executiveReport', 'REPORTE ESTADÍSTICO EJECUTIVO')) . '</td></tr>';
        $html .= "<tr><td colspan=\"6\" class=\"subtitle\">" . $t('report.period', 'Período') . ": <strong>{$periodoNombre}</strong> | " . $t('report.range', 'Rango') . ": <strong>{$rangoFechas}</strong></td></tr>";
        $html .= '<tr><td colspan="6"></td></tr>';

        // 1. Resumen de Métricas Clave
        $html .= '<tr><td colspan="6" class="section-header">' . $t('report.excelKeyMetrics', '1. RESUMEN DE MÉTRICAS CLAVE') . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">' . $t('report.totalRevenue', 'Ingresos Totales del Período') . '</td><td colspan="2" class="kpi-val">S/ ' . number_format($data['ventasPeriodo'], 2) . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">' . $t('report.averageTicket', 'Ticket Promedio por Venta') . '</td><td colspan="2" class="kpi-val">S/ ' . number_format($data['ticketPromedio'], 2) . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">' . $t('report.totalRevenue', 'Total de Ventas Concretadas') . '</td><td colspan="2" class="kpi-val">' . $data['totalVentasCount'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">' . $t('report.completedAppointments', 'Citas Médicas Atendidas / Completadas') . '</td><td colspan="2" class="kpi-val">' . $data['citasCompletadas'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">' . $t('report.pendingAppointments', 'Citas Pendientes / En Progreso') . '</td><td colspan="2" class="kpi-val">' . $data['citasPendientes'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">' . $t('report.cancelledAppointments', 'Citas Canceladas') . '</td><td colspan="2" class="kpi-val">' . $data['citasCanceladas'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">' . $t('report.lowStockProducts', 'Productos con Stock Bajo') . '</td><td colspan="2" class="kpi-val">' . $data['productosStockBajo'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">' . $t('dashboard.expiringBatches', 'Lotes Próximos a Vencer (< 90 días)') . '</td><td colspan="2" class="kpi-val">' . $data['lotesProximosVencerCount'] . '</td></tr>';
        $html .= '<tr><td colspan="4" class="kpi-title">' . $t('report.financialSummary', 'Valorización de Inventario Activo') . '</td><td colspan="2" class="kpi-val">S/ ' . number_format($data['valorizacionInventario'], 2) . '</td></tr>';
        $html .= '<tr><td colspan="6"></td></tr>';

        // 2. Detalle de Ventas
        $html .= '<tr><td colspan="6" class="section-header">' . $t('report.excelSalesDetail', '2. DETALLE DE VENTAS DEL PERÍODO') . '</td></tr>';
        $html .= '<tr>
            <th class="th-header">' . $t('report.receiptNo', 'ID Venta') . '</th>
            <th class="th-header">' . $t('report.dateTime', 'Fecha y Hora') . '</th>
            <th class="th-header">' . $t('report.client', 'Cliente') . '</th>
            <th class="th-header">' . $t('form.receiptType', 'Comprobante') . '</th>
            <th class="th-header">' . $t('report.paymentMethod', 'Método Pago') . '</th>
            <th class="th-header" style="text-align: right;">' . $t('report.total', 'Total (S/)') . '</th>
        </tr>';
        foreach ($data['sales'] as $sale) {
            $clienteNombre = htmlspecialchars($sale->cliente ? $sale->cliente->nombre_completo : $t('report.walkInCustomer', 'Cliente General'));
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
        $html .= '<tr><td colspan="6" class="section-header">' . $t('report.excelApptDetail', '3. DETALLE DE CITAS MÉDICAS') . '</td></tr>';
        $html .= '<tr>
            <th class="th-header">' . $t('appointment.apptNumber', 'ID Cita') . '</th>
            <th class="th-header">' . $t('appointment.scheduledDate', 'Fecha Programada') . '</th>
            <th class="th-header">' . $t('table.owner', 'Cliente') . '</th>
            <th class="th-header">' . $t('form.pet', 'Mascota') . '</th>
            <th class="th-header">' . $t('table.reason', 'Motivo') . '</th>
            <th class="th-header">' . $t('report.status', 'Estado') . '</th>
        </tr>';
        foreach ($data['appointments'] as $appt) {
            $clienteNombre = htmlspecialchars($appt->cliente ? $appt->cliente->nombre_completo : '-');
            $mascotaNombre = htmlspecialchars($appt->mascota ? $appt->mascota->name : '-');
            $fecha = $appt->fecha_hora ? $appt->fecha_hora->format('d/m/Y H:i') : '-';
            $motivo = htmlspecialchars($appt->reason ?? '-');
            $statusTraducido = $t('status.' . strtolower($appt->status), $appt->status);
            $html .= "<tr>
                <td class=\"td-center\">#{$appt->id}</td>
                <td class=\"td-center\">{$fecha}</td>
                <td class=\"td-cell\">{$clienteNombre}</td>
                <td class=\"td-cell\">{$mascotaNombre}</td>
                <td class=\"td-cell\">{$motivo}</td>
                <td class=\"td-center\">{$statusTraducido}</td>
            </tr>";
        }
        $html .= '<tr><td colspan="6"></td></tr>';

        // 4. Top Productos
        if (count($data['topDetalles']) > 0) {
            $html .= '<tr><td colspan="6" class="section-header">' . $t('report.excelTopProducts', '4. TOP PRODUCTOS Y SERVICIOS MÁS VENDIDOS') . '</td></tr>';
            $html .= '<tr>
                <th class="th-header" colspan="3">' . $t('form.product', 'Producto / Servicio') . '</th>
                <th class="th-header" colspan="1" style="text-align: center;">' . $t('report.qtySold', 'Cantidad Vendida') . '</th>
                <th class="th-header" colspan="2" style="text-align: right;">' . $t('report.totalInvoiced', 'Total Facturado (S/)') . '</th>
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

        $filename = 'reporte_ejecutivo_' . $data['periodo'] . '_' . date('Ymd_His') . '.xls';

        return response("\xEF\xBB\xBF" . $html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
