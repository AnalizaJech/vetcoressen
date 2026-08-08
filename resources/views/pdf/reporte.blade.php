<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Estadístico</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #059669; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #059669; font-size: 24px; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 14px; }
        
        .section { margin-bottom: 30px; }
        .section-title { font-size: 18px; color: #1f2937; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 15px; }
        
        .kpi-grid { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .kpi-grid td { width: 50%; padding: 15px; border: 1px solid #e5e7eb; vertical-align: top; }
        .kpi-value { font-size: 24px; font-weight: bold; color: #111827; }
        .kpi-label { font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 5px; }
        
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>VETCORESSEN</h1>
        <p>Reporte Estadístico - {{ strtoupper(str_replace('_', ' ', $periodo)) }}</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Ventas y Finanzas</div>
        <table class="kpi-grid">
            <tr>
                <td>
                    <div class="kpi-value">S/ {{ number_format($ventasPeriodo, 2) }}</div>
                    <div class="kpi-label">Ingresos del Periodo</div>
                </td>
                <td>
                    <div class="kpi-value">S/ {{ number_format($ticketPromedio, 2) }}</div>
                    <div class="kpi-label">Ticket Promedio</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Rendimiento de Citas</div>
        <table class="kpi-grid">
            <tr>
                <td>
                    <div class="kpi-value">{{ $citasCompletadas }}</div>
                    <div class="kpi-label">Citas Completadas</div>
                </td>
                <td>
                    <div class="kpi-value">{{ $citasCanceladas }}</div>
                    <div class="kpi-label">Citas Canceladas</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="kpi-value">{{ $citasNuevas }}</div>
                    <div class="kpi-label">Nuevas Reservas</div>
                </td>
                <td>
                    <div class="kpi-value">{{ collect($citasChartData)->sum() }}</div>
                    <div class="kpi-label">Total Citas en el Periodo</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalle de Ventas</div>
        @if(count($sales) > 0)
        <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f3f4f6; text-align: left;">
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">ID</th>
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">Fecha</th>
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">Cliente</th>
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">Método Pago</th>
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">Total</th>
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $sale->cliente?->nombre_completo ?? 'Cliente General' }}</td>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ str_replace('_', ' ', $sale->payment_method) }}</td>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">S/ {{ number_format($sale->total, 2) }}</td>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $sale->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="font-size: 12px; color: #6b7280;">No se registraron ventas en este periodo.</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Detalle de Citas</div>
        @if(count($appointments) > 0)
        <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f3f4f6; text-align: left;">
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">ID</th>
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">Fecha/Hora</th>
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">Cliente</th>
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">Mascota</th>
                    <th style="padding: 8px; border: 1px solid #e5e7eb;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appt)
                <tr>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ str_pad($appt->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $appt->fecha_hora ? $appt->fecha_hora->format('d/m/Y H:i') : '-' }}</td>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $appt->cliente?->nombre_completo ?? '-' }}</td>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $appt->mascota?->name ?? '-' }}</td>
                    <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $appt->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="font-size: 12px; color: #6b7280;">No se registraron citas en este periodo.</p>
        @endif
    </div>

    <div class="footer">
        Documento generado automáticamente por Vetcoressen - Reporte Profesional.
    </div>
</body>
</html>
