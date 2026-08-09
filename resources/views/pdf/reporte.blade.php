<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte Estadístico - {{ strtoupper(str_replace('_', ' ', $periodo)) }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; margin: 0; padding: 0; font-size: 13px; line-height: 1.5; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* Header */
        .header { display: table; width: 100%; border-bottom: 2px solid #059669; padding-bottom: 20px; margin-bottom: 30px; }
        .header-logo { display: table-cell; vertical-align: middle; width: 60%; }
        .header-logo h1 { margin: 0; color: #047857; font-size: 26px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header-logo p { margin: 5px 0 0 0; color: #4b5563; font-size: 14px; }
        .header-info { display: table-cell; vertical-align: bottom; width: 40%; text-align: right; }
        .header-info p { margin: 2px 0; color: #4b5563; }
        .header-info strong { color: #111827; }
        
        .section { margin-bottom: 35px; page-break-inside: avoid; }
        .section-title { font-size: 16px; font-weight: bold; color: #059669; border-bottom: 1px solid #d1d5db; padding-bottom: 5px; margin-bottom: 15px; margin-top: 25px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* KPI Grid */
        .kpi-grid { width: 100%; border-collapse: separate; border-spacing: 15px 0; margin-bottom: 20px; margin-left: -15px; margin-right: -15px; }
        .kpi-grid td { width: 50%; padding: 15px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; vertical-align: top; }
        .kpi-value { font-size: 28px; font-weight: bold; color: #065f46; margin-bottom: 5px; }
        .kpi-label { font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
        
        /* Data Tables */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; }
        table.data-table th { background-color: #f3f4f6; color: #374151; font-weight: bold; font-size: 12px; text-transform: uppercase; }
        table.data-table td { font-size: 13px; color: #1f2937; }
        table.data-table tr:nth-child(even) { background-color: #f9fafb; }
        
        .empty-state { padding: 20px; text-align: center; color: #6b7280; background-color: #f9fafb; border: 1px dashed #d1d5db; border-radius: 8px; }
        
        /* Badges */
        .badge { display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: bold; border-radius: 4px; color: #fff; text-transform: uppercase; }
        .badge-success { background-color: #10b981; }
        .badge-warning { background-color: #f59e0b; }
        .badge-danger { background-color: #ef4444; }
        .badge-info { background-color: #3b82f6; }
        .badge-default { background-color: #6b7280; }
        
        /* Footer */
        .footer { margin-top: 50px; text-align: center; color: #9ca3af; font-size: 11px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-logo">
                <h1>{{ config('app.name', 'VETCORESSEN') }}</h1>
                <p>Reporte Estadístico Gerencial</p>
            </div>
            <div class="header-info">
                <p>Periodo: <strong>{{ strtoupper(str_replace('_', ' ', $periodo)) }}</strong></p>
                <p>Generado el: <strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Resumen Financiero</div>
            <table class="kpi-grid">
                <tr>
                    <td>
                        <div class="kpi-value">S/ {{ number_format($ventasPeriodo, 2) }}</div>
                        <div class="kpi-label">Ingresos Totales del Periodo</div>
                    </td>
                    <td>
                        <div class="kpi-value">S/ {{ number_format($ticketPromedio, 2) }}</div>
                        <div class="kpi-label">Ticket Promedio por Venta</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Rendimiento de Gestión Médica</div>
            <table class="kpi-grid">
                <tr>
                    <td>
                        <div class="kpi-value" style="color: #1d4ed8;">{{ collect($citasChartData)->sum() }}</div>
                        <div class="kpi-label">Total Citas Registradas</div>
                    </td>
                    <td>
                        <div class="kpi-value" style="color: #059669;">{{ $citasCompletadas }}</div>
                        <div class="kpi-label">Citas Atendidas / Completadas</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="kpi-value" style="color: #8b5cf6;">{{ $citasNuevas }}</div>
                        <div class="kpi-label">Citas Pendientes / Confirmadas</div>
                    </td>
                    <td>
                        <div class="kpi-value" style="color: #b91c1c;">{{ $citasCanceladas }}</div>
                        <div class="kpi-label">Citas Canceladas</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Registro Detallado de Ventas</div>
            @if(count($sales) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nº Recibo</th>
                        <th>Fecha y Hora</th>
                        <th>Cliente</th>
                        <th>Método Pago</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
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
            @else
            <div class="empty-state">No se registraron ventas en el periodo seleccionado.</div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Registro Detallado de Citas</div>
            @if(count($appointments) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nº Cita</th>
                        <th>Fecha y Hora</th>
                        <th>Paciente (Mascota)</th>
                        <th>Propietario</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appt)
                    @php
                        $statusColors = [
                            'PENDIENTE' => 'badge-warning',
                            'CONFIRMADA' => 'badge-info',
                            'EN_PROGRESO' => 'badge-default',
                            'COMPLETADA' => 'badge-success',
                            'CANCELADA' => 'badge-danger',
                            'EMERGENCIA' => 'badge-danger',
                        ];
                        $badgeClass = $statusColors[$appt->status] ?? 'badge-default';
                    @endphp
                    <tr>
                        <td><strong>#{{ str_pad($appt->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>{{ $appt->fecha_hora ? $appt->fecha_hora->format('d/m/Y H:i') : '-' }}</td>
                        <td><strong>{{ $appt->mascota?->name ?? '-' }}</strong></td>
                        <td>{{ $appt->cliente?->nombre_completo ?? '-' }}</td>
                        <td><span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $appt->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">No se registraron citas en el periodo seleccionado.</div>
            @endif
        </div>

        <div class="footer">
            Documento generado automáticamente por {{ config('app.name', 'VETCORESSEN') }} - Reporte Confidencial
        </div>
    </div>
</body>
</html>
