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
    <title>{{ $t('report.medicalRecordTitle', 'Historia Clínica') }} #{{ str_pad($historia->id, 6, '0', STR_PAD_LEFT) }}</title>
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

        /* ═══ Triaje / Signos Vitales (Alineado al 100%) ═══ */
        table.vitals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        table.vitals-table td {
            width: 25%;
            padding: 5px 6px;
            background-color: #f0fdf4;
            border: 1px solid #a7f3d0;
            text-align: center;
            vertical-align: middle;
        }
        .vital-label {
            font-size: 8px;
            font-weight: 700;
            color: #047857;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .vital-value {
            font-size: 11px;
            font-weight: bold;
            color: #065f46;
        }

        /* ═══ Tarjetas Clínicas en 2 Columnas (Alineación Perfecta) ═══ */
        table.cards-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        table.cards-table td.col-left {
            width: 50%;
            vertical-align: top;
            padding-right: 4px;
            padding-left: 0;
        }
        table.cards-table td.col-right {
            width: 50%;
            vertical-align: top;
            padding-left: 4px;
            padding-right: 0;
        }
        .clinical-box {
            border: 1px solid #cbd5e1;
            border-top: 2.5px solid #059669;
            border-radius: 4px;
            background-color: #ffffff;
            padding: 6px 8px;
            min-height: 44px;
        }
        .clinical-box.blue {
            border-top-color: #2563eb;
            background-color: #f8fafc;
        }
        .box-header {
            font-size: 8.5px;
            font-weight: bold;
            color: #065f46;
            text-transform: uppercase;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 3px;
            margin-bottom: 4px;
        }
        .clinical-box.blue .box-header {
            color: #1d4ed8;
        }
        .box-body {
            font-size: 9px;
            color: #334155;
            line-height: 1.35;
        }

        /* ═══ Tabla de Receta Médica ═══ */
        table.rx-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 6px;
            font-size: 8.5px;
            border: 1px solid #cbd5e1;
        }
        table.rx-table th {
            background-color: #065f46;
            color: #ffffff;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #065f46;
            text-align: left;
        }
        table.rx-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        table.rx-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* ═══ Firma y Pie de Página ═══ */
        .footer-section {
            margin-top: 18px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
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
        {{-- Encabezado Ejecutivo --}}
        <table class="header-table">
            <tr>
                <td class="header-left">
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" alt="Logo" style="max-height: 38px; margin-bottom: 3px;">
                    @endif
                    <h1 class="clinic-name">{{ $clinic->name ?? config('app.name', 'VETCORESSEN') }}</h1>
                    <p class="doc-type">{{ $t('report.medicalReport', 'Reporte Médico Clínico') }}</p>
                    <div class="clinic-details">
                        @if($clinic && $clinic->address) {{ $clinic->address }} &bull; @endif
                        @if($clinic && $clinic->phone) Tel: {{ $clinic->phone }} &bull; @endif
                        @if($clinic && $clinic->email) {{ $clinic->email }} @endif
                    </div>
                </td>
                <td class="header-right">
                    <div class="record-badge">
                        <p class="record-number">{{ $t('report.recordNumber', 'Nº de Registro') }}: #{{ str_pad($historia->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="record-date">{{ $t('report.consultDate', 'Fecha de Consulta') }}: <strong>{{ $historia->created_at->format('M d, Y h:i A') }}</strong></p>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Información General --}}
        <div class="section-title">{{ $t('report.generalInfo', 'Información General') }}</div>
        <table class="grid">
            <tr>
                <td class="label">{{ $t('table.patient', 'Paciente') }}:</td>
                <td class="value"><strong>{{ $historia->pet->name ?? 'N/A' }}</strong></td>
                <td class="label">{{ $t('table.owner', 'Propietario') }}:</td>
                <td class="value"><strong>{{ $historia->pet->cliente->nombre_completo ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.species', 'Especie') }} / {{ $t('form.breed', 'Raza') }}:</td>
                <td class="value">{{ $historia->pet->especie->name ?? 'N/A' }} &bull; {{ $historia->pet->raza->name ?? 'N/A' }}</td>
                <td class="label">{{ $t('form.taxId', 'DNI/RUC') }}:</td>
                <td class="value">{{ $historia->pet->cliente->numero_documento ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.gender', 'Sexo') }}:</td>
                <td class="value">{{ isset($historia->pet->gender) ? ($historia->pet->gender === 'M' ? $t('form.male', 'Macho') : $t('form.female', 'Hembra')) : 'N/A' }}</td>
                <td class="label">{{ $t('table.phone', 'Teléfono') }}:</td>
                <td class="value">{{ $historia->pet->cliente->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.age', 'Edad') }}:</td>
                <td class="value">{{ $historia->pet->birth_date ? \Carbon\Carbon::parse($historia->pet->birth_date)->age . ' ' . $t('misc.years', 'años') : 'N/A' }}</td>
                <td class="label">{{ $t('form.email', 'Email') }} / {{ $t('form.address', 'Dir.') }}:</td>
                <td class="value">{{ $historia->pet->cliente->email ?? '-' }} &bull; <span style="color: #64748b;">{{ $historia->pet->cliente->address ?? '-' }}</span></td>
            </tr>
        </table>

        {{-- Anamnesis y Signos Vitales --}}
        <div class="section-title">{{ $t('report.anamnesisAndVitals', 'Anamnesis y Signos Vitales') }}</div>
        
        {{-- Tarjetas Triaje --}}
        <table class="vitals-table">
            <tr>
                <td>
                    <div class="vital-label">{{ $t('form.weight', 'Peso') }}</div>
                    <div class="vital-value">{{ $historia->weight ? $historia->weight . ' kg' : '-' }}</div>
                </td>
                <td>
                    <div class="vital-label">{{ $t('form.temperature', 'Temperatura') }}</div>
                    <div class="vital-value">{{ $historia->temperature ? $historia->temperature . ' °C' : '-' }}</div>
                </td>
                <td>
                    <div class="vital-label">{{ $t('form.heartRate', 'Frec. Cardíaca') }}</div>
                    <div class="vital-value">{{ $historia->heart_rate ? $historia->heart_rate . ' bpm' : '-' }}</div>
                </td>
                <td>
                    <div class="vital-label">{{ $t('form.respRate', 'Frec. Resp.') }}</div>
                    <div class="vital-value">{{ $historia->respiratory_rate ? $historia->respiratory_rate . ' rpm' : '-' }}</div>
                </td>
            </tr>
        </table>

        {{-- Motivo y Anamnesis --}}
        <table class="cards-table">
            <tr>
                <td class="col-left">
                    <div class="clinical-box">
                        <div class="box-header">{{ $t('report.reasonForVisit', 'Motivo de Consulta') }}</div>
                        <div class="box-body">{{ $historia->reason ?? $t('misc.notSpecified', 'No especificado') }}</div>
                    </div>
                </td>
                <td class="col-right">
                    <div class="clinical-box">
                        <div class="box-header">{{ $t('report.anamnesisSigns', 'Anamnesis y Signos Clínicos') }}</div>
                        <div class="box-body">{{ $historia->anamnesis ?? $t('misc.notSpecified', 'No especificados') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Examen Físico por Sistemas --}}
        <div class="section-title">{{ $t('report.physicalExamBySystem', 'Examen Físico por Sistemas') }}</div>
        <table class="grid">
            <tr>
                <td class="label" style="width:16%;">{{ $t('form.bodyCondition', 'Cond. Corp') }}:</td>
                <td class="value" style="width:17.33%;">{{ $historia->condicion_corporal ?? '-' }}</td>
                <td class="label" style="width:16%;">{{ $t('form.hydration', 'Hidratación') }}:</td>
                <td class="value" style="width:17.33%;">{{ $historia->nivel_hidratacion ?? '-' }}</td>
                <td class="label" style="width:16%;">{{ $t('form.painLevel', 'Nivel Dolor') }}:</td>
                <td class="value" style="width:17.34%;">{{ $historia->nivel_dolor ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.cardiovascular', 'Cardiovascular') }}:</td>
                <td class="value">{{ $historia->examen_cardiovascular ?? '-' }}</td>
                <td class="label">{{ $t('form.digestive', 'Digestivo') }}:</td>
                <td class="value">{{ $historia->examen_digestivo ?? '-' }}</td>
                <td class="label">{{ $t('form.lymphNodes', 'Linfonodos') }}:</td>
                <td class="value">{{ $historia->examen_linfonodos ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.mucous', 'Mucosas') }}:</td>
                <td class="value">{{ $historia->examen_mucosas ?? '-' }}</td>
                <td class="label">{{ $t('form.musculoskeletal', 'Músculoesq.') }}:</td>
                <td class="value">{{ $historia->examen_musculoesqueletico ?? '-' }}</td>
                <td class="label">{{ $t('form.neurological', 'Neurológico') }}:</td>
                <td class="value">{{ $historia->examen_neurologico ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.eyesEars', 'Ojos/Oídos') }}:</td>
                <td class="value">{{ $historia->examen_ojos_oidos ?? '-' }}</td>
                <td class="label">{{ $t('form.skinCoat', 'Piel/Pelaje') }}:</td>
                <td class="value">{{ $historia->examen_piel_pelaje ?? '-' }}</td>
                <td class="label">{{ $t('form.respiratory', 'Respiratorio') }}:</td>
                <td class="value">{{ $historia->examen_respiratorio ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.urinary', 'Urinario') }}:</td>
                <td class="value" colspan="5">{{ $historia->examen_urinario ?? '-' }}</td>
            </tr>
        </table>

        {{-- Diagnóstico y Plan --}}
        <div class="section-title">{{ $t('report.diagnosisAndPlan', 'Diagnóstico y Plan') }}</div>
        <table class="cards-table">
            <tr>
                <td class="col-left">
                    <div class="clinical-box">
                        <div class="box-header">{{ $t('report.diagnosis', 'Diagnóstico Presuntivo / Definitivo') }}</div>
                        <div class="box-body">{{ $historia->diagnostico_presuntivo ?? $t('misc.notSpecified', 'No especificado') }}</div>
                    </div>
                </td>
                <td class="col-right">
                    <div class="clinical-box blue">
                        <div class="box-header">{{ $t('report.treatmentIndications', 'Tratamiento e Indicaciones') }}</div>
                        <div class="box-body">{{ $historia->tratamiento_indicaciones ?? $t('misc.notSpecified', 'No especificado') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Receta Médica --}}
        @if($historia->prescripciones && count($historia->prescripciones) > 0)
        <div class="section-title">{{ $t('report.prescriptions', 'Receta Médica') }}</div>
        <table class="rx-table">
            <thead>
                <tr>
                    <th style="width: 26%">{{ $t('report.medication', 'Medicamento') }}</th>
                    <th style="width: 14%">{{ $t('report.dose', 'Dosis') }}</th>
                    <th style="width: 15%">{{ $t('report.frequency', 'Frecuencia') }}</th>
                    <th style="width: 10%">{{ $t('form.route', 'Vía') }}</th>
                    <th style="width: 10%">{{ $t('form.duration', 'Duración') }}</th>
                    <th style="width: 25%">{{ $t('form.indications', 'Indicaciones') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historia->prescripciones as $rx)
                <tr>
                    <td>
                        <strong>{{ $rx->producto->name ?? $rx->medicamento ?? $t('misc.unspecifiedMedication', 'Medicamento no especificado') }}</strong>
                        @if($rx->producto && ($rx->producto->presentacion || $rx->producto->principio_activo))
                        <br><span style="font-size: 8px; color: #64748b;">{{ $rx->producto->presentacion }} ({{ $rx->producto->principio_activo }})</span>
                        @endif
                    </td>
                    <td>{{ $rx->dosage ?? '-' }}</td>
                    <td>{{ $rx->frequency ?? '-' }}</td>
                    <td>{{ $rx->via_administracion ?? '-' }}</td>
                    <td>{{ $rx->duracion_dias ? $rx->duracion_dias . ' ' . $t('misc.days', 'días') : '-' }}</td>
                    <td style="font-size: 8.5px;">{{ $rx->indicaciones ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Notas Adicionales y Próxima Cita --}}
        @if($historia->notas_aclaratorias || $historia->proxima_cita_recomendada)
        <table class="cards-layout" style="margin-top: 4px;">
            <tr>
                <td style="width: 100%;">
                    <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-left: 3px solid #f59e0b; border-radius: 4px; padding: 7px 9px;">
                        <div style="font-size: 8.5px; font-weight: bold; color: #b45309; text-transform: uppercase; margin-bottom: 3px;">{{ $t('report.additionalNotes', 'Notas Adicionales') }}</div>
                        @if($historia->notas_aclaratorias)
                            <div style="font-size: 9.5px; color: #334155; margin-bottom: 3px;">{{ $historia->notas_aclaratorias }}</div>
                        @endif
                        @if($historia->proxima_cita_recomendada)
                            <div style="font-size: 9.5px; color: #0f172a;"><strong>{{ $t('form.recommendedNextAppt', 'Próxima Cita Recomendada') }}:</strong> {{ \Carbon\Carbon::parse($historia->proxima_cita_recomendada)->format('M d, Y') }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
        @endif

        {{-- Firma y Pie de Página --}}
        <div class="footer-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="vet-name">{{ $historia->veterinario->name ?? 'N/A' }} {{ $historia->veterinario->last_name ?? '' }}</div>
                <div class="vet-role">{{ $t('misc.veterinarian', 'Médico Veterinario') }}</div>
                @if(isset($historia->veterinario->cmvp) && $historia->veterinario->cmvp)
                <div class="vet-role">CMVP: {{ $historia->veterinario->cmvp }}</div>
                @endif
            </div>
            
            <div class="footer-disclaimer">
                {{ $t('report.generatedBy', 'Documento generado automáticamente por') }} {{ config('app.name', 'VETCORESSEN') }} {{ $t('misc.on_date', 'el') }} {{ now()->format('M d, Y h:i A') }}.<br>
                {{ $t('report.confidentiality', 'Este reporte es de uso exclusivamente clínico y confidencial.') }}
            </div>
        </div>
    </div>
</body>
</html>
