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
    <title>{{ $t('report.medicalHistory', 'Historial Clínico') }} - {{ $mascota->name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #374151; line-height: 1.5; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* Header */
        .header { display: table; width: 100%; background-color: #09090b; color: #ffffff; border-bottom: 4px solid #10b981; padding: 15px; margin-bottom: 20px; box-sizing: border-box; }
        .header-logo { display: table-cell; vertical-align: middle; width: 60%; }
        .header-logo h1 { margin: 0; color: #ffffff; font-size: 26px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header-logo p { margin: 5px 0 0 0; color: #a7f3d0; font-size: 14px; }
        .header-info { display: table-cell; vertical-align: bottom; width: 40%; text-align: right; }
        .header-info p { margin: 2px 0; color: #d1d5db; }
        .header-info strong { color: #ffffff; }

        /* Titles */
        .section-title { font-size: 13px; font-weight: bold; color: #ffffff; background-color: #059669; padding: 4px 8px; margin-bottom: 8px; margin-top: 15px; text-transform: uppercase; border-radius: 3px; }
        .record-title { font-size: 14px; font-weight: bold; color: #111827; background-color: #f3f4f6; padding: 8px 12px; border-left: 4px solid #059669; margin-top: 30px; margin-bottom: 15px; }
        
        /* Grid Tables */
        table.grid { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.grid td { padding: 6px 0; vertical-align: top; }
        table.grid td.label { width: 20%; color: #6b7280; font-weight: bold; }
        table.grid td.value { width: 30%; color: #111827; }
        
        /* Layout Tables */
        table.layout { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.layout > tbody > tr > td { padding: 0; vertical-align: top; }
        table.layout > tbody > tr > td:first-child { padding-right: 5px; }
        table.layout > tbody > tr > td:last-child { padding-left: 5px; }

        /* Content boxes */
        .content-box { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px; margin-bottom: 10px; }
        .content-box h4 { margin: 0 0 5px 0; color: #374151; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; }
        .content-box p { margin: 0; white-space: pre-wrap; color: #1f2937; }

        /* Compact Table */
        table.compact { width: 100%; border-collapse: collapse; }
        table.compact th, table.compact td { border: 1px solid #d1d5db; padding: 4px 6px; text-align: left; font-size: 11px; }
        table.compact th { background-color: #374151; color: #ffffff; font-weight: bold; font-size: 10px; text-transform: uppercase; }

        /* Prescriptions table */
        table.prescriptions { width: 100%; border-collapse: collapse; margin-top: 5px; margin-bottom: 15px; font-size: 11px; }
        table.prescriptions th, table.prescriptions td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; }
        table.prescriptions th { background-color: #f3f4f6; color: #374151; font-weight: bold; }
        
        /* Footer */
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #e5e7eb; position: relative; page-break-inside: avoid; }
        .footer-text { text-align: center; color: #9ca3af; font-size: 11px; margin-top: 20px; }

        /* Utility */
        .page-break { page-break-after: always; }
        .avoid-break { page-break-inside: avoid; }
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
                <p>{{ $t('report.completeHistory', 'Historial Clínico Completo') }}</p>
            </div>
            <div class="header-info">
                <p>{{ $t('misc.generated_on', 'Generado el') }}: <strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
                <p>{{ $t('report.totalRecords', 'Total registros') }}: <strong>{{ $mascota->historiasClinicas->count() }}</strong></p>
            </div>
        </div>

        <div class="section-title">{{ $t('form.patientOwnerInfo', 'Información del Paciente y Propietario') }}</div>
        <table class="grid">
            <tr>
                <td class="label">{{ $t('form.patient', 'Paciente') }}:</td>
                <td class="value"><strong>{{ $mascota->name ?? 'N/A' }}</strong></td>
                <td class="label">{{ $t('form.owner', 'Propietario') }}:</td>
                <td class="value">{{ $mascota->cliente?->nombre_completo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.species', 'Especie') }} / {{ $t('form.breed', 'Raza') }}:</td>
                <td class="value">{{ $mascota->especie->name ?? 'N/A' }} / {{ $mascota->raza->name ?? 'N/A' }}</td>
                <td class="label">{{ $t('form.idNumber', 'DNI / RUC') }}:</td>
                <td class="value">{{ $mascota->cliente?->numero_documento ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.sex', 'Sexo') }} / {{ $t('form.weightLast', 'Peso (último)') }}:</td>
                <td class="value">{{ isset($mascota->gender) ? ($mascota->gender === 'M' ? $t('form.male', 'Macho') : $t('form.female', 'Hembra')) : 'N/A' }} / {{ $mascota->historiasClinicas->first()?->weight ? $mascota->historiasClinicas->first()->weight . ' kg' : 'N/A' }}</td>
                <td class="label">{{ $t('form.phone', 'Teléfono') }}:</td>
                <td class="value">{{ $mascota->cliente?->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">{{ $t('form.age', 'Edad') }}:</td>
                <td class="value">{{ $mascota->birth_date ? \Carbon\Carbon::parse($mascota->birth_date)->age . ' ' . $t('misc.years', 'años') : 'N/A' }}</td>
                <td class="label">{{ $t('form.email', 'Email') }} / {{ $t('form.addr', 'Dir.') }}:</td>
                <td class="value">{{ $mascota->cliente?->email ?? '-' }} <br> <span style="font-size: 9px; color: #4b5563;">{{ $mascota->cliente?->address ?? '-' }}</span></td>
            </tr>
        </table>

        @if($mascota->historiasClinicas->isEmpty())
            <div style="text-align: center; padding: 50px; color: #6b7280; font-size: 14px; background-color: #f9fafb; border-radius: 8px; margin-top: 30px;">
                {{ $t('report.noRecords', 'No hay registros clínicos para esta mascota.') }}
            </div>
        @else
            @foreach($mascota->historiasClinicas as $historia)
                <div class="record-title avoid-break">
                        <table style="width: 100%; border: none;">
                            <tr>
                                <td style="width: 50%; text-align: left;">
                                    {{ $t('form.date', 'Fecha') }}: {{ $historia->date ? $historia->date->format('d/m/Y h:i A') : $historia->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td style="width: 50%; text-align: right; color: #4b5563; font-weight: normal; font-size: 12px;">
                                    {{ $t('report.attendedBy', 'Atendido por') }}: {{ $historia->veterinario->name ?? 'N/A' }} {{ $historia->veterinario->last_name ?? '' }}
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="width: 50%; text-align: left;">
                                {{ $t('form.date', 'Fecha') }}: {{ $historia->date ? $historia->date->format('d/m/Y h:i A') : $historia->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td style="width: 50%; text-align: right; color: #4b5563; font-weight: normal; font-size: 12px;">
                                {{ $t('report.attendedBy', 'Atendido por') }}: {{ $historia->veterinario->name ?? 'N/A' }} {{ $historia->veterinario->last_name ?? '' }}
                                @if(isset($historia->veterinario->cmvp) && $historia->veterinario->cmvp)
                                    <br><span style="font-size: 10px;">CMVP: {{ $historia->veterinario->cmvp }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">{{ $t('report.anamnesisAndVitals', 'Anamnesis y Signos Vitales') }}</div>
                <table class="layout">
                    <tr>
                        <td style="width: 50%;">
                            <div class="content-box">
                                <h4>{{ $t('report.reasonForVisit', 'Motivo de Consulta') }}</h4>
                                <p>{{ $historia->reason ?? $t('misc.notSpecified', 'No especificado') }}</p>
                            </div>
                        </td>
                        <td style="width: 50%;">
                            <div class="content-box">
                                <h4>{{ $t('report.anamnesisSigns', 'Anamnesis y Signos Clínicos') }}</h4>
                                <p>{{ $historia->anamnesis ?? $t('misc.notSpecified', 'No especificados') }}</p>
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

                <div class="section-title">{{ $t('report.diagnosisAndPlan', 'Diagnóstico y Plan') }}</div>
                <table class="layout">
                    <tr>
                        <td style="width: 50%;">
                            <div class="content-box" style="background-color: #f0fdf4; border-color: #86efac;">
                                <h4 style="color: #065f46; border-bottom-color: #bbf7d0;">{{ $t('report.diagnosis', 'Diagnóstico Presuntivo / Definitivo') }}</h4>
                                <p>{{ $historia->diagnostico_presuntivo ?? $t('misc.notSpecified', 'No especificado') }}</p>
                            </div>
                        </td>
                        <td style="width: 50%;">
                            <div class="content-box" style="background-color: #eff6ff; border-color: #93c5fd;">
                                <h4 style="color: #1e40af; border-bottom-color: #bfdbfe;">{{ $t('report.treatmentIndications', 'Tratamiento e Indicaciones Médicas') }}</h4>
                                <p>{{ $historia->tratamiento_indicaciones ?? $t('misc.notSpecified', 'No especificado') }}</p>
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

                <table class="layout" style="margin-top: 8px;">
                    <tr>
                        <td style="width: 50%;">
                            @if($historia->notas_aclaratorias || $historia->proxima_cita_recomendada)
                                <div class="content-box" style="background-color: #fffbeb; border-color: #fde68a;">
                                    <h4 style="color: #b45309;">{{ $t('report.additionalNotes', 'Notas Adicionales') }}</h4>
                                    @if($historia->notas_aclaratorias)
                                        <p style="margin-bottom: 5px;">{{ $historia->notas_aclaratorias }}</p>
                                    @endif
                                    @if($historia->proxima_cita_recomendada)
                                        <p><strong>{{ $t('form.recommendedNextAppt', 'Próxima Cita Recomendada') }}:</strong> {{ \Carbon\Carbon::parse($historia->proxima_cita_recomendada)->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td style="width: 50%;"></td>
                    </tr>
                </table>

                @if(!$loop->last)
                    <div class="page-break"></div>
                @endif
            @endforeach
        @endif

        <div class="footer">
            <div class="footer-text">
                {{ $t('report.generatedBy', 'Documento generado automáticamente por') }} {{ config('app.name', 'VETCORESSEN') }} {{ $t('misc.on_date', 'el') }} {{ now()->format('d/m/Y H:i') }}.<br>
                {{ $t('report.confidentiality', 'Este reporte es de uso exclusivamente clínico y confidencial.') }}
            </div>
        </div>
    </div>
</body>
</html>
