<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historia Clínica #{{ str_pad($historia->id, 6, '0', STR_PAD_LEFT) }}</title>
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
        table.grid td.label { width: 20%; color: #6b7280; font-weight: bold; }
        table.grid td.value { width: 30%; color: #111827; }
        
        /* Content boxes */
        .content-box { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; margin-bottom: 15px; }
        .content-box h4 { margin: 0 0 8px 0; color: #374151; font-size: 13px; text-transform: uppercase; }
        .content-box p { margin: 0; white-space: pre-wrap; color: #1f2937; }

        /* Prescriptions table */
        table.prescriptions { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.prescriptions th, table.prescriptions td { border: 1px solid #d1d5db; padding: 8px 10px; text-align: left; }
        table.prescriptions th { background-color: #f3f4f6; color: #374151; font-weight: bold; }
        
        /* Footer */
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #e5e7eb; position: relative; }
        .signature-box { width: 250px; text-align: center; float: right; margin-top: 20px; }
        .signature-line { border-top: 1px solid #9ca3af; padding-top: 5px; margin-bottom: 5px; }
        .signature-box strong { display: block; color: #111827; font-size: 14px; }
        .signature-box span { color: #6b7280; font-size: 12px; }
        .footer-text { clear: both; text-align: center; color: #9ca3af; font-size: 11px; margin-top: 60px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-logo">
                <h1>{{ config('app.name', 'VETCORESSEN') }}</h1>
                <p>Reporte Médico Clínico</p>
            </div>
            <div class="header-info">
                <p>Nº de Registro: <strong>{{ str_pad($historia->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                <p>Fecha de Consulta: <strong>{{ $historia->created_at->format('d/m/Y H:i') }}</strong></p>
            </div>
        </div>

        <div class="section-title">Información del Paciente y Propietario</div>
        <table class="grid">
            <tr>
                <td class="label">Paciente:</td>
                <td class="value"><strong>{{ $historia->pet->name ?? 'N/A' }}</strong></td>
                <td class="label">Propietario:</td>
                <td class="value">{{ $historia->pet->customer->nombre_completo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Especie / Raza:</td>
                <td class="value">{{ $historia->pet->species ?? 'N/A' }} / {{ $historia->pet->breed ?? 'N/A' }}</td>
                <td class="label">DNI / RUC:</td>
                <td class="value">{{ $historia->pet->customer->document_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Sexo / Peso:</td>
                <td class="value">{{ isset($historia->pet->gender) ? ($historia->pet->gender === 'M' ? 'Macho' : 'Hembra') : 'N/A' }} / {{ $historia->weight ? $historia->weight . ' kg' : 'N/A' }}</td>
                <td class="label">Teléfono:</td>
                <td class="value">{{ $historia->pet->customer->phone ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="section-title">Evaluación Clínica</div>
        
        <div class="content-box">
            <h4>Motivo de Consulta</h4>
            <p>{{ $historia->reason ?? 'No especificado' }}</p>
        </div>

        <div class="content-box">
            <h4>Anamnesis y Signos Clínicos</h4>
            <p>{{ $historia->anamnesis ?? 'No especificados' }}</p>
        </div>

        <div class="section-title">Diagnóstico y Tratamiento</div>

        <div class="content-box" style="background-color: #f0fdf4; border-color: #bbf7d0;">
            <h4 style="color: #065f46;">Diagnóstico Presuntivo / Definitivo</h4>
            <p>{{ $historia->diagnostico_presuntivo ?? 'No especificado' }}</p>
        </div>

        <div class="content-box" style="background-color: #eff6ff; border-color: #bfdbfe;">
            <h4 style="color: #1e40af;">Tratamiento e Indicaciones Médicas</h4>
            <p>{{ $historia->tratamiento_indicaciones ?? 'No especificado' }}</p>
        </div>

        @if($historia->prescripciones && count($historia->prescripciones) > 0)
        <div class="section-title">Receta Médica</div>
        <table class="prescriptions">
            <thead>
                <tr>
                    <th style="width: 40%">Medicamento</th>
                    <th style="width: 20%">Dosis</th>
                    <th style="width: 20%">Frecuencia</th>
                    <th style="width: 20%">Vía / Duración</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historia->prescripciones as $rx)
                <tr>
                    <td>
                        <strong>{{ $rx->producto->name ?? 'Medicamento eliminado' }}</strong>
                        @if($rx->producto && ($rx->producto->presentacion || $rx->producto->principio_activo))
                        <br><span style="font-size: 11px; color: #6b7280;">{{ $rx->producto->presentacion }} ({{ $rx->producto->principio_activo }})</span>
                        @endif
                    </td>
                    <td>{{ $rx->dose ?? '-' }}</td>
                    <td>{{ $rx->frequency ?? '-' }}</td>
                    <td>{{ $rx->route ?? '-' }}<br><span style="font-size: 11px;">Por {{ $rx->duration_days ?? '-' }} días</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($historia->notas_aclaratorias)
        <div class="section-title">Notas Adicionales</div>
        <div class="content-box">
            <p>{{ $historia->notas_aclaratorias }}</p>
        </div>
        @endif

        @if($historia->proxima_cita_recomendada)
        <p style="margin-top: 20px; font-weight: bold; color: #047857;">
            Próxima cita recomendada: {{ \Carbon\Carbon::parse($historia->proxima_cita_recomendada)->format('d/m/Y') }}
        </p>
        @endif

        <div class="footer">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>{{ $historia->veterinario->name ?? 'N/A' }} {{ $historia->veterinario->last_name ?? '' }}</strong>
                <span>Médico Veterinario</span>
                <br><span>CMVP: ____________</span>
            </div>
            
            <div class="footer-text">
                Documento generado automáticamente por el sistema {{ config('app.name', 'VETCORESSEN') }} el {{ now()->format('d/m/Y H:i') }}.<br>
                Este reporte es de uso exclusivamente clínico y confidencial.
            </div>
        </div>
    </div>
</body>
</html>
