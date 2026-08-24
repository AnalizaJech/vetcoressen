<!DOCTYPE html>
<html lang="{{ $lang ?? request()->query('lang', 'es') }}">
<head>
@php
    $clinic = \App\Models\Clinic::first();
    $logoSrc = '';
    if ($clinic && $clinic->logo && file_exists(public_path('storage/' . $clinic->logo))) {
        $path = public_path('storage/' . $clinic->logo);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif'])) {
            $logoData = base64_encode(file_get_contents($path));
            $logoMime = mime_content_type($path);
            $logoSrc = 'data:' . $logoMime . ';base64,' . $logoData;
        }
    }

    if (!isset($t) || !is_callable($t)) {
        $langCode = $lang ?? request()->query('lang', 'es');
        $jsonPath = public_path("locales/{$langCode}.json");
        $translations = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
        $t = function ($key, $default = null) use ($translations) {
            $keys = explode('.', $key);
            $val = $translations;
            foreach ($keys as $k) {
                if (isset($val[$k])) {
                    $val = $val[$k];
                } else {
                    return $default !== null ? $default : $key;
                }
            }
            return is_string($val) ? $val : ($default !== null ? $default : $key);
        };
    }

    $periodoKey = match($periodo ?? 'mes_actual') {
        'hoy' => 'report.today',
        'semana_actual' => 'report.thisWeek',
        'mes_actual' => 'report.thisMonth',
        'anio_actual' => 'report.thisYear',
        'personalizado' => 'report.custom',
        default => 'report.thisMonth',
    };
    $periodoLabel = strtoupper($t($periodoKey, str_replace('_', ' ', $periodo ?? '')));
