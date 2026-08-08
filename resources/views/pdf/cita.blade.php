<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cita #{{ $cita->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; line-height: 1.6; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 3px solid #10b981; }
        .header h1 { margin: 0; color: #065f46; font-size: 28px; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 5px 0 0; color: #4b5563; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        
        .section { margin-bottom: 30px; background-color: #f9fafb; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .section-title { font-size: 16px; font-weight: bold; border-bottom: 2px solid #e5e7eb; margin-bottom: 15px; padding-bottom: 8px; color: #047857; text-transform: uppercase; letter-spacing: 1px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; text-align: left; vertical-align: top; border-bottom: 1px solid #f3f4f6; }
        th { width: 35%; font-weight: bold; color: #374151; font-size: 13px; text-transform: uppercase; }
        td { color: #1f2937; font-size: 14px; }
        
        .badge { display: inline-block; padding: 6px 12px; font-size: 12px; font-weight: bold; border-radius: 6px; color: #fff; text-transform: uppercase; letter-spacing: 1px; }
        .badge-pending { background-color: #f59e0b; }
        .badge-confirmed { background-color: #3b82f6; }
        .badge-in-progress { background-color: #8b5cf6; }
        .badge-completed { background-color: #10b981; }
        .badge-cancelled { background-color: #ef4444; }
        .badge-emergency { background-color: #b91c1c; }
        
        .status-box { padding: 15px; border-radius: 8px; margin-bottom: 30px; text-align: center; background-color: #f0fdf4; border: 1px solid #bbf7d0; display: flex; align-items: center; justify-content: center; gap: 15px; }
        .status-label { font-weight: bold; color: #065f46; font-size: 14px; text-transform: uppercase; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ config('app.name', 'VETCORESSEN') }}</h1>
        <p>Detalles de la Cita Médica Veterinaria</p>
    </div>

    @php
        $statusColors = [
            'PENDIENTE' => 'badge-pending',
            'CONFIRMADA' => 'badge-confirmed',
            'EN_PROGRESO' => 'badge-in-progress',
            'COMPLETADA' => 'badge-completed',
            'CANCELADA' => 'badge-cancelled',
            'EMERGENCIA' => 'badge-emergency',
        ];
        $badgeClass = $statusColors[$cita->status] ?? 'badge-pending';
    @endphp

    <div class="status-box">
        <span class="status-label">ESTADO DE LA CITA:</span> <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $cita->status) }}</span>
    </div>

    <div class="section">
        <div class="section-title">Información General</div>
        <table>
            <tr>
                <th>Nº de Cita:</th>
                <td><strong>#{{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
            </tr>
            <tr>
                <th>Fecha y Hora:</th>
                <td>{{ $cita->fecha_hora ? $cita->fecha_hora->format('d/m/Y H:i') : 'No especificada' }}</td>
            </tr>
            <tr>
                <th>Cliente:</th>
                <td>{{ $cita->cliente->nombre_completo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>DNI/RUC Cliente:</th>
                <td>{{ $cita->cliente->numero_documento ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Mascota:</th>
                <td><strong>{{ $cita->mascota->name ?? 'N/A' }}</strong> (Especie: {{ $cita->mascota->species ?? 'N/A' }}, Raza: {{ $cita->mascota->breed ?? 'N/A' }})</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalles Clínicos</div>
        <table>
            <tr>
                <th>Veterinario Asignado:</th>
                <td>{{ $cita->veterinario->name ?? 'No asignado' }}</td>
            </tr>
            <tr>
                <th>Motivo / Razón:</th>
                <td>{{ $cita->reason ?? 'No especificado' }}</td>
            </tr>
            <tr>
                <th>Notas Adicionales:</th>
                <td>{{ $cita->notes ?? 'Sin notas adicionales' }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Este documento es un comprobante de cita generado por el sistema {{ config('app.name', 'VETCORESSEN') }}.</p>
        <p>Documento generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

</body>
</html>
