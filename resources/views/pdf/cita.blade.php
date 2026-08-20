<!DOCTYPE html>
<html lang="{{ request()->query('lang', 'es') }}">
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

    <meta charset="UTF-8">
    <title>{{ $t('appointment.titleSingular', 'Cita') }} #{{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</title>
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
            width: 62%;
            vertical-align: middle;
        }
        .header-right {
            width: 38%;
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
            font-size: 12px;
            font-weight: bold;
            color: #065f46;
            margin: 0;
        }
        .record-date {
            font-size: 8.5px;
            color: #64748b;
            margin: 2px 0 0 0;
        }

        /* ═══ Estado de la Cita ═══ */
        .status-badge-container {
            margin: 6px 0 10px 0;
            padding: 6px 10px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            text-align: center;
        }
        .status-label {
            font-size: 8.5px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-right: 6px;
        }
        .status-pill {
            display: inline-block;
            font-size: 9px;
            font-weight: bold;
            padding: 2.5px 8px;
            border-radius: 10px;
            text-transform: uppercase;
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
            margin-top: 8px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 0 4px 4px 0;
        }

        /* ═══ Tablas de Datos Alineadas (100% de Ancho) ═══ */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 9px;
            border: 1px solid #cbd5e1;
        }
        table.grid td {
            padding: 4px 6px;
            vertical-align: middle;
            border: 1px solid #e2e8f0;
        }
        table.grid td.label {
            width: 18%;
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }
        table.grid td.value {
            width: 32%;
            color: #0f172a;
        }

        .content-card {
            border: 1px solid #cbd5e1;
            border-left: 3.5px solid #059669;
            border-radius: 4px;
            background-color: #ffffff;
            padding: 6px 8px;
            margin-bottom: 6px;
        }
        .content-card h4 {
            margin: 0 0 3px 0;
            font-size: 8.5px;
            font-weight: bold;
            color: #065f46;
            text-transform: uppercase;
        }
        .content-card p {
            margin: 0;
            font-size: 9px;
            color: #334155;
            line-height: 1.35;
        }

        /* ═══ Firma y Pie de Página ═══ */
        .footer-section {
            margin-top: 18px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 200px;
            text-align: center;
            float: right;
        }
        .signature-line {
            border-top: 1.5px solid #475569;
            margin-bottom: 4px;
            padding-top: 4px;
        }
        .vet-name {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
        }
        .vet-role {
            font-size: 8px;
            color: #64748b;
        }
        .footer-disclaimer {
            clear: both;
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
        {{-- Cabecera Ejecutiva --}}
        <table class="header-table">
            <tr>
                <td class="header-left">
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" alt="Logo" style="max-height: 38px; margin-bottom: 3px;">
                    @endif
                    <h1 class="clinic-name">{{ $clinic->name ?? config('app.name', 'VETCORESSEN') }}</h1>
                    <p class="doc-type">{{ $t('appointment.voucher', 'Comprobante de Cita Médica') }}</p>
                    <div class="clinic-details">
                        @if($clinic && $clinic->address) {{ $clinic->address }} &bull; @endif
                        @if($clinic && $clinic->phone) Tel: {{ $clinic->phone }} &bull; @endif
                        @if($clinic && $clinic->email) {{ $clinic->email }} @endif
                    </div>
                </td>
                <td class="header-right">
                    <div class="record-badge">
                        <p class="record-number">{{ $t('appointment.apptNumber', 'Nº de Cita') }}: #{{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="record-date">{{ $t('appointment.scheduledDate', 'Fecha Programada') }}: <strong>{{ $cita->fecha_hora ? $cita->fecha_hora->format('d/m/Y H:i') : $t('misc.notSpecified', 'No especificada') }}</strong></p>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Estado de la Cita --}}
        @php
            $statusConfig = match($cita->status) {
                'PENDIENTE', 'Programada' => ['color' => '#1e40af', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
                'CONFIRMADA', 'Confirmada' => ['color' => '#1d4ed8', 'bg' => '#dbeafe', 'border' => '#93c5fd'],
                'EN_PROGRESO', 'En Progreso' => ['color' => '#b45309', 'bg' => '#fef3c7', 'border' => '#fde68a'],
                'COMPLETADA', 'Completada' => ['color' => '#047857', 'bg' => '#dcfce7', 'border' => '#86efac'],
                'CANCELADA', 'Cancelada' => ['color' => '#b91c1c', 'bg' => '#fee2e2', 'border' => '#fca5a5'],
                'EMERGENCIA', 'Emergencia' => ['color' => '#991b1b', 'bg' => '#ffe4e6', 'border' => '#fecdd3'],
                default => ['color' => '#334155', 'bg' => '#f1f5f9', 'border' => '#cbd5e1']
            };
            $statusLabel = $t('status.' . strtolower($cita->status), $cita->status);
        @endphp

        <div class="status-badge-container">
            <span class="status-label">{{ $t('appointment.status', 'Estado de la Cita') }}:</span>
            <span class="status-pill" style="color: {{ $statusConfig['color'] }}; background-color: {{ $statusConfig['bg'] }}; border: 1px solid {{ $statusConfig['border'] }};">
                {{ $statusLabel }}
            </span>
        </div>

        {{-- Información del Propietario --}}
        <div class="section-title">{{ $t('misc.generalInformation', 'Información General') }}</div>
        <table class="grid">
            <tr>
                <td class="label">{{ $t('form.owner', 'Propietario') }}:</td>
                <td class="value"><strong>{{ $cita->cliente->nombre_completo ?? 'N/A' }}</strong></td>
                <td class="label">{{ $t('form.idNumber', 'DNI/RUC') }}:</td>
                <td class="value">{{ $cita->cliente->numero_documento ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.phone', 'Teléfono') }}:</td>
                <td class="value">{{ $cita->cliente->phone ?? 'N/A' }}</td>
                <td class="label">{{ $t('form.email', 'Email') }}:</td>
                <td class="value">{{ $cita->cliente->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.address', 'Dirección') }}:</td>
                <td class="value" colspan="3">{{ $cita->cliente->address ?? 'N/A' }}</td>
            </tr>
        </table>

        {{-- Datos del Paciente --}}
        <div class="section-title">{{ $t('form.patientData', 'Datos del Paciente') }}</div>
        <table class="grid">
            <tr>
                <td class="label">{{ $t('form.pet', 'Mascota') }}:</td>
                <td class="value"><strong>{{ $cita->mascota?->name ?? 'N/A' }}</strong></td>
                <td class="label">{{ $t('form.species', 'Especie') }}:</td>
                <td class="value">{{ $cita->mascota?->especie?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.breed', 'Raza') }}:</td>
                <td class="value">{{ $cita->mascota?->raza?->name ?? 'N/A' }}</td>
                <td class="label">{{ $t('form.sex', 'Sexo') }}:</td>
                <td class="value">{{ isset($cita->mascota?->gender) ? ($cita->mascota->gender === 'M' ? $t('form.male', 'Macho') : $t('form.female', 'Hembra')) : 'N/A' }}</td>
            </tr>
            @if($cita->mascota?->birth_date || $cita->mascota?->weight)
            <tr>
                <td class="label">{{ $t('form.age', 'Edad') }}:</td>
                <td class="value">{{ $cita->mascota?->birth_date ? \Carbon\Carbon::parse($cita->mascota->birth_date)->age . ' ' . $t('misc.years', 'años') : 'N/A' }}</td>
                <td class="label">{{ $t('form.weight', 'Peso') }}:</td>
                <td class="value">{{ $cita->mascota?->weight ? $cita->mascota->weight . ' kg' : 'N/A' }}</td>
            </tr>
            @endif
        </table>

        {{-- Detalles de la Atención --}}
        <div class="section-title">{{ $t('form.clinicalDetails', 'Detalles Clínicos de la Cita') }}</div>
        <table class="grid">
            <tr>
                <td class="label">{{ $t('table.veterinarian', 'Veterinario') }}:</td>
                <td class="value" colspan="3"><strong>{{ $cita->veterinario->name ?? $t('form.notAssigned', 'No asignado') }}</strong></td>
            </tr>
        </table>

        <div class="content-card">
            <h4>{{ $t('table.reason', 'Motivo de Consulta') }}</h4>
            <p>{{ $cita->reason ?? $t('misc.notSpecified', 'No especificado') }}</p>
        </div>

        @if($cita->notes)
        <div class="content-card" style="border-left-color: #f59e0b; background-color: #fffbeb;">
            <h4 style="color: #b45309;">{{ $t('form.additionalNotes', 'Notas Adicionales') }}</h4>
            <p>{{ $cita->notes }}</p>
        </div>
        @endif

        {{-- Firma y Pie de Página --}}
        <div class="footer-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="vet-name">{{ $cita->veterinario->name ?? 'Médico Veterinario' }}</div>
                <div class="vet-role">{{ $t('misc.veterinarian', 'Médico Veterinario') }}</div>
            </div>
            
            <div class="footer-disclaimer">
                {{ $t('report.generatedBy', 'Documento generado automáticamente por') }} {{ config('app.name', 'VETCORESSEN') }} {{ $t('misc.on_date', 'el') }} {{ now()->format('d/m/Y H:i') }}.<br>
                {{ $t('report.confidentiality', 'Este comprobante es para control de citas y seguimiento clínico.') }}
            </div>
        </div>
    </div>
</body>
</html>
