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
    <title>{{ $t('appointment.titleSingular', 'Cita') }} #{{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; line-height: 1.3; margin: 0; padding: 0; font-size: 11px; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; }

        /* Header — mismo estilo que historia clínica */
        .header { display: table; width: 100%; background-color: #09090b; color: #ffffff; border-bottom: 4px solid #10b981; padding: 15px; margin-bottom: 15px; box-sizing: border-box; }
        .header-logo { display: table-cell; vertical-align: middle; width: 50%; }
        .header-logo h1 { margin: 0; color: #ffffff; font-size: 20px; font-weight: bold; text-transform: uppercase; }
        .header-logo p { margin: 3px 0 0 0; color: #a7f3d0; font-size: 12px; }
        .header-info { display: table-cell; vertical-align: bottom; width: 50%; text-align: right; }
        .header-info p { margin: 1px 0; color: #d1d5db; }
        .header-info strong { color: #ffffff; }

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
                <p>{{ $t('appointment.voucher', 'Comprobante de Cita Médica') }}</p>
            </div>
            <div class="header-info">
                <p>{{ $t('appointment.apptNumber', 'Nº de Cita') }}: <strong>#{{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                <p>{{ $t('appointment.scheduledDate', 'Fecha Programada') }}: <strong>{{ $cita->fecha_hora ? $cita->fecha_hora->format('d/m/Y H:i') : $t('misc.notSpecified', 'No especificada') }}</strong></p>
            </div>
        </div>

        {{-- Estado de la cita --}}
        @php
            $statusLabels = [
                'PENDIENTE' => $t('appointment.statusPending', 'Pendiente'),
                'CONFIRMADA' => $t('appointment.statusConfirmed', 'Confirmada'),
                'EN_PROGRESO' => $t('appointment.statusInProgress', 'En Progreso'),
                'COMPLETADA' => $t('appointment.statusCompleted', 'Completada'),
                'CANCELADA' => $t('appointment.statusCancelled', 'Cancelada'),
                'EMERGENCIA' => $t('appointment.statusEmergency', 'Emergencia'),
            ];
            $statusLabel = $statusLabels[$cita->status] ?? $cita->status;
        @endphp

        <table class="layout" style="margin-bottom: 15px; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px;">
            <tr>
                <td style="width: 100%; text-align: center; vertical-align: middle; padding: 10px 0;">
                    <span style="color: #4b5563; font-weight: bold; font-size: 12px; text-transform: uppercase;">{{ $t('appointment.status', 'Estado de la Cita') }}:</span>
                    <strong style="text-transform: uppercase; font-size: 14px; margin-left: 8px;">{{ $statusLabel }}</strong>
                </td>
            </tr>
        </table>

        {{-- Información General --}}
        <div class="section-title">{{ $t('misc.generalInformation', 'Información General') }}</div>
        <table class="grid">
            <tr>
                <td class="label" style="width: 15%">{{ $t('form.owner', 'Propietario') }}:</td>
                <td style="width: 35%"><strong>{{ $cita->cliente->nombre_completo ?? 'N/A' }}</strong></td>
                <td class="label" style="width: 15%">{{ $t('form.idNumber', 'DNI/RUC') }}:</td>
                <td style="width: 35%">{{ $cita->cliente->numero_documento ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.phone', 'Teléfono') }}:</td>
                <td>{{ $cita->cliente->phone ?? 'N/A' }}</td>
                <td class="label">{{ $t('form.email', 'Email') }}:</td>
                <td>{{ $cita->cliente->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.address', 'Dirección') }}:</td>
                <td colspan="3">{{ $cita->cliente->address ?? 'N/A' }}@if($cita->cliente->city || $cita->cliente->state), {{ $cita->cliente->city }} {{ $cita->cliente->state }}@endif</td>
            </tr>
        </table>

        {{-- Datos del paciente --}}
        <div class="section-title">{{ $t('form.patientData', 'Datos del Paciente') }}</div>
        <table class="grid">
            <tr>
                <td class="label" style="width: 15%">{{ $t('form.pet', 'Mascota') }}:</td>
                <td style="width: 35%"><strong>{{ $cita->mascota?->name ?? 'N/A' }}</strong></td>
                <td class="label" style="width: 15%">{{ $t('form.species', 'Especie') }}:</td>
                <td style="width: 35%">{{ $cita->mascota?->especie?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.breed', 'Raza') }}:</td>
                <td>{{ $cita->mascota?->raza?->name ?? 'N/A' }}</td>
                <td class="label">{{ $t('form.sex', 'Sexo') }}:</td>
                <td>{{ isset($cita->mascota?->gender) ? ($cita->mascota->gender === 'M' ? $t('form.male', 'Macho') : $t('form.female', 'Hembra')) : 'N/A' }}</td>
            </tr>
            @if($cita->mascota?->birth_date || $cita->mascota?->weight)
            <tr>
                <td class="label">{{ $t('form.age', 'Edad') }}:</td>
                <td>@if($cita->mascota?->birth_date){{ \Carbon\Carbon::parse($cita->mascota->birth_date)->age }} {{ $t('misc.years', 'años') }} @else N/A @endif</td>
                <td class="label">{{ $t('form.weightRef', 'Peso (Ref.)') }}:</td>
                <td>{{ $cita->mascota?->weight ? $cita->mascota->weight . ' kg' : 'N/A' }}</td>
            </tr>
            @endif
        </table>

        {{-- Detalles Clínicos — 2 columnas lado a lado --}}
        <div class="section-title">{{ $t('appointment.clinicalDetails', 'Detalles Clínicos de la Cita') }}</div>
        <table class="grid" style="margin-bottom: 6px;">
            <tr>
                <td class="label" style="width: 15%">{{ $t('form.veterinarian', 'Veterinario') }}:</td>
                <td colspan="3"><strong>{{ $cita->veterinario->name ?? $t('misc.unassigned', 'No asignado') }} {{ $cita->veterinario->last_name ?? '' }}</strong></td>
            </tr>
        </table>

        <table class="layout">
            <tr>
                <td style="width: 50%;">
                    <div class="content-box" style="background-color: #f0fdf4; border-color: #86efac; height: 100%;">
                        <h4 style="color: #065f46; border-bottom-color: #bbf7d0;">{{ $t('appointment.reason', 'Motivo / Razón de la Consulta') }}</h4>
                        <p>{{ $cita->reason ?? $t('misc.notSpecified', 'No especificado') }}</p>
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="content-box" style="background-color: #eff6ff; border-color: #93c5fd; height: 100%;">
                        <h4 style="color: #1e40af; border-bottom-color: #bfdbfe;">{{ $t('appointment.previousNotes', 'Notas Adicionales Previas') }}</h4>
                        <p>{{ $cita->notes ?? $t('appointment.noAdditionalNotes', 'Sin notas adicionales') }}</p>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Notificaciones enviadas --}}
        @if($cita->notificado_sms || $cita->notificado_whatsapp || $cita->notificado_email)
        <div class="section-title">{{ $t('appointment.notificationsSent', 'Notificaciones Enviadas') }}</div>
        <table class="grid">
            <tr>
                @if($cita->notificado_email)
                <td class="label" style="width: 15%">Email:</td>
                <td style="width: 18%">✓ {{ $t('appointment.sent', 'Enviado') }}</td>
                @endif
                @if($cita->notificado_sms)
                <td class="label" style="width: 15%">SMS:</td>
                <td style="width: 18%">✓ {{ $t('appointment.sent', 'Enviado') }}</td>
                @endif
                @if($cita->notificado_whatsapp)
                <td class="label" style="width: 15%">WhatsApp:</td>
                <td style="width: 18%">✓ {{ $t('appointment.sent', 'Enviado') }}</td>
                @endif
            </tr>
        </table>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-text">
                {{ $t('report.generatedBy', 'Documento generado automáticamente por') }} {{ config('app.name', 'VETCORESSEN') }} {{ $t('misc.on_date', 'el') }} {{ now()->format('d/m/Y H:i') }}.<br>
                {{ $t('report.notFiscalDoc', 'Este comprobante es de carácter informativo y no constituye un documento fiscal.') }}
            </div>
        </div>
    </div>
</body>
</html>
