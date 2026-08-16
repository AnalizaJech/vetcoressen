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
    <title>Historial Clínico - {{ $mascota->name }}</title>
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
        .section-title { font-size: 16px; font-weight: bold; color: #059669; border-bottom: 1px solid #d1d5db; padding-bottom: 5px; margin-bottom: 15px; margin-top: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
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
        .content-box p { margin: 0; white-space: pre-wrap; color: #1f2937; font-size: 11px; }

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
                <p>Historial Clínico Completo</p>
            </div>
            <div class="header-info">
                <p>Generado el: <strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
                <p>Total registros: <strong>{{ $mascota->historiasClinicas->count() }}</strong></p>
            </div>
        </div>

        <div class="section-title">Información del Paciente y Propietario</div>
        <table class="grid">
            <tr>
                <td class="label">Paciente:</td>
                <td class="value"><strong>{{ $mascota->name ?? 'N/A' }}</strong></td>
                <td class="label">Propietario:</td>
                <td class="value">{{ $mascota->cliente?->nombre_completo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Especie / Raza:</td>
                <td class="value">{{ $mascota->especie->name ?? 'N/A' }} / {{ $mascota->raza->name ?? 'N/A' }}</td>
                <td class="label">DNI / RUC:</td>
                <td class="value">{{ $mascota->cliente?->numero_documento ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Sexo / Peso (último):</td>
                <td class="value">{{ isset($mascota->gender) ? ($mascota->gender === 'M' ? 'Macho' : 'Hembra') : 'N/A' }} / {{ $mascota->historiasClinicas->first()?->weight ? $mascota->historiasClinicas->first()->weight . ' kg' : 'N/A' }}</td>
                <td class="label">Teléfono:</td>
                <td class="value">{{ $mascota->cliente?->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Edad:</td>
                <td class="value">{{ $mascota->birth_date ? \Carbon\Carbon::parse($mascota->birth_date)->age . ' años' : 'N/A' }}</td>
                <td class="label">Email / Dir.:</td>
                <td class="value">{{ $mascota->cliente?->email ?? '-' }} <br> <span style="font-size: 9px; color: #4b5563;">{{ $mascota->cliente?->address ?? '-' }}</span></td>
            </tr>
        </table>

        @if($mascota->historiasClinicas->isEmpty())
            <div style="text-align: center; padding: 50px; color: #6b7280; font-size: 14px; background-color: #f9fafb; border-radius: 8px; margin-top: 30px;">
                No hay registros clínicos para esta mascota.
            </div>
        @else
            @foreach($mascota->historiasClinicas as $historia)
                <div class="avoid-break">
                    <div class="record-title">
                        <table style="width: 100%; border: none;">
                            <tr>
                                <td style="width: 50%; text-align: left;">
                                    Fecha: {{ $historia->date ? $historia->date->format('d/m/Y h:i A') : $historia->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td style="width: 50%; text-align: right; color: #4b5563; font-weight: normal; font-size: 12px;">
                                    Atendido por: {{ $historia->veterinario->name ?? 'N/A' }} {{ $historia->veterinario->last_name ?? '' }}
                                    @if(isset($historia->veterinario->cmvp) && $historia->veterinario->cmvp)
                                        <br><span style="font-size: 10px;">CMVP: {{ $historia->veterinario->cmvp }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table class="grid" style="font-size: 11px;">
                        <tr>
                            <td class="label" style="width: 15%;">Motivo:</td>
                            <td class="value" style="width: 85%;">{{ $historia->reason ?? 'No especificado' }}</td>
                        </tr>
                        @if($historia->weight || $historia->temperature || $historia->heart_rate || $historia->respiratory_rate)
                        <tr>
                            <td class="label" style="width: 15%;">Signos Vitales:</td>
                            <td class="value" style="width: 85%;">
                                {{ $historia->weight ? 'Peso: '.$historia->weight.' kg' : '' }}
                                {{ $historia->temperature ? ' | Temp: '.$historia->temperature.' °C' : '' }}
                                {{ $historia->heart_rate ? ' | FC: '.$historia->heart_rate.' lpm' : '' }}
                                {{ $historia->respiratory_rate ? ' | FR: '.$historia->respiratory_rate.' rpm' : '' }}
                                {{ $historia->condicion_corporal ? ' | Cond. Corp: '.$historia->condicion_corporal : '' }}
                                {{ $historia->nivel_hidratacion ? ' | Hidratación: '.$historia->nivel_hidratacion : '' }}
                                {{ $historia->nivel_dolor ? ' | Dolor: '.$historia->nivel_dolor : '' }}
                            </td>
                        </tr>
                        @endif
                        
                        <tr>
                            <td class="label" style="width: 15%;">Examen Físico:</td>
                            <td class="value" style="width: 85%;">
                                <table style="width: 100%; font-size: 10px; margin-top: 2px;">
                                    <tr>
                                        <td style="width: 50%"><strong>Cardiovascular:</strong> {{ $historia->examen_cardiovascular ?? '-' }}</td>
                                        <td style="width: 50%"><strong>Digestivo:</strong> {{ $historia->examen_digestivo ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Linfonodos:</strong> {{ $historia->examen_linfonodos ?? '-' }}</td>
                                        <td><strong>Mucosas:</strong> {{ $historia->examen_mucosas ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Músculoesquelético:</strong> {{ $historia->examen_musculoesqueletico ?? '-' }}</td>
                                        <td><strong>Neurológico:</strong> {{ $historia->examen_neurologico ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ojos/Oídos:</strong> {{ $historia->examen_ojos_oidos ?? '-' }}</td>
                                        <td><strong>Piel/Pelaje:</strong> {{ $historia->examen_piel_pelaje ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Respiratorio:</strong> {{ $historia->examen_respiratorio ?? '-' }}</td>
                                        <td><strong>Urinario:</strong> {{ $historia->examen_urinario ?? '-' }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table class="layout" style="margin-top: 15px;">
                        <tr>
                            <td style="width: 50%;">
                                <div class="content-box">
                                    <h4>Anamnesis y Signos Clínicos</h4>
                                    <p>{{ $historia->anamnesis ?? 'No especificados' }}</p>
                                </div>
                            </td>
                            <td style="width: 50%;">
                                <div class="content-box" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                                    <h4 style="color: #065f46;">Diagnóstico Presuntivo / Definitivo</h4>
                                    <p>{{ $historia->diagnostico_presuntivo ?? 'No especificado' }}</p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%;">
                                <div class="content-box" style="background-color: #eff6ff; border-color: #bfdbfe;">
                                    <h4 style="color: #1e40af;">Tratamiento e Indicaciones Médicas</h4>
                                    <p>{{ $historia->tratamiento_indicaciones ?? 'No especificado' }}</p>
                                </div>
                            </td>
                            <td style="width: 50%;">
                                @if($historia->notas_aclaratorias || $historia->proxima_cita_recomendada)
                                <div class="content-box" style="background-color: #fffbeb; border-color: #fde68a;">
                                    <h4 style="color: #b45309;">Notas Adicionales</h4>
                                    @if($historia->notas_aclaratorias)
                                        <p style="margin-bottom: 5px;">{{ $historia->notas_aclaratorias }}</p>
                                    @endif
                                    @if($historia->proxima_cita_recomendada)
                                        <p><strong>Próxima Cita Recomendada:</strong> {{ $historia->proxima_cita_recomendada->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if($historia->prescripciones && count($historia->prescripciones) > 0)
                    <table class="prescriptions">
                        <thead>
                            <tr>
                                <th style="width: 25%">Medicamento Recetado</th>
                                <th style="width: 15%">Dosis</th>
                                <th style="width: 15%">Frecuencia</th>
                                <th style="width: 20%">Vía / Duración</th>
                                <th style="width: 25%">Indicaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historia->prescripciones as $rx)
                            <tr>
                                <td>
                                    <strong>{{ $rx->producto->name ?? $rx->medicamento ?? 'Medicamento eliminado' }}</strong>
                                </td>
                                <td>{{ $rx->dosage ?? '-' }}</td>
                                <td>{{ $rx->frequency ?? '-' }}</td>
                                <td>
                                    {{ $rx->via_administracion ?? '-' }} <br>
                                    <span style="color: #6b7280; font-size: 9px;">{{ $rx->duration ?? ($rx->duracion_dias ? $rx->duracion_dias . ' días' : '-') }}</span>
                                </td>
                                <td>{{ $rx->indicaciones ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            @endforeach
        @endif

        <div class="footer">
            <div class="footer-text">
                Documento generado automáticamente por el sistema {{ config('app.name', 'VETCORESSEN') }} el {{ now()->format('d/m/Y H:i') }}.<br>
                Este reporte contiene el historial clínico completo del paciente hasta la fecha indicada y es de uso confidencial.
            </div>
        </div>
    </div>
</body>
</html>
