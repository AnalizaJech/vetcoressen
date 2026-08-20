<!DOCTYPE html>
<html lang="es">
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
@endphp

    <meta charset="utf-8">
    <title>Reporte Estadístico - {{ strtoupper(str_replace('_', ' ', $periodo)) }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; margin: 0; padding: 0; font-size: 12px; line-height: 1.5; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* Header */
        .header { display: table; width: 100%; border-bottom: 2px solid #059669; padding-bottom: 15px; margin-bottom: 20px; }
        .header-logo { display: table-cell; vertical-align: middle; width: 60%; }
        .header-logo h1 { margin: 0; color: #047857; font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header-logo p { margin: 3px 0 0 0; color: #4b5563; font-size: 13px; }
        .header-info { display: table-cell; vertical-align: bottom; width: 40%; text-align: right; }
        .header-info p { margin: 2px 0; color: #4b5563; font-size: 11px; }
        .header-info strong { color: #111827; }
        
        .section { margin-bottom: 25px; page-break-inside: avoid; }
        .section-title { font-size: 14px; font-weight: bold; color: #059669; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; margin-bottom: 12px; margin-top: 15px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* KPI Grid */
        .kpi-grid { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-bottom: 15px; margin-left: -10px; margin-right: -10px; }
        .kpi-grid td { width: 50%; padding: 12px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; vertical-align: top; }
        .kpi-value { font-size: 22px; font-weight: bold; color: #065f46; margin-bottom: 4px; }
        .kpi-label { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
        
        /* Data Tables */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.data-table th, table.data-table td { border: 1px solid #e5e7eb; padding: 7px 9px; text-align: left; }
        table.data-table th { background-color: #f3f4f6; color: #374151; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        table.data-table td { font-size: 11px; color: #1f2937; }
        table.data-table tr:nth-child(even) { background-color: #f9fafb; }
        
        .empty-state { padding: 15px; text-align: center; color: #6b7280; background-color: #f9fafb; border: 1px dashed #d1d5db; border-radius: 6px; }
        
        /* Badges */
        .badge { display: inline-block; padding: 2px 6px; font-size: 10px; font-weight: bold; border-radius: 4px; color: #fff; text-transform: uppercase; }
        .badge-success { background-color: #10b981; }
        .badge-warning { background-color: #f59e0b; }
        .badge-danger { background-color: #ef4444; }
        .badge-info { background-color: #3b82f6; }
        .badge-default { background-color: #6b7280; }
        
        /* Footer */
        .footer { margin-top: 30px; text-align: center; color: #9ca3af; font-size: 10px; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-logo">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo" style="max-height: 45px; margin-bottom: 4px;">
                @endif
                <h1>{{ $clinic->name ?? config('app.name', 'VETCORESSEN') }}</h1>
                <p>Reporte Estadístico Ejecutivo</p>
            </div>
            <div class="header-info">
                <p>Periodo: <strong>{{ strtoupper(str_replace('_', ' ', $periodo)) }}</strong></p>
                <p>Rango: <strong>{{ $startDate->format('d/m/Y') }} al {{ $endDate->format('d/m/Y') }}</strong></p>
                <p>Generado el: <strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
            </div>
        </div>

        {{-- Resumen Financiero --}}
        <div class="section">
            <div class="section-title">Resumen Financiero y Operativo</div>
            <table class="kpi-grid">
                <tr>
                    <td>
                        <div class="kpi-value">S/ {{ number_format($ventasPeriodo, 2) }}</div>
                        <div class="kpi-label">Ingresos Totales ({{ $totalVentasCount }} Ventas)</div>
                    </td>
                    <td>
                        <div class="kpi-value">S/ {{ number_format($ticketPromedio, 2) }}</div>
                        <div class="kpi-label">Ticket Promedio por Venta</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Rendimiento de Citas --}}
        <div class="section">
            <div class="section-title">Rendimiento de Gestión Médica</div>
            <table class="kpi-grid">
                <tr>
                    <td>
                        <div class="kpi-value" style="color: #1d4ed8;">{{ $totalCitas }}</div>
                        <div class="kpi-label">Total Citas Registradas</div>
                    </td>
                    <td>
                        <div class="kpi-value" style="color: #059669;">{{ $citasCompletadas }}</div>
                        <div class="kpi-label">Citas Atendidas / Completadas</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="kpi-value" style="color: #8b5cf6;">{{ $citasPendientes }}</div>
                        <div class="kpi-label">Citas Pendientes / En Progreso</div>
                    </td>
                    <td>
                        <div class="kpi-value" style="color: #b91c1c;">{{ $citasCanceladas }}</div>
                        <div class="kpi-label">Citas Canceladas</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Top Productos --}}
        @if(count($topDetalles) > 0)
        <div class="section">
            <div class="section-title">Top Productos y Servicios Más Vendidos</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Producto / Servicio</th>
                        <th style="text-align: center; width: 100px;">Cant. Vendida</th>
                        <th style="text-align: right; width: 120px;">Total Facturado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topDetalles as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $item->description }}</strong></td>
                        <td style="text-align: center;">{{ $item->total_qty }}</td>
                        <td style="text-align: right;"><strong>S/ {{ number_format($item->total_revenue, 2) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Detalle de Ventas --}}
        <div class="section">
            <div class="section-title">Registro de Ventas del Periodo</div>
            @if(count($sales) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Recibo</th>
                        <th>Fecha y Hora</th>
                        <th>Cliente</th>
                        <th>Método Pago</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales->take(25) as $sale)
                    @php
                        $statusClass = $sale->status === 'PAGADO' ? 'badge-success' : ($sale->status === 'PENDIENTE' ? 'badge-warning' : 'badge-danger');
                    @endphp
                    <tr>
                        <td><strong>#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->cliente?->nombre_completo ?? 'Cliente General' }}</td>
                        <td>{{ str_replace('_', ' ', $sale->payment_method) }}</td>
                        <td><strong>S/ {{ number_format($sale->total, 2) }}</strong></td>
                        <td><span class="badge {{ $statusClass }}">{{ $sale->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if(count($sales) > 25)
                <p style="font-size: 10px; color: #6b7280; text-align: center;">(Mostrando las primeras 25 ventas. Use la exportación CSV para el archivo completo)</p>
            @endif
            @else
            <div class="empty-state">No se registraron ventas en el periodo seleccionado.</div>
            @endif
        </div>

        <div class="footer">
            Documento generado automáticamente por {{ config('app.name', 'VETCORESSEN') }} - Reporte Confidencial
        </div>
    </div>
</body>
</html>
