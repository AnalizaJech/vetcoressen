<!DOCTYPE html>
<html lang="es">
<head>
@php
    $clinic = \App\Models\Clinic::first();
    $logoPath = $clinic && $clinic->logo ? public_path('storage/' . $clinic->logo) : public_path('favicon.svg');
    $logoSrc = '';
    if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoMime = mime_content_type($logoPath);
        $logoSrc = 'data:' . $logoMime . ';base64,' . $logoData;
    }
@endphp

    <meta charset="UTF-8">
    <title>Cita #{{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; line-height: 1.5; margin: 0; padding: 0; font-size: 13px; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* Header */
        .header { display: table; width: 100%; border-bottom: 2px solid #059669; padding-bottom: 20px; margin-bottom: 30px; }
        .header-logo { display: table-cell; vertical-align: middle; width: 60%; }
        .header-logo h1 { margin: 0; color: #047857; font-size: 26px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header-logo p { margin: 5px 0 0 0; color: #4b5563; font-size: 14px; }
        .header-info { display: table-cell; vertical-align: bottom; width: 40%; text-align: right; }
        .header-info p { margin: 2px 0; color: #4b5563; }
        .header-info strong { color: #111827; }

        /* Titles */
        .section-title { font-size: 16px; font-weight: bold; color: #059669; border-bottom: 1px solid #d1d5db; padding-bottom: 5px; margin-bottom: 15px; margin-top: 25px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Grid Tables */
        table.grid { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.grid td { padding: 6px 0; vertical-align: top; }
        table.grid td.label { width: 25%; color: #6b7280; font-weight: bold; }
        table.grid td.value { width: 75%; color: #111827; }
        
        /* Content boxes */
        .content-box { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; margin-bottom: 15px; }
        .content-box h4 { margin: 0 0 8px 0; color: #374151; font-size: 13px; text-transform: uppercase; }
        .content-box p { margin: 0; white-space: pre-wrap; color: #1f2937; }

        /* Badges */
        .badge { display: inline-block; padding: 6px 12px; font-size: 12px; font-weight: bold; border-radius: 6px; color: #fff; text-transform: uppercase; letter-spacing: 1px; }
        .badge-pending { background-color: #f59e0b; }
        .badge-confirmed { background-color: #3b82f6; }
        .badge-in-progress { background-color: #8b5cf6; }
        .badge-completed { background-color: #10b981; }
        .badge-cancelled { background-color: #ef4444; }
        .badge-emergency { background-color: #b91c1c; }
        
        .status-box { padding: 15px; border-radius: 8px; margin-bottom: 30px; text-align: center; background-color: #f0fdf4; border: 1px solid #bbf7d0; }
        .status-label { font-weight: bold; color: #065f46; font-size: 14px; text-transform: uppercase; margin-right: 15px; }
        
        /* Footer */
        .footer { margin-top: 50px; text-align: center; color: #9ca3af; font-size: 11px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-logo">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo" style="max-height: 50px; margin-bottom: 5px;">
                @endif
                <h1 style="font-size: 20px;">{{ $clinic->name ?? config('app.name', 'VETCORESSEN') }}</h1>
                <p>Detalles de la Cita Médica Veterinaria</p>
            </div>
            <div class="header-info">
                <p>Nº de Cita: <strong>#{{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                <p>Fecha Programada: <strong>{{ $cita->fecha_hora ? $cita->fecha_hora->format('d/m/Y H:i') : 'No especificada' }}</strong></p>
            </div>
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
            <span class="status-label">ESTADO DE LA CITA:</span> 
            <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $cita->status) }}</span>
        </div>

        <div class="section-title">Información General</div>
        <table class="grid">
            <tr>
                <td class="label">Cliente (Propietario):</td>
                <td class="value">{{ $cita->cliente->nombre_completo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">DNI/RUC Cliente:</td>
                <td class="value">{{ $cita->cliente->numero_documento ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Mascota (Paciente):</td>
                <td class="value"><strong>{{ $cita->mascota->name ?? 'N/A' }}</strong> (Especie: {{ $cita->mascota->species ?? 'N/A' }}, Raza: {{ $cita->mascota->breed ?? 'N/A' }})</td>
            </tr>
        </table>

        <div class="section-title">Detalles Clínicos de la Cita</div>
        <table class="grid">
            <tr>
                <td class="label">Veterinario Asignado:</td>
                <td class="value"><strong>{{ $cita->veterinario->name ?? 'No asignado' }} {{ $cita->veterinario->last_name ?? '' }}</strong></td>
            </tr>
        </table>
        
        <div class="content-box">
            <h4>Motivo / Razón de la Consulta</h4>
            <p>{{ $cita->reason ?? 'No especificado' }}</p>
        </div>

        @if($cita->notes)
        <div class="content-box">
            <h4>Notas Adicionales Previas</h4>
            <p>{{ $cita->notes }}</p>
        </div>
        @endif

        <div class="footer">
            Este documento es un comprobante de cita generado por el sistema {{ config('app.name', 'VETCORESSEN') }}.<br>
            Documento generado el: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
