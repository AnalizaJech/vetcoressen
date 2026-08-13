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
    <title>Historia Clínica #{{ str_pad($historia->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; line-height: 1.3; margin: 0; padding: 0; font-size: 11px; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* Header */
        .header { display: table; width: 100%; border-bottom: 2px solid #059669; padding-bottom: 10px; margin-bottom: 15px; }
        .header-logo { display: table-cell; vertical-align: middle; width: 50%; }
        .header-logo h1 { margin: 0; color: #047857; font-size: 20px; font-weight: bold; text-transform: uppercase; }
        .header-logo p { margin: 3px 0 0 0; color: #4b5563; font-size: 12px; }
        .header-info { display: table-cell; vertical-align: bottom; width: 50%; text-align: right; }
        .header-info p { margin: 1px 0; color: #4b5563; }
        .header-info strong { color: #111827; }

        /* Titles */
        .section-title { font-size: 13px; font-weight: bold; color: #ffffff; background-color: #059669; padding: 4px 8px; margin-bottom: 8px; margin-top: 15px; text-transform: uppercase; border-radius: 3px; }
        
        /* Grid Tables */
        table.grid { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.grid td { padding: 4px 6px; vertical-align: top; border: 1px solid #e5e7eb; }
        table.grid td.label { width: 15%; background-color: #f9fafb; color: #4b5563; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        table.grid td.value { width: 35%; color: #111827; }
        
        /* Layout Tables */
        table.layout { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.layout > tbody > tr > td { padding: 0; vertical-align: top; }
        table.layout > tbody > tr > td:first-child { padding-right: 5px; }
        table.layout > tbody > tr > td:last-child { padding-left: 5px; }

        /* Content boxes */
        .content-box { border: 1px solid #d1d5db; border-radius: 4px; padding: 6px 8px; margin-bottom: 8px; background-color: #f9fafb; }
        .content-box h4 { margin: 0 0 4px 0; color: #374151; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; }
        .content-box p { margin: 0; white-space: pre-wrap; color: #1f2937; }

        /* Compact Table */
        table.compact { width: 100%; border-collapse: collapse; }
        table.compact th, table.compact td { border: 1px solid #d1d5db; padding: 4px 6px; text-align: left; }
        table.compact th { background-color: #374151; color: #ffffff; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        
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
                <p>Reporte Médico Clínico</p>
            </div>
            <div class="header-info">
                <p>Nº de Registro: <strong>{{ str_pad($historia->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                <p>Fecha de Consulta: <strong>{{ $historia->created_at->format('d/m/Y H:i') }}</strong></p>
            </div>
        </div>

        <div class="section-title">Información General</div>
        <table class="grid">
            <tr>
                <td class="label">Paciente:</td>
                <td class="value"><strong>{{ $historia->pet->name ?? 'N/A' }}</strong></td>
                <td class="label">Propietario:</td>
                <td class="value">{{ $historia->pet->customer->nombre_completo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Especie/Raza:</td>
                <td class="value">{{ $historia->pet->species ?? 'N/A' }} / {{ $historia->pet->breed ?? 'N/A' }}</td>
                <td class="label">DNI/RUC:</td>
                <td class="value">{{ $historia->pet->customer->document_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Sexo/Peso:</td>
                <td class="value">{{ isset($historia->pet->gender) ? ($historia->pet->gender === 'M' ? 'Macho' : 'Hembra') : 'N/A' }} / {{ $historia->weight ? $historia->weight . ' kg' : 'N/A' }}</td>
                <td class="label">Teléfono:</td>
                <td class="value">{{ $historia->pet->customer->phone ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="section-title">Anamnesis y Signos Vitales</div>
        
        <table class="layout">
            <tr>
                <td style="width: 50%;">
                    <div class="content-box" style="height: 100%;">
                        <h4>Motivo de Consulta</h4>
                        <p>{{ $historia->reason ?? 'No especificado' }}</p>
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="content-box" style="height: 100%;">
                        <h4>Anamnesis y Signos Clínicos</h4>
                        <p>{{ $historia->anamnesis ?? 'No especificados' }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="compact" style="margin-bottom: 8px;">
            <tr>
                <th>Temp. (°C)</th>
                <th>Frec. Cardíaca (bpm)</th>
                <th>Frec. Resp. (rpm)</th>
                <th>Cond. Corporal</th>
                <th>Hidratación</th>
                <th>Nivel Dolor</th>
            </tr>
            <tr>
                <td>{{ $historia->temperature ?? '-' }}</td>
                <td>{{ $historia->heart_rate ?? '-' }}</td>
                <td>{{ $historia->respiratory_rate ?? '-' }}</td>
                <td>{{ $historia->condicion_corporal ?? '-' }}</td>
                <td>{{ $historia->nivel_hidratacion ?? '-' }}</td>
                <td>{{ $historia->nivel_dolor ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">Examen Físico por Sistemas</div>
        <table class="grid">
            <tr>
                <td class="label" style="width:12%">Cardiovascular:</td>
                <td class="value" style="width:21%">{{ $historia->examen_cardiovascular ?? '-' }}</td>
                <td class="label" style="width:12%">Digestivo:</td>
                <td class="value" style="width:21%">{{ $historia->examen_digestivo ?? '-' }}</td>
                <td class="label" style="width:12%">Linfonodos:</td>
                <td class="value" style="width:22%">{{ $historia->examen_linfonodos ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Mucosas:</td>
                <td class="value">{{ $historia->examen_mucosas ?? '-' }}</td>
                <td class="label">Músculoesq.:</td>
                <td class="value">{{ $historia->examen_musculoesqueletico ?? '-' }}</td>
                <td class="label">Neurológico:</td>
                <td class="value">{{ $historia->examen_neurologico ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Ojos/Oídos:</td>
                <td class="value">{{ $historia->examen_ojos_oidos ?? '-' }}</td>
                <td class="label">Piel/Pelaje:</td>
                <td class="value">{{ $historia->examen_piel_pelaje ?? '-' }}</td>
                <td class="label">Respiratorio:</td>
                <td class="value">{{ $historia->examen_respiratorio ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Urinario:</td>
                <td class="value" colspan="5">{{ $historia->examen_urinario ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">Diagnóstico y Plan</div>

        <table class="layout">
            <tr>
                <td style="width: 50%;">
                    <div class="content-box" style="background-color: #f0fdf4; border-color: #86efac; height: 100%;">
                        <h4 style="color: #065f46; border-bottom-color: #bbf7d0;">Diagnóstico Presuntivo / Definitivo</h4>
                        <p>{{ $historia->diagnostico_presuntivo ?? 'No especificado' }}</p>
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="content-box" style="background-color: #eff6ff; border-color: #93c5fd; height: 100%;">
                        <h4 style="color: #1e40af; border-bottom-color: #bfdbfe;">Tratamiento e Indicaciones Médicas</h4>
                        <p>{{ $historia->tratamiento_indicaciones ?? 'No especificado' }}</p>
                    </div>
                </td>
            </tr>
        </table>

        @if($historia->prescripciones && count($historia->prescripciones) > 0)
        <div class="section-title">Receta Médica</div>
        <table class="compact">
            <thead>
                <tr>
                    <th style="width: 35%">Medicamento</th>
                    <th style="width: 15%">Dosis</th>
                    <th style="width: 15%">Frecuencia</th>
                    <th style="width: 15%">Vía</th>
                    <th style="width: 20%">Duración</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historia->prescripciones as $rx)
                <tr>
                    <td>
                        <strong>{{ $rx->producto->name ?? $rx->medicamento ?? 'Medicamento no especificado' }}</strong>
                        @if($rx->producto && ($rx->producto->presentacion || $rx->producto->principio_activo))
                        <br><span style="font-size: 9px; color: #6b7280;">{{ $rx->producto->presentacion }} ({{ $rx->producto->principio_activo }})</span>
                        @endif
                    </td>
                    <td>{{ $rx->dosage ?? '-' }}</td>
                    <td>{{ $rx->frequency ?? '-' }}</td>
                    <td>{{ $rx->via_administracion ?? '-' }}</td>
                    <td>{{ $rx->duracion_dias ? $rx->duracion_dias . ' días' : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($historia->notas_aclaratorias)
        <div class="content-box" style="margin-top: 8px;">
            <h4>Notas Adicionales</h4>
            <p>{{ $historia->notas_aclaratorias }}</p>
        </div>
        @endif

        @if($historia->proxima_cita_recomendada)
        <div class="content-box" style="margin-top: 8px; background-color: #fffbeb; border-color: #fde68a;">
            <p style="font-weight: bold; color: #92400e; margin: 0;">
                Próxima cita recomendada: {{ \Carbon\Carbon::parse($historia->proxima_cita_recomendada)->format('d/m/Y') }}
            </p>
        </div>
        @endif

        <div class="footer">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>{{ $historia->veterinario->name ?? 'N/A' }} {{ $historia->veterinario->last_name ?? '' }}</strong>
                <span>Médico Veterinario</span>
                @if(isset($historia->veterinario->cmvp) && $historia->veterinario->cmvp)
                <br><span>CMVP: {{ $historia->veterinario->cmvp }}</span>
                @endif
            </div>
            
            <div class="footer-text">
                Documento generado automáticamente por {{ config('app.name', 'VETCORESSEN') }} el {{ now()->format('d/m/Y H:i') }}.<br>
                Este reporte es de uso exclusivamente clínico y confidencial.
            </div>
        </div>
    </div>
</body>
</html>
