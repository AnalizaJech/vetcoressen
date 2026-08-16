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
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; line-height: 1.3; margin: 0; padding: 0; font-size: 11px; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; }

        /* Header — mismo estilo que historia clínica */
        .header { display: table; width: 100%; border-bottom: 2px solid #059669; padding-bottom: 10px; margin-bottom: 15px; }
        .header-logo { display: table-cell; vertical-align: middle; width: 50%; }
        .header-logo h1 { margin: 0; color: #047857; font-size: 20px; font-weight: bold; text-transform: uppercase; }
        .header-logo p { margin: 3px 0 0 0; color: #4b5563; font-size: 12px; }
        .header-info { display: table-cell; vertical-align: bottom; width: 50%; text-align: right; }
        .header-info p { margin: 1px 0; color: #4b5563; }
        .header-info strong { color: #111827; }

        /* Títulos de sección — barra sólida verde (como historia clínica) */
        .section-title { font-size: 13px; font-weight: bold; color: #ffffff; background-color: #059669; padding: 4px 8px; margin-bottom: 8px; margin-top: 15px; text-transform: uppercase; border-radius: 3px; }

        /* Tablas de datos (grid 4 columnas) */
        table.grid { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.grid td { padding: 4px 6px; vertical-align: top; border: 1px solid #e5e7eb; }
        table.grid td.label { background-color: #f9fafb; color: #4b5563; font-weight: bold; font-size: 10px; text-transform: uppercase; }

        /* Layout de 2 columnas */
        table.layout { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.layout > tbody > tr > td { padding: 0; vertical-align: top; }
        table.layout > tbody > tr > td:first-child { padding-right: 5px; }
        table.layout > tbody > tr > td:last-child { padding-left: 5px; }

        /* Cajas de contenido */
        .content-box { border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; margin-bottom: 8px; background-color: #f9fafb; }
        .content-box h4 { margin: 0 0 4px 0; color: #374151; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; }
        .content-box p { margin: 0; white-space: pre-wrap; color: #1f2937; }

        /* Badge de estado */
        .badge { display: inline-block; padding: 4px 10px; font-size: 10px; font-weight: bold; border-radius: 4px; color: #fff; text-transform: uppercase; letter-spacing: 1px; }
        .badge-pending { background-color: #f59e0b; }
        .badge-confirmed { background-color: #3b82f6; }
        .badge-in-progress { background-color: #8b5cf6; }
        .badge-completed { background-color: #10b981; }
        .badge-cancelled { background-color: #ef4444; }
        .badge-emergency { background-color: #b91c1c; }

        /* Caja de estado integrada */
        .status-row { display: table; width: 100%; margin-bottom: 12px; }
        .status-row td { padding: 6px 8px; border: 1px solid #e5e7eb; vertical-align: middle; }
        .status-row td.label { background-color: #f0fdf4; font-weight: bold; color: #065f46; font-size: 11px; text-transform: uppercase; width: 25%; text-align: right; }
        .status-row td.value { text-align: left; }

        /* Tabla de datos adicionales */
        table.compact { width: 100%; border-collapse: collapse; }
        table.compact th, table.compact td { border: 1px solid #d1d5db; padding: 4px 6px; text-align: left; }
        table.compact th { background-color: #374151; color: #ffffff; font-weight: bold; font-size: 10px; text-transform: uppercase; }

        /* Footer */
        .footer { margin-top: 30px; padding-top: 10px; position: relative; page-break-inside: avoid; }
        .footer-text { text-align: center; color: #9ca3af; font-size: 9px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Cabecera — idéntica al estilo del reporte clínico --}}
        <div class="header">
            <div class="header-logo">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo" style="max-height: 40px; margin-bottom: 4px;">
                @endif
                <h1>{{ $clinic->name ?? config('app.name', 'VETCORESSEN') }}</h1>
                <p>Comprobante de Cita Médica</p>
            </div>
            <div class="header-info">
                <p>Nº de Cita: <strong>#{{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                <p>Fecha Programada: <strong>{{ $cita->fecha_hora ? $cita->fecha_hora->format('d/m/Y H:i') : 'No especificada' }}</strong></p>
            </div>
        </div>

        {{-- Estado de la cita — integrado como fila compacta --}}
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

            $statusLabels = [
                'PENDIENTE' => 'Pendiente',
                'CONFIRMADA' => 'Confirmada',
                'EN_PROGRESO' => 'En Progreso',
                'COMPLETADA' => 'Completada',
                'CANCELADA' => 'Cancelada',
                'EMERGENCIA' => 'Emergencia',
            ];
            $statusLabel = $statusLabels[$cita->status] ?? $cita->status;
        @endphp

        <table class="grid" style="margin-bottom: 12px;">
            <tr>
                <td class="label" style="width: 20%; background-color: #f0fdf4; color: #065f46; text-align: right;">Estado de la Cita:</td>
                <td style="width: 80%;"><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
            </tr>
        </table>

        {{-- Información General — grid 4 columnas (2 pares label/value) --}}
        <div class="section-title">Información General</div>
        <table class="grid">
            <tr>
                <td class="label" style="width: 15%">Propietario:</td>
                <td style="width: 35%"><strong>{{ $cita->cliente->nombre_completo ?? 'N/A' }}</strong></td>
                <td class="label" style="width: 15%">DNI/RUC:</td>
                <td style="width: 35%">{{ $cita->cliente->numero_documento ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Teléfono:</td>
                <td>{{ $cita->cliente->phone ?? 'N/A' }}</td>
                <td class="label">Email:</td>
                <td>{{ $cita->cliente->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Dirección:</td>
                <td colspan="3">{{ $cita->cliente->address ?? 'N/A' }}@if($cita->cliente->city || $cita->cliente->state), {{ $cita->cliente->city }} {{ $cita->cliente->state }}@endif</td>
            </tr>
        </table>

        {{-- Datos del paciente --}}
        <div class="section-title">Datos del Paciente</div>
        <table class="grid">
            <tr>
                <td class="label" style="width: 15%">Mascota:</td>
                <td style="width: 35%"><strong>{{ $cita->mascota?->name ?? 'N/A' }}</strong></td>
                <td class="label" style="width: 15%">Especie:</td>
                <td style="width: 35%">{{ $cita->mascota?->especie?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Raza:</td>
                <td>{{ $cita->mascota?->raza?->name ?? 'N/A' }}</td>
                <td class="label">Sexo:</td>
                <td>{{ isset($cita->mascota?->gender) ? ($cita->mascota->gender === 'M' ? 'Macho' : 'Hembra') : 'N/A' }}</td>
            </tr>
            @if($cita->mascota?->birth_date || $cita->mascota?->weight)
            <tr>
                <td class="label">Edad:</td>
                <td>@if($cita->mascota?->birth_date){{ \Carbon\Carbon::parse($cita->mascota->birth_date)->age }} años @else N/A @endif</td>
                <td class="label">Peso (Ref.):</td>
                <td>{{ $cita->mascota?->weight ? $cita->mascota->weight . ' kg' : 'N/A' }}</td>
            </tr>
            @endif
        </table>

        {{-- Detalles Clínicos — 2 columnas lado a lado --}}
        <div class="section-title">Detalles Clínicos de la Cita</div>
        <table class="grid" style="margin-bottom: 6px;">
            <tr>
                <td class="label" style="width: 15%">Veterinario:</td>
                <td colspan="3"><strong>{{ $cita->veterinario->name ?? 'No asignado' }} {{ $cita->veterinario->last_name ?? '' }}</strong></td>
            </tr>
        </table>

        <table class="layout">
            <tr>
                <td style="width: 50%;">
                    <div class="content-box" style="background-color: #f0fdf4; border-color: #86efac; height: 100%;">
                        <h4 style="color: #065f46; border-bottom-color: #bbf7d0;">Motivo / Razón de la Consulta</h4>
                        <p>{{ $cita->reason ?? 'No especificado' }}</p>
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="content-box" style="background-color: #eff6ff; border-color: #93c5fd; height: 100%;">
                        <h4 style="color: #1e40af; border-bottom-color: #bfdbfe;">Notas Adicionales Previas</h4>
                        <p>{{ $cita->notes ?? 'Sin notas adicionales' }}</p>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Notificaciones enviadas --}}
        @if($cita->notificado_sms || $cita->notificado_whatsapp || $cita->notificado_email)
        <div class="section-title">Notificaciones Enviadas</div>
        <table class="grid">
            <tr>
                @if($cita->notificado_email)
                <td class="label" style="width: 15%">Email:</td>
                <td style="width: 18%">✓ Enviado</td>
                @endif
                @if($cita->notificado_sms)
                <td class="label" style="width: 15%">SMS:</td>
                <td style="width: 18%">✓ Enviado</td>
                @endif
                @if($cita->notificado_whatsapp)
                <td class="label" style="width: 15%">WhatsApp:</td>
                <td style="width: 18%">✓ Enviado</td>
                @endif
            </tr>
        </table>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-text">
                Documento generado automáticamente por {{ config('app.name', 'VETCORESSEN') }} el {{ now()->format('d/m/Y H:i') }}.<br>
                Este comprobante es de carácter informativo y no constituye un documento fiscal.
            </div>
        </div>
    </div>
</body>
</html>
