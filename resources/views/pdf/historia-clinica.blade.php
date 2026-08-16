<!DOCTYPE html>
<html lang="{{ request()->query('lang', 'es') }}">
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
    <title>{{ $t('report.medicalRecordTitle', 'Historia Clínica') }} #{{ str_pad($historia->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; line-height: 1.3; margin: 0; padding: 0; font-size: 11px; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* Header */
        .header { display: table; width: 100%; background-color: #09090b; color: #ffffff; border-bottom: 4px solid #10b981; padding: 15px; margin-bottom: 15px; box-sizing: border-box; }
        .header-logo { display: table-cell; vertical-align: middle; width: 50%; }
        .header-logo h1 { margin: 0; color: #ffffff; font-size: 20px; font-weight: bold; text-transform: uppercase; }
        .header-logo p { margin: 3px 0 0 0; color: #a7f3d0; font-size: 12px; }
        .header-info { display: table-cell; vertical-align: bottom; width: 50%; text-align: right; }
        .header-info p { margin: 1px 0; color: #d1d5db; }
        .header-info strong { color: #ffffff; }

        /* Titles */
        .section-title { font-size: 11px; font-weight: bold; color: #065f46; border-bottom: 2px solid #34d399; padding-bottom: 3px; margin-bottom: 8px; margin-top: 18px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Grid Tables */
        table.grid { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 10px; }
        table.grid td { padding: 4px 6px; vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
        table.grid td.label { width: 15%; color: #6b7280; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        table.grid td.value { width: 35%; color: #111827; }
        
        /* Layout Tables */
        table.layout { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 10px; }
        table.layout > tbody > tr > td { padding: 0; vertical-align: top; }
        table.layout > tbody > tr > td:first-child { padding-right: 5px; }
        table.layout > tbody > tr > td:last-child { padding-left: 5px; }

        /* Content boxes */
        .content-box { border-left: 3px solid #34d399; border-radius: 0px; padding: 6px 8px; margin-bottom: 8px; background-color: #f9fafb; }
        .content-box h4 { margin: 0 0 4px 0; color: #374151; font-size: 10px; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; }
        .content-box p { margin: 0; white-space: pre-wrap; color: #1f2937; }

        /* Compact Table */
        table.compact { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.compact th, table.compact td { border-bottom: 1px solid #e5e7eb; padding: 6px 4px; text-align: left; }
        table.compact th { color: #4b5563; font-weight: bold; font-size: 9px; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; }
        
        /* Footer */
        .footer { margin-top: 30px; padding-top: 10px; position: relative; page-break-inside: avoid; }
        .signature-box { width: 220px; text-align: center; float: right; }
        .signature-line { border-top: 1px solid #4b5563; padding-top: 3px; margin-bottom: 3px; }
        .signature-box strong { display: block; color: #111827; font-size: 12px; }
        .signature-box span { color: #6b7280; font-size: 10px; }
        .footer-text { clear: both; text-align: center; color: #9ca3af; font-size: 9px; margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-logo">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo" style="max-height: 40px; margin-bottom: 4px;">
                @endif
                <h1>{{ $clinic->name ?? config('app.name', 'VETCORESSEN') }}</h1>
                <p>{{ $t('report.medicalReport', 'Reporte Médico Clínico') }}</p>
            </div>
            <div class="header-info">
                <p>{{ $t('report.recordNumber', 'Nº de Registro') }}: <strong>{{ str_pad($historia->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                <p>{{ $t('report.consultDate', 'Fecha de Consulta') }}: <strong>{{ $historia->created_at->format('d/m/Y H:i') }}</strong></p>
            </div>
        </div>

        <div class="section-title">{{ $t('report.generalInfo', 'Información General') }}</div>
        <table class="grid">
            <tr>
                <td class="label">{{ $t('table.patient', 'Paciente') }}:</td>
                <td class="value"><strong>{{ $historia->pet->name ?? 'N/A' }}</strong></td>
                <td class="label">{{ $t('table.owner', 'Propietario') }}:</td>
                <td class="value">{{ $historia->pet->cliente->nombre_completo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.species', 'Especie') }}/{{ $t('form.breed', 'Raza') }}:</td>
                <td class="value">{{ $historia->pet->especie->name ?? 'N/A' }} / {{ $historia->pet->raza->name ?? 'N/A' }}</td>
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
                <td class="value">{{ $historia->pet->cliente->email ?? '-' }} <br> <span style="font-size: 9px; color: #4b5563;">{{ $historia->pet->cliente->address ?? '-' }}</span></td>
            </tr>
        </table>

        <div class="section-title">{{ $t('report.anamnesisAndVitals', 'Anamnesis y Signos Vitales') }}</div>
        
        <table class="layout" style="width: 100%; border-spacing: 0; border-collapse: separate;">
            <tr>
                <td style="width: 50%; padding-right: 8px;">
                    <div style="border-left: 3px solid #34d399; border: 1px solid #e5e7eb; border-left-width: 4px; border-left-color: #34d399; background-color: #f9fafb; padding: 10px; border-radius: 6px;">
                        <h4 style="color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin: 0 0 5px 0; font-size: 10px; text-transform: uppercase;">📋 {{ $t('report.reasonForVisit', 'Motivo de Consulta') }}</h4>
                        <p style="margin: 0; white-space: pre-wrap; color: #1f2937; font-size: 11px;">{{ $historia->reason ?? $t('misc.notSpecified', 'No especificado') }}</p>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 8px;">
                    <div style="border-left: 3px solid #34d399; border: 1px solid #e5e7eb; border-left-width: 4px; border-left-color: #34d399; background-color: #f9fafb; padding: 10px; border-radius: 6px;">
                        <h4 style="color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin: 0 0 5px 0; font-size: 10px; text-transform: uppercase;">📝 {{ $t('report.anamnesisSigns', 'Anamnesis y Signos Clínicos') }}</h4>
                        <p style="margin: 0; white-space: pre-wrap; color: #1f2937; font-size: 11px;">{{ $historia->anamnesis ?? $t('misc.notSpecified', 'No especificados') }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="compact" style="margin-bottom: 8px;">
            <tr>
                <th>{{ $t('form.weight', 'Peso') }} (kg)</th>
                <th>{{ $t('form.temperature', 'Temp.') }} (°C)</th>
                <th>{{ $t('form.heartRate', 'Frec. Cardíaca') }} (bpm)</th>
                <th>{{ $t('form.respRate', 'Frec. Resp.') }} (rpm)</th>
            </tr>
            <tr>
                <td>{{ $historia->weight ?? '-' }}</td>
                <td>{{ $historia->temperature ?? '-' }}</td>
                <td>{{ $historia->heart_rate ?? '-' }}</td>
                <td>{{ $historia->respiratory_rate ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">{{ $t('report.physicalExamBySystem', 'Examen Físico por Sistemas') }}</div>
        <table class="grid">
            <tr>
                <td class="label" style="width:12%">{{ $t('form.bodyCondition', 'Cond. Corp') }}:</td>
                <td class="value" style="width:21%">{{ $historia->condicion_corporal ?? '-' }}</td>
                <td class="label" style="width:12%">{{ $t('form.hydration', 'Hidratación') }}:</td>
                <td class="value" style="width:21%">{{ $historia->nivel_hidratacion ?? '-' }}</td>
                <td class="label" style="width:12%">{{ $t('form.painLevel', 'Nivel Dolor') }}:</td>
                <td class="value" style="width:22%">{{ $historia->nivel_dolor ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label" style="width:12%">{{ $t('form.cardiovascular', 'Cardiovascular') }}:</td>
                <td class="value" style="width:21%">{{ $historia->examen_cardiovascular ?? '-' }}</td>
                <td class="label" style="width:12%">{{ $t('form.digestive', 'Digestivo') }}:</td>
                <td class="value" style="width:21%">{{ $historia->examen_digestivo ?? '-' }}</td>
                <td class="label" style="width:12%">{{ $t('form.lymphNodes', 'Linfonodos') }}:</td>
                <td class="value" style="width:22%">{{ $historia->examen_linfonodos ?? '-' }}</td>
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

        <div class="section-title">{{ $t('report.diagnosisAndPlan', 'Diagnóstico y Plan') }}</div>

        <table class="layout" style="width: 100%; border-spacing: 0; border-collapse: separate;">
            <tr>
                <td style="width: 50%; padding-right: 8px;">
                    <div style="border-left: 3px solid #10b981; border: 1px solid #bbf7d0; border-left-width: 4px; border-left-color: #10b981; background-color: #f0fdf4; padding: 10px; border-radius: 6px;">
                        <h4 style="color: #065f46; border-bottom: 1px solid #bbf7d0; padding-bottom: 4px; margin: 0 0 5px 0; font-size: 10px; text-transform: uppercase;">🩺 {{ $t('report.diagnosis', 'Diagnóstico Presuntivo / Definitivo') }}</h4>
                        <p style="margin: 0; white-space: pre-wrap; color: #1f2937; font-size: 11px;">{{ $historia->diagnostico_presuntivo ?? $t('misc.notSpecified', 'No especificado') }}</p>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 8px;">
                    <div style="border-left: 3px solid #3b82f6; border: 1px solid #bfdbfe; border-left-width: 4px; border-left-color: #3b82f6; background-color: #eff6ff; padding: 10px; border-radius: 6px;">
                        <h4 style="color: #1e40af; border-bottom: 1px solid #bfdbfe; padding-bottom: 4px; margin: 0 0 5px 0; font-size: 10px; text-transform: uppercase;">💊 {{ $t('report.treatmentIndications', 'Tratamiento e Indicaciones Médicas') }}</h4>
                        <p style="margin: 0; white-space: pre-wrap; color: #1f2937; font-size: 11px;">{{ $historia->tratamiento_indicaciones ?? $t('misc.notSpecified', 'No especificado') }}</p>
                    </div>
                </td>
            </tr>
        </table>

        @if($historia->prescripciones && count($historia->prescripciones) > 0)
        <div class="section-title">{{ $t('report.prescriptions', 'Receta Médica') }}</div>
        <table class="compact">
            <thead>
                <tr>
                    <th style="width: 25%">{{ $t('report.medication', 'Medicamento') }}</th>
                    <th style="width: 15%">{{ $t('report.dose', 'Dosis') }}</th>
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
                        <br><span style="font-size: 9px; color: #6b7280;">{{ $rx->producto->presentacion }} ({{ $rx->producto->principio_activo }})</span>
                        @endif
                    </td>
                    <td>{{ $rx->dosage ?? '-' }}</td>
                    <td>{{ $rx->frequency ?? '-' }}</td>
                    <td>{{ $rx->via_administracion ?? '-' }}</td>
                    <td>{{ $rx->duracion_dias ? $rx->duracion_dias . ' ' . $t('misc.days', 'días') : '-' }}</td>
                    <td style="font-size: 10px;">{{ $rx->indicaciones ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <table class="layout" style="width: 100%; border-spacing: 5px; border-collapse: separate; margin-top: 8px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    @if($historia->notas_aclaratorias || $historia->proxima_cita_recomendada)
                    <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; padding: 10px;">
                        <h4 style="color: #b45309; border-bottom: 1px solid #fcd34d; padding-bottom: 4px; margin: 0 0 5px 0; font-size: 11px; text-transform: uppercase;">{{ $t('report.additionalNotes', 'Notas Adicionales') }}</h4>
                        @if($historia->notas_aclaratorias)
                            <p style="margin: 0 0 5px 0; white-space: pre-wrap; color: #1f2937;">{{ $historia->notas_aclaratorias }}</p>
                        @endif
                        @if($historia->proxima_cita_recomendada)
                            <p style="margin: 0; color: #1f2937;"><strong>{{ $t('form.recommendedNextAppt', 'Próxima Cita Recomendada') }}:</strong> {{ \Carbon\Carbon::parse($historia->proxima_cita_recomendada)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    @endif
                </td>
                <td style="width: 50%;"></td>
            </tr>
        </table>

        <div class="footer">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>{{ $historia->veterinario->name ?? 'N/A' }} {{ $historia->veterinario->last_name ?? '' }}</strong>
                <span>{{ $t('misc.veterinarian', 'Médico Veterinario') }}</span>
                @if(isset($historia->veterinario->cmvp) && $historia->veterinario->cmvp)
                <br><span>CMVP: {{ $historia->veterinario->cmvp }}</span>
                @endif
            </div>
            
            <div class="footer-text">
                {{ $t('report.generatedBy', 'Documento generado automáticamente por') }} {{ config('app.name', 'VETCORESSEN') }} {{ $t('misc.on_date', 'el') }} {{ now()->format('d/m/Y H:i') }}.<br>
                {{ $t('report.confidentiality', 'Este reporte es de uso exclusivamente clínico y confidencial.') }}
            </div>
        </div>
    </div>
</body>
</html>