@endphp

    <meta charset="utf-8">
    <title>{{ $t('report.executiveReport', 'Reporte Estadístico') }} - {{ $periodoLabel }}</title>
    <style>
        @page {
            margin: 15mm 14mm 15mm 14mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            font-size: 9.5px;
            background-color: #ffffff;
        }
        .container {
            width: 100%;
        }
        
        /* ═══ Header Principal ═══ */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border-bottom: 2.5px solid #059669;
            padding-bottom: 8px;
        }
        .header-left {
            width: 60%;
            vertical-align: middle;
        }
        .header-right {
            width: 40%;
            vertical-align: middle;
            text-align: right;
        }
        .clinic-name {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-type {
            font-size: 10.5px;
            font-weight: 700;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 2px 0 0 0;
        }
        .clinic-details {
            font-size: 8px;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.25;
        }
        .record-badge {
            display: inline-block;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 5px 10px;
            text-align: right;
        }
        .record-number {
            font-size: 11px;
            font-weight: bold;
            color: #065f46;
            margin: 0;
        }
        .record-date {
            font-size: 8.5px;
            color: #64748b;
            margin: 2px 0 0 0;
        }

        /* ═══ Títulos de Sección ═══ */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            color: #065f46;
            background-color: #f0fdf4;
            border-left: 3.5px solid #059669;
            border-top: 1px solid #d1fae5;
            border-right: 1px solid #d1fae5;
            border-bottom: 1px solid #d1fae5;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 0 4px 4px 0;
        }

        /* ═══ KPI Cards Table (Alineación Perfecta al 100%) ═══ */
        table.kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.kpi-table td.kpi-col-left {
            width: 50%;
            vertical-align: top;
            padding-right: 4px;
            padding-left: 0;
            padding-top: 0;
            padding-bottom: 6px;
        }
        table.kpi-table td.kpi-col-right {
            width: 50%;
            vertical-align: top;
            padding-left: 4px;
            padding-right: 0;
            padding-top: 0;
            padding-bottom: 6px;
        }
        .kpi-card {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-left: 3.5px solid #059669;
            border-radius: 4px;
            padding: 6px 10px;
        }
        .kpi-card.blue {
            border-left-color: #2563eb;
        }
        .kpi-card.purple {
            border-left-color: #8b5cf6;
        }
        .kpi-card.red {
            border-left-color: #ef4444;
        }
        .kpi-value {
            font-size: 14px;
            font-weight: bold;
            color: #065f46;
            margin-bottom: 2px;
        }
        .kpi-card.blue .kpi-value { color: #1d4ed8; }
        .kpi-card.purple .kpi-value { color: #6d28d9; }
        .kpi-card.red .kpi-value { color: #b91c1c; }
        .kpi-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        /* ═══ Tablas de Datos ═══ */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8.5px;
            border: 1px solid #cbd5e1;
        }
        table.data-table th {
            background-color: #065f46;
            color: #ffffff;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #065f46;
            text-align: left;
        }
        table.data-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .empty-state {
            padding: 12px;
            text-align: center;
            color: #64748b;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            font-size: 9px;
            margin-bottom: 8px;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5px;
            font-weight: bold;
            border-radius: 4px;
            color: #fff;
            text-transform: uppercase;
        }
        .badge-success { background-color: #10b981; }
        .badge-warning { background-color: #f59e0b; }
        .badge-danger { background-color: #ef4444; }

        /* Footer */
        .footer-disclaimer {
            text-align: center;
            color: #94a3b8;
            font-size: 7.5px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            margin-top: 15px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header Principal --}}
        <table class="header-table">
            <tr>
                <td class="header-left">
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" alt="Logo" style="max-height: 38px; margin-bottom: 3px;">
                    @endif
                    <h1 class="clinic-name">{{ $clinic->name ?? config('app.name', 'VETCORESSEN') }}</h1>
                    <p class="doc-type">{{ $t('report.executiveReport', 'Reporte Estadístico Ejecutivo') }}</p>
                    <div class="clinic-details">
                        @if($clinic && $clinic->address) {{ $clinic->address }} &bull; @endif
                        @if($clinic && $clinic->phone) Tel: {{ $clinic->phone }} &bull; @endif
                        @if($clinic && $clinic->email) {{ $clinic->email }} @endif
                    </div>
                </td>
                <td class="header-right">
                    <div class="record-badge">
                        <p class="record-number">{{ $periodoLabel }}</p>
                        <p class="record-date">{{ $t('report.range', 'Rango') }}: <strong>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</strong></p>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Resumen Financiero --}}
        <div class="section-title">{{ $t('report.financialSummary', 'Resumen Financiero y Operativo') }}</div>
        <table class="kpi-table">
            <tr>
                <td class="kpi-col-left">
                    <div class="kpi-card">
                        <div class="kpi-value">S/ {{ number_format($ventasPeriodo, 2) }}</div>
                        <div class="kpi-label">{{ $t('report.totalRevenue', 'Ingresos Totales') }} ({{ $totalVentasCount }} {{ $t('report.sales', 'Ventas') }})</div>
                    </div>
                </td>
                <td class="kpi-col-right">
                    <div class="kpi-card blue">
                        <div class="kpi-value">S/ {{ number_format($ticketPromedio, 2) }}</div>
                        <div class="kpi-label">{{ $t('report.averageTicket', 'Ticket Promedio por Venta') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Rendimiento de Citas --}}
        <div class="section-title">{{ $t('report.medicalSummary', 'Rendimiento de Gestión Médica') }}</div>
        <table class="kpi-table">
            <tr>
                <td class="kpi-col-left">
                    <div class="kpi-card blue">
                        <div class="kpi-value">{{ $totalCitas }}</div>
                        <div class="kpi-label">{{ $t('report.totalAppointments', 'Total Citas Registradas') }}</div>
                    </div>
                </td>
                <td class="kpi-col-right">
                    <div class="kpi-card">
                        <div class="kpi-value">{{ $citasCompletadas }}</div>
                        <div class="kpi-label">{{ $t('report.attendedAppointments', 'Citas Atendidas / Completadas') }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="kpi-col-left">
                    <div class="kpi-card purple">
                        <div class="kpi-value">{{ $citasPendientes }}</div>
                        <div class="kpi-label">{{ $t('report.pendingAppointments', 'Citas Pendientes / En Progreso') }}</div>
                    </div>
                </td>
                <td class="kpi-col-right">
                    <div class="kpi-card red">
                        <div class="kpi-value">{{ $citasCanceladas }}</div>
                        <div class="kpi-label">{{ $t('report.cancelledAppointments', 'Citas Canceladas') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Top Productos --}}
        @if(count($topDetalles) > 0)
        <div class="section-title">{{ $t('report.topProductsTitle', 'Top Productos y Servicios Más Vendidos') }}</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">#</th>
                    <th>{{ $t('form.product', 'Producto / Servicio') }}</th>
                    <th style="text-align: center; width: 80px;">{{ $t('report.qtySold', 'Cant. Vendida') }}</th>
                    <th style="text-align: right; width: 100px;">{{ $t('report.totalInvoiced', 'Total Facturado') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topDetalles as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $item->description }}</strong></td>
                    <td style="text-align: center;">{{ $item->total_qty }}</td>
                    <td style="text-align: right;"><strong>S/ {{ number_format($item->total_revenue, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Detalle de Ventas --}}
        <div class="section-title">{{ $t('report.salesRegister', 'Registro de Ventas del Periodo') }}</div>
        @if(count($sales) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">{{ $t('report.receiptNo', 'N° Recibo') }}</th>
                    <th style="width: 90px;">{{ $t('report.dateTime', 'Fecha y Hora') }}</th>
                    <th>{{ $t('report.client', 'Cliente') }}</th>
                    <th style="width: 90px;">{{ $t('report.paymentMethod', 'Método Pago') }}</th>
                    <th style="width: 70px; text-align: right;">{{ $t('report.total', 'Total') }}</th>
                    <th style="width: 65px; text-align: center;">{{ $t('report.status', 'Estado') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales->take(20) as $sale)
                @php
                    $statusClass = $sale->status === 'PAGADO' ? 'badge-success' : ($sale->status === 'PENDIENTE' ? 'badge-warning' : 'badge-danger');
                    $statusName = $t('payment.' . strtolower($sale->status), $sale->status);
                @endphp
                <tr>
                    <td><strong>#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                    <td>{{ $sale->cliente?->nombre_completo ?? $t('report.walkInCustomer', 'Cliente General') }}</td>
                    <td>{{ $t('payment.' . strtolower($sale->payment_method), str_replace('_', ' ', $sale->payment_method)) }}</td>
                    <td style="text-align: right;"><strong>S/ {{ number_format($sale->total, 2) }}</strong></td>
                    <td style="text-align: center;"><span class="badge {{ $statusClass }}">{{ $statusName }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($sales) > 20)
            <p style="font-size: 8px; color: #64748b; text-align: center; margin: 2px 0 6px 0;">{{ $t('report.showingFirst20', '(Mostrando las primeras 20 ventas. Use la exportación Excel para el archivo completo)') }}</p>
        @endif
        @else
        <div class="empty-state">{{ $t('report.noSalesPeriod', 'No se registraron ventas en el periodo seleccionado.') }}</div>
        @endif

        <div class="footer-disclaimer">
            {{ $t('report.generatedBy', 'Documento generado automáticamente por') }} {{ config('app.name', 'VETCORESSEN') }} {{ $t('misc.on_date', 'el') }} {{ now()->format('M d, Y h:i A') }}. &bull; {{ $t('report.confidentialReport', 'Reporte Confidencial') }}
        </div>
    </div>
</body>
</html>
