<!DOCTYPE html>
<html lang="{{ $lang ?? request()->query('lang', 'en') }}">
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
        $langCode = $lang ?? request()->query('lang', 'en');
        $jsonPath = public_path("locales/{$langCode}.json");
        if (!file_exists($jsonPath)) {
            $jsonPath = base_path("public/locales/{$langCode}.json");
        }
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
    $currency = $simboloMoneda ?? 'S/';
@endphp

    <meta charset="utf-8">
    <title>{{ $t('report.title', 'Executive Report') }} - {{ $periodoLabel }}</title>
    <style>
        @page {
            margin: 12mm 12mm 12mm 12mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            font-size: 8.5px;
            background-color: #ffffff;
        }
        .container {
            width: 100%;
        }
        
        /* ═══ Header Principal ═══ */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 2px solid #059669;
            padding-bottom: 6px;
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
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-type {
            font-size: 10px;
            font-weight: 700;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 2px 0 0 0;
        }
        .clinic-details {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.2;
        }
        .record-badge {
            display: inline-block;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 5px;
            padding: 4px 8px;
            text-align: right;
        }
        .record-number {
            font-size: 10px;
            font-weight: bold;
            color: #065f46;
            margin: 0;
        }
        .record-date {
            font-size: 7.5px;
            color: #64748b;
            margin: 2px 0 0 0;
        }

        /* ═══ Títulos de Sección ═══ */
        .section-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #065f46;
            background-color: #f0fdf4;
            border-left: 3px solid #059669;
            border-top: 1px solid #d1fae5;
            border-right: 1px solid #d1fae5;
            border-bottom: 1px solid #d1fae5;
            padding: 3px 6px;
            margin-top: 8px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 0 3px 3px 0;
        }

        /* ═══ KPI Cards Table (4 Columnas Equilibradas) ═══ */
        table.kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        table.kpi-table td {
            vertical-align: top;
            padding: 0 3px;
        }
        table.kpi-table td:first-child {
            padding-left: 0;
        }
        table.kpi-table td:last-child {
            padding-right: 0;
        }
        .kpi-card {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-left: 3.5px solid #059669;
            border-radius: 4px;
            padding: 5px 7px;
        }
        .kpi-card.blue { border-left-color: #2563eb; }
        .kpi-card.purple { border-left-color: #8b5cf6; }
        .kpi-card.amber { border-left-color: #f59e0b; }
        
        .kpi-value {
            font-size: 12px;
            font-weight: bold;
            color: #065f46;
            margin-bottom: 1px;
        }
        .kpi-card.blue .kpi-value { color: #1d4ed8; }
        .kpi-card.purple .kpi-value { color: #6d28d9; }
        .kpi-card.amber .kpi-value { color: #b45309; }
        
        .kpi-label {
            font-size: 7.5px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: 700;
        }
        .kpi-sub {
            font-size: 7px;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* ═══ Gráficos en 2 Columnas ═══ */
        table.charts-layout {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        table.charts-layout td.chart-box-left {
            width: 50%;
            vertical-align: top;
            padding-right: 4px;
            padding-left: 0;
        }
        table.charts-layout td.chart-box-right {
            width: 50%;
            vertical-align: top;
            padding-left: 4px;
            padding-right: 0;
        }
        .chart-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 6px 8px;
        }
        .chart-title {
            font-size: 8px;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Barras de Progreso Vectoriales */
        .bar-row {
            margin-bottom: 4px;
        }
        .bar-label-line {
            width: 100%;
            margin-bottom: 1.5px;
        }
        .bar-label-left {
            font-size: 7.5px;
            font-weight: 600;
            color: #475569;
            float: left;
        }
        .bar-label-right {
            font-size: 7.5px;
            font-weight: bold;
            color: #1e293b;
            float: right;
        }
        .clear {
            clear: both;
        }
        .bar-track {
            width: 100%;
            height: 6px;
            background-color: #f1f5f9;
            border-radius: 3px;
            overflow: hidden;
        }
        .bar-fill-green { background-color: #10b981; height: 100%; border-radius: 3px; }
        .bar-fill-blue { background-color: #3b82f6; height: 100%; border-radius: 3px; }
        .bar-fill-purple { background-color: #8b5cf6; height: 100%; border-radius: 3px; }
        .bar-fill-amber { background-color: #f59e0b; height: 100%; border-radius: 3px; }
        .bar-fill-red { background-color: #ef4444; height: 100%; border-radius: 3px; }

        /* Barra Segmentada de Citas */
        .multi-bar-track {
            width: 100%;
            height: 10px;
            background-color: #f1f5f9;
            border-radius: 4px;
            overflow: hidden;
            margin: 6px 0;
            display: table;
        }
        .multi-bar-seg {
            display: table-cell;
            height: 10px;
        }

        /* ═══ Tablas de Datos ═══ */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 8px;
            border: 1px solid #cbd5e1;
        }
        table.data-table th {
            background-color: #065f46;
            color: #ffffff;
            font-weight: bold;
            font-size: 7.5px;
            text-transform: uppercase;
            padding: 4px 5px;
            border: 1px solid #065f46;
            text-align: left;
        }
        table.data-table td {
            padding: 3.5px 5px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .empty-state {
            padding: 8px;
            text-align: center;
            color: #64748b;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            font-size: 8px;
            margin-bottom: 6px;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 1.5px 5px;
            font-size: 7px;
            font-weight: bold;
            border-radius: 3px;
            color: #fff;
            text-transform: uppercase;
        }
        .badge-success { background-color: #10b981; }
        .badge-warning { background-color: #f59e0b; }
        .badge-danger { background-color: #ef4444; }
        .badge-purple { background-color: #8b5cf6; }

        /* Footer */
        .footer-disclaimer {
            text-align: center;
            color: #94a3b8;
            font-size: 7px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            margin-top: 8px;
            line-height: 1.2;
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
                        <img src="{{ $logoSrc }}" alt="Logo" style="max-height: 32px; margin-bottom: 2px;">
                    @endif
                    <h1 class="clinic-name">{{ $clinic->name ?? config('app.name', 'VETCORESSEN') }}</h1>
                    <p class="doc-type">{{ $t('report.title', 'Executive Statistics & Operational Report') }}</p>
                    <div class="clinic-details">
                        @if($clinic && $clinic->address) {{ $clinic->address }} &bull; @endif
                        @if($clinic && $clinic->phone) Tel: {{ $clinic->phone }} &bull; @endif
                        @if($clinic && $clinic->email) {{ $clinic->email }} @endif
                    </div>
                </td>
                <td class="header-right">
                    <div class="record-badge">
                        <p class="record-number">{{ $periodoLabel }}</p>
                        <p class="record-date">{{ $t('report.custom', 'Period') }}: <strong>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</strong></p>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ═══ 1. Resumen Ejecutivo (KPIs) ═══ --}}
        <div class="section-title">{{ $t('report.subtitle', 'Executive Performance & Financial Summary') }}</div>
        <table class="kpi-table">
            <tr>
                <td style="width: 25%;">
                    <div class="kpi-card">
                        <div class="kpi-value">{{ $currency }} {{ number_format($ventasPeriodo, 2) }}</div>
                        <div class="kpi-label">{{ $t('report.totalRevenue', 'Total Revenue') }}</div>
                        <div class="kpi-sub">{{ $totalVentasCount }} {{ $t('report.sales', 'sales') }} ({{ ($porcentajeVentas >= 0 ? '+' : '') . number_format($porcentajeVentas, 1) }}%)</div>
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="kpi-card blue">
                        <div class="kpi-value">{{ $currency }} {{ number_format($ticketPromedio, 2) }}</div>
                        <div class="kpi-label">{{ $t('report.averageTicket', 'Average Ticket') }}</div>
                        <div class="kpi-sub">{{ $t('report.perTransaction', 'Per transaction') }}</div>
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="kpi-card purple">
                        <div class="kpi-value">{{ $citasCompletadas }} / {{ $totalCitas }}</div>
                        <div class="kpi-label">{{ $t('report.completedAppointments', 'Completed Appts') }}</div>
                        <div class="kpi-sub">{{ $totalCitas > 0 ? number_format(($citasCompletadas / $totalCitas) * 100, 1) : 0 }}% {{ $t('status.completed', 'rate') }}</div>
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="kpi-card amber">
                        <div class="kpi-value">{{ $productosStockBajo }}</div>
                        <div class="kpi-label">{{ $t('report.lowStockProducts', 'Low Stock Items') }}</div>
                        <div class="kpi-sub">{{ $currency }} {{ number_format($valorizacionInventario, 2) }} {{ $t('inventory.valuation', 'Valuation') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ═══ 2. Gráficos Visuales (Revenue Evolution & Appointments Breakdown) ═══ --}}
        <table class="charts-layout">
            <tr>
                {{-- Gráfico 1: Evolución de Ingresos --}}
                <td class="chart-box-left">
                    <div class="chart-card">
                        <div class="chart-title">{{ $t('report.salesEvol', 'Revenue Evolution') }}</div>
                        @php
                            $maxVenta = !empty($ventasChartData) ? max(max($ventasChartData), 1) : 1;
                            $displayVentas = array_slice($ventasChartData ?? [], 0, 8, true);
                        @endphp
                        @if(!empty($ventasChartData) && max($ventasChartData) > 0)
                            @foreach($displayVentas as $idx => $val)
                                @php
                                    $label = $ventasChartLabels[$idx] ?? ('Point ' . ($idx + 1));
                                    $pct = round(($val / $maxVenta) * 100);
                                @endphp
                                <div class="bar-row">
                                    <div class="bar-label-line">
                                        <span class="bar-label-left">{{ $label }}</span>
                                        <span class="bar-label-right">{{ $currency }} {{ number_format($val, 2) }}</span>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="bar-track">
                                        <div class="bar-fill-green" style="width: {{ max($pct, 2) }}%;"></div>
                                    </div>
                                </div>
                            @endforeach
                            @if(count($ventasChartData) > 8)
                                <p style="font-size: 6.5px; color: #94a3b8; margin: 2px 0 0 0; text-align: right;">+{{ count($ventasChartData) - 8 }} {{ $t('misc.morePoints', 'additional period dates') }}</p>
                            @endif
                        @else
                            <div class="empty-state" style="padding: 12px 4px; margin: 0;">{{ $t('report.noSalesInPeriod', 'No revenue activity in this period') }}</div>
                        @endif
                    </div>
                </td>

                {{-- Gráfico 2: Estado de Citas --}}
                <td class="chart-box-right">
                    <div class="chart-card">
                        <div class="chart-title">{{ $t('report.appointmentStatus', 'Appointments Status') }}</div>
                        @if($totalCitas > 0)
                            @php
                                $pctComp = round(($citasCompletadas / $totalCitas) * 100);
                                $pctPend = round(($citasPendientes / $totalCitas) * 100);
                                $pctCanc = max(0, 100 - $pctComp - $pctPend);
                            @endphp
                            {{-- Multi-Segment Bar --}}
                            <table class="multi-bar-track" style="margin-bottom: 8px;">
                                <tr>
                                    @if($pctComp > 0)
                                        <td class="multi-bar-seg" style="width: {{ $pctComp }}%; background-color: #10b981;"></td>
                                    @endif
                                    @if($pctPend > 0)
                                        <td class="multi-bar-seg" style="width: {{ $pctPend }}%; background-color: #8b5cf6;"></td>
                                    @endif
                                    @if($pctCanc > 0)
                                        <td class="multi-bar-seg" style="width: {{ $pctCanc }}%; background-color: #ef4444;"></td>
                                    @endif
                                </tr>
                            </table>

                            {{-- Detalle Estado --}}
                            <div class="bar-row">
                                <div class="bar-label-line">
                                    <span class="bar-label-left" style="color: #059669;">● {{ $t('status.completed', 'Completed') }}</span>
                                    <span class="bar-label-right">{{ $citasCompletadas }} ({{ $pctComp }}%)</span>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <div class="bar-row">
                                <div class="bar-label-line">
                                    <span class="bar-label-left" style="color: #7c3aed;">● {{ $t('status.pending', 'Pending / In Progress') }}</span>
                                    <span class="bar-label-right">{{ $citasPendientes }} ({{ $pctPend }}%)</span>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <div class="bar-row">
                                <div class="bar-label-line">
                                    <span class="bar-label-left" style="color: #dc2626;">● {{ $t('status.cancelled', 'Cancelled') }}</span>
                                    <span class="bar-label-right">{{ $citasCanceladas }} ({{ $pctCanc }}%)</span>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        @else
                            <div class="empty-state" style="padding: 12px 4px; margin: 0;">{{ $t('report.noAppointmentsInPeriod', 'No appointments in this period') }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- ═══ 3. Gráficos Visuales (Top Products & Payment Methods) ═══ --}}
        <table class="charts-layout">
            <tr>
                {{-- Gráfico 3: Top 5 Productos / Servicios --}}
                <td class="chart-box-left">
                    <div class="chart-card">
                        <div class="chart-title">{{ $t('report.topProducts', 'Top 5 Best-Selling Products & Services') }}</div>
                        @php
                            $maxProdRevenue = !empty($topProductosData) ? max(max($topProductosData), 1) : 1;
                        @endphp
                        @if(!empty($topDetalles) && count($topDetalles) > 0)
                            @foreach($topDetalles->take(5) as $item)
                                @php
                                    $pct = round(($item->total_revenue / $maxProdRevenue) * 100);
                                @endphp
                                <div class="bar-row">
                                    <div class="bar-label-line">
                                        <span class="bar-label-left" style="max-width: 65%; overflow: hidden; white-space: nowrap;">{{ substr($item->description, 0, 26) }}</span>
                                        <span class="bar-label-right">{{ $currency }} {{ number_format($item->total_revenue, 2) }}</span>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="bar-track">
                                        <div class="bar-fill-blue" style="width: {{ max($pct, 3) }}%;"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state" style="padding: 12px 4px; margin: 0;">{{ $t('report.noSalesInPeriod', 'No products sold in this period') }}</div>
                        @endif
                    </div>
                </td>

                {{-- Gráfico 4: Métodos de Pago --}}
                <td class="chart-box-right">
                    <div class="chart-card">
                        <div class="chart-title">{{ $t('report.paymentMethods', 'Payment Methods Breakdown') }}</div>
                        @php
                            $totalPagos = !empty($pagosChartData) ? array_sum($pagosChartData) : 0;
                            $colorsMap = [0 => 'bar-fill-green', 1 => 'bar-fill-blue', 2 => 'bar-fill-purple', 3 => 'bar-fill-amber'];
                        @endphp
                        @if($totalPagos > 0)
                            @foreach($pagosChartLabels as $idx => $methodName)
                                @php
                                    $mAmount = $pagosChartData[$idx] ?? 0;
                                    $pct = $totalPagos > 0 ? round(($mAmount / $totalPagos) * 100) : 0;
                                    $fillClass = $colorsMap[$idx % 4];
                                    $methodTrans = $t('payment.' . strtolower(str_replace([' ', '/', '-'], '_', $methodName)), $methodName);
                                @endphp
                                <div class="bar-row">
                                    <div class="bar-label-line">
                                        <span class="bar-label-left">{{ $methodTrans }}</span>
                                        <span class="bar-label-right">{{ $currency }} {{ number_format($mAmount, 2) }} ({{ $pct }}%)</span>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="bar-track">
                                        <div class="{{ $fillClass }}" style="width: {{ max($pct, 2) }}%;"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state" style="padding: 12px 4px; margin: 0;">{{ $t('report.noSalesInPeriod', 'No collections registered') }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- ═══ 4. Diagnósticos Clínicos Frecuentes ═══ --}}
        @if(!empty($topDiagnosticos) && count($topDiagnosticos) > 0)
        <div class="section-title">{{ $t('report.diagnosis', 'Most Frequent Medical Diagnoses') }}</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">#</th>
                    <th>{{ $t('report.diagnosis', 'Presumptive Diagnosis / Condition') }}</th>
                    <th style="width: 100px; text-align: center;">{{ $t('report.totalRecords', 'Cases Recorded') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topDiagnosticos as $dIndex => $diag)
                <tr>
                    <td style="text-align: center;">{{ $dIndex + 1 }}</td>
                    <td><strong>{{ $diag->diagnostico_presuntivo }}</strong></td>
                    <td style="text-align: center;"><strong>{{ $diag->total }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- ═══ 5. Registro de Ventas del Período ═══ --}}
        <div class="section-title">{{ $t('report.periodSales', 'Sales Transactions Register') }}</div>
        @if(count($sales) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">{{ $t('report.receiptNo', 'Receipt No.') }}</th>
                    <th style="width: 85px;">{{ $t('report.dateTime', 'Date / Time') }}</th>
                    <th>{{ $t('report.client', 'Customer') }}</th>
                    <th style="width: 85px;">{{ $t('report.paymentMethod', 'Payment Method') }}</th>
                    <th style="width: 70px; text-align: right;">{{ $t('report.total', 'Total') }}</th>
                    <th style="width: 65px; text-align: center;">{{ $t('report.status', 'Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales->take(25) as $sale)
                @php
                    $statusClass = $sale->status === 'PAGADO' ? 'badge-success' : ($sale->status === 'PENDIENTE' ? 'badge-warning' : 'badge-danger');
                    $statusName = $t('status.' . strtolower($sale->status), $sale->status);
                    $payRaw = $sale->payment_method ?? '';
                    $payKey = 'payment.' . strtolower(str_replace([' ', '/', '-'], '_', $payRaw));
                    $payDisplay = $t($payKey, str_replace('_', ' ', $payRaw ?: '-'));
                @endphp
                <tr>
                    <td><strong>#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                    <td>{{ $sale->cliente?->nombre_completo ?? $t('report.walkInCustomer', 'Walk-in Customer') }}</td>
                    <td>{{ $payDisplay }}</td>
                    <td style="text-align: right;"><strong>{{ $currency }} {{ number_format($sale->total, 2) }}</strong></td>
                    <td style="text-align: center;"><span class="badge {{ $statusClass }}">{{ $statusName }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($sales) > 25)
            <p style="font-size: 7px; color: #64748b; text-align: center; margin: 2px 0 4px 0;">{{ $t('report.showingFirst20', '(Displaying first 25 sales of the period)') }}</p>
        @endif
        @else
        <div class="empty-state">{{ $t('report.noSalesInPeriod', 'No sales registered in this period.') }}</div>
        @endif

        {{-- Footer --}}
        <div class="footer-disclaimer">
            {{ $t('report.generatedBy', 'Document automatically generated by') }} {{ config('app.name', 'VETCORESSEN') }} &bull; {{ now()->format('M d, Y h:i A') }} &bull; {{ $t('report.confidentialReport', 'Confidential Management Report') }}
        </div>
    </div>
</body>
</html>
