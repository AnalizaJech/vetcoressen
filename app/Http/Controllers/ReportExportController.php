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
        $rawPeriodo = (string) $request->get('periodo', 'mes_actual');
        $allowedPeriodos = ['hoy', 'semana_actual', 'mes_actual', 'anio_actual', 'personalizado'];
        $periodo = in_array($rawPeriodo, $allowedPeriodos) ? $rawPeriodo : 'mes_actual';

        $fecha_inicio = trim((string) $request->get('fecha_inicio', ''));
        $fecha_fin = trim((string) $request->get('fecha_fin', ''));

        // Remove any non-date characters from fecha_inicio and fecha_fin
        $fecha_inicio = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) ? $fecha_inicio : '';
        $fecha_fin = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin) ? $fecha_fin : '';

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
            ?? 'en';
            
        if (!in_array($lang, ['es', 'en'])) {
            $lang = 'en';
        }
        
        $jsonPath = public_path("locales/{$lang}.json");
        if (!file_exists($jsonPath)) {
            $jsonPath = base_path("public/locales/{$lang}.json");
        }

        $translations = [];
        if (file_exists($jsonPath)) {
            $translations = json_decode(file_get_contents($jsonPath), true) ?: [];
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

        $cleanPeriodo = preg_replace('/[^a-zA-Z0-9_-]/', '', $data['periodo']);
        $filename = 'executive_report_' . $cleanPeriodo . '_' . date('Ymd_His') . '.pdf';

        $pdf = Pdf::loadView('pdf.reporte', $data);
        $pdf->setPaper('a4', 'portrait');

        if ($request->has('download') && $request->download) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
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
        $rangoFechas = $data['startDate']->format('M d, Y') . ' - ' . $data['endDate']->format('M d, Y');

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
        $html .= '<tr><td colspan="7" class="title">VETCORESSEN - ' . strtoupper($t('report.executiveReport', 'EXECUTIVE STATISTICAL REPORT')) . '</td></tr>';
        $html .= "<tr><td colspan=\"7\" class=\"subtitle\">" . $t('report.period', 'Period') . ": <strong>{$periodoNombre}</strong> | " . $t('report.range', 'Range') . ": <strong>{$rangoFechas}</strong></td></tr>";
        $html .= '<tr><td colspan="7"></td></tr>';

        // 1. Resumen de Métricas Clave
        $html .= '<tr><td colspan="7" class="section-header">' . $t('report.excelKeyMetrics', '1. KEY METRICS SUMMARY') . '</td></tr>';
        $html .= '<tr><td colspan="5" class="kpi-title">' . $t('report.totalRevenue', 'Total Revenue for Period') . '</td><td colspan="2" class="kpi-val">S/ ' . number_format($data['ventasPeriodo'], 2) . '</td></tr>';
        $html .= '<tr><td colspan="5" class="kpi-title">' . $t('report.averageTicket', 'Average Ticket') . '</td><td colspan="2" class="kpi-val">S/ ' . number_format($data['ticketPromedio'], 2) . '</td></tr>';
        $html .= '<tr><td colspan="5" class="kpi-title">' . $t('report.periodSales', 'Total Completed Sales') . '</td><td colspan="2" class="kpi-val">' . $data['totalVentasCount'] . '</td></tr>';
        $html .= '<tr><td colspan="5" class="kpi-title">' . $t('report.completedAppointments', 'Completed Appointments') . '</td><td colspan="2" class="kpi-val">' . $data['citasCompletadas'] . '</td></tr>';
        $html .= '<tr><td colspan="5" class="kpi-title">' . $t('report.pendingAppointments', 'Pending / In Progress Appointments') . '</td><td colspan="2" class="kpi-val">' . $data['citasPendientes'] . '</td></tr>';
        $html .= '<tr><td colspan="5" class="kpi-title">' . $t('report.cancelledAppointments', 'Cancelled Appointments') . '</td><td colspan="2" class="kpi-val">' . $data['citasCanceladas'] . '</td></tr>';
        $html .= '<tr><td colspan="5" class="kpi-title">' . $t('report.lowStockProducts', 'Low Stock Products') . '</td><td colspan="2" class="kpi-val">' . $data['productosStockBajo'] . '</td></tr>';
        $html .= '<tr><td colspan="5" class="kpi-title">' . $t('dashboard.expiringBatches', 'Expiring Batches (< 90 days)') . '</td><td colspan="2" class="kpi-val">' . $data['lotesProximosVencerCount'] . '</td></tr>';
        $html .= '<tr><td colspan="5" class="kpi-title">' . $t('report.financialSummary', 'Active Inventory Valuation') . '</td><td colspan="2" class="kpi-val">S/ ' . number_format($data['valorizacionInventario'], 2) . '</td></tr>';
        $html .= '<tr><td colspan="7"></td></tr>';

        // 2. Detalle de Ventas
        $html .= '<tr><td colspan="7" class="section-header">' . $t('report.excelSalesDetail', '2. PERIOD SALES DETAIL') . '</td></tr>';
        $html .= '<tr>
            <th class="th-header">' . $t('report.receiptNo', 'ID') . '</th>
            <th class="th-header">' . $t('report.dateTime', 'Date & Time') . '</th>
            <th class="th-header">' . $t('report.client', 'Client') . '</th>
            <th class="th-header">' . $t('form.receiptType', 'Receipt') . '</th>
            <th class="th-header">' . $t('report.paymentMethod', 'Payment Method') . '</th>
            <th class="th-header" style="text-align: right;">' . $t('report.total', 'Total (S/)') . '</th>
            <th class="th-header" style="text-align: center;">' . $t('report.status', 'Status') . '</th>
        </tr>';
        foreach ($data['sales'] as $sale) {
            $clienteNombre = htmlspecialchars($sale->cliente ? $sale->cliente->nombre_completo : $t('report.walkInCustomer', 'Walk-in Customer'));
            $fecha = $sale->created_at->format('M d, Y h:i A');
            $totalFormateado = number_format($sale->total, 2);
            $payKey = 'payment.' . strtolower(str_replace([' ', '/', '-'], '_', $sale->payment_method ?? ''));
            $payMethodTrad = htmlspecialchars($t($payKey, $sale->payment_method ?? '-'));
            $saleStatusTrad = htmlspecialchars($t('status.' . strtolower($sale->status), $t('payment.' . strtolower($sale->status), $sale->status)));
            $html .= "<tr>
                <td class=\"td-center\">#{$sale->id}</td>
                <td class=\"td-center\">{$fecha}</td>
                <td class=\"td-cell\">{$clienteNombre}</td>
                <td class=\"td-center\">{$sale->tipo_comprobante}</td>
                <td class=\"td-center\">{$payMethodTrad}</td>
                <td class=\"td-num\">S/ {$totalFormateado}</td>
                <td class=\"td-center\">{$saleStatusTrad}</td>
            </tr>";
        }
        $html .= '<tr><td colspan="7"></td></tr>';

        // 3. Detalle de Citas Médicas
        $html .= '<tr><td colspan="7" class="section-header">' . $t('report.excelApptDetail', '3. MEDICAL APPOINTMENTS DETAIL') . '</td></tr>';
        $html .= '<tr>
            <th class="th-header">' . $t('appointment.apptNumber', 'Appt ID') . '</th>
            <th class="th-header">' . $t('appointment.scheduledDate', 'Scheduled Date') . '</th>
            <th class="th-header">' . $t('table.owner', 'Client') . '</th>
            <th class="th-header">' . $t('form.pet', 'Pet') . '</th>
            <th class="th-header">' . $t('table.reason', 'Reason') . '</th>
            <th class="th-header">' . $t('report.status', 'Status') . '</th>
            <th class="th-header">' . $t('table.veterinarian', 'Veterinarian') . '</th>
        </tr>';
        foreach ($data['appointments'] as $appt) {
            $clienteNombre = htmlspecialchars($appt->cliente ? $appt->cliente->nombre_completo : '-');
            $mascotaNombre = htmlspecialchars($appt->mascota ? $appt->mascota->name : '-');
            $fecha = $appt->fecha_hora ? $appt->fecha_hora->format('M d, Y h:i A') : '-';
            $veterinarioNombre = htmlspecialchars($appt->veterinario ? $appt->veterinario->name : '-');
            
            $rawReason = $appt->reason ?? '';
            $cleanReason = trim(strtolower($rawReason));
            if (empty($cleanReason) || in_array($cleanReason, ['sin motivo especificado', 'no especificado', 'no especificada', 'not specified'])) {
                $motivo = $t('misc.notSpecified', 'Not specified');
            } else {
                $rKey = 'reason.' . strtolower(str_replace([' ', 'ó', 'í', 'á', 'é', 'ú'], ['_', 'o', 'i', 'a', 'e', 'u'], $cleanReason));
                $motivo = htmlspecialchars($t($rKey, $rawReason));
            }

            $statusTraducido = htmlspecialchars($t('status.' . strtolower($appt->status), $appt->status));
            $html .= "<tr>
                <td class=\"td-center\">#{$appt->id}</td>
                <td class=\"td-center\">{$fecha}</td>
                <td class=\"td-cell\">{$clienteNombre}</td>
                <td class=\"td-cell\">{$mascotaNombre}</td>
                <td class=\"td-cell\">{$motivo}</td>
                <td class=\"td-center\">{$statusTraducido}</td>
                <td class=\"td-cell\">{$veterinarioNombre}</td>
            </tr>";
        }
        $html .= '<tr><td colspan="7"></td></tr>';

        // 4. Top Productos
        if (count($data['topDetalles']) > 0) {
            $html .= '<tr><td colspan="7" class="section-header">' . $t('report.excelTopProducts', '4. TOP PRODUCTS & SERVICES') . '</td></tr>';
            $html .= '<tr>
                <th class="th-header" colspan="3">' . $t('form.product', 'Product / Service') . '</th>
                <th class="th-header" colspan="2" style="text-align: center;">' . $t('report.qtySold', 'Qty Sold') . '</th>
                <th class="th-header" colspan="2" style="text-align: right;">' . $t('report.totalInvoiced', 'Total Invoiced (S/)') . '</th>
            </tr>';
            foreach ($data['topDetalles'] as $top) {
                $itemDesc = htmlspecialchars($top->description);
                $revFormateado = number_format($top->total_revenue, 2);
                $html .= "<tr>
                    <td class=\"td-cell\" colspan=\"3\">{$itemDesc}</td>
                    <td class=\"td-center\" colspan=\"2\">{$top->total_qty}</td>
                    <td class=\"td-num\" colspan=\"2\">S/ {$revFormateado}</td>
                </tr>";
            }
        }

        $html .= '</table></body></html>';

        $cleanPeriodo = preg_replace('/[^a-zA-Z0-9_-]/', '', $data['periodo']);
        $filename = 'executive_report_' . $cleanPeriodo . '_' . date('Ymd_His') . '.xls';

        return response("\xEF\xBB\xBF" . $html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
