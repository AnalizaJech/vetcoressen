<div x-data class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
    <x-slot:title>Historia Clínica - {{ $historia->pet?->name ?? 'Sin mascota' }}</x-slot:title>

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('historias.index') }}" class="btn-secondary text-sm flex items-center gap-2">
            <span class="material-symbols-outlined icon-sm">arrow_back</span>
            <span x-text="$store.i18n.t('btn.back')"></span>
        </a>
        <div class="flex gap-2">
            <a href="{{ route('historias.editar', $historia->id) }}" class="btn-secondary text-sm flex items-center gap-2">
                <span class="material-symbols-outlined icon-sm">edit</span>
                <span x-text="$store.i18n.t('btn.edit')"></span>
            </a>
            <a x-bind:href="'{{ route('historias.pdf', $historia->id) }}?lang=' + $store.i18n.locale" target="_blank" class="btn-primary text-sm flex items-center gap-2">
                <span class="material-symbols-outlined icon-sm">picture_as_pdf</span>
                <span x-text="$store.i18n.t('report.downloadPDF')"></span>
            </a>
        </div>
    </div>

    {{-- Vista para imprimir y ver --}}
    <div id="historia-print-area" class="bg-white text-zinc-900 rounded-2xl shadow-xl shadow-zinc-950/10 border border-zinc-200 overflow-hidden">
        {{-- Cabecera --}}
        <div class="bg-zinc-950 border-b-4 border-emerald-500 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div class="flex items-center gap-3">
                    @php
                        $clinic = \App\Models\Clinic::first();
                        $logoPath = $clinic && $clinic->logo ? asset('storage/' . $clinic->logo) : null;
                    @endphp
                    @if($logoPath)
                        <img src="{{ $logoPath }}" alt="Logo Clínica" class="w-12 h-12 rounded-xl object-contain bg-white/10 p-1">
                    @else
                        <div class="w-12 h-12 rounded-xl bg-emerald-400/15 border border-emerald-400/30 flex items-center justify-center text-emerald-300">
                            <span class="material-symbols-outlined text-2xl">pets</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white">{{ $clinic->name ?? config('app.name', 'VetCore') }}</h1>
                        <p class="text-sm text-emerald-200" x-text="$store.i18n.t('report.medicalReport')"></p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-sm text-zinc-300"><span x-text="$store.i18n.t('report.recordNo')"></span> <span class="font-mono font-semibold text-white">{{ str_pad($historia->id, 6, '0', STR_PAD_LEFT) }}</span></p>
                    <p class="text-sm text-zinc-300"><span x-text="$store.i18n.t('report.date')"></span> <span class="font-medium text-white">{{ $historia->created_at->format('d/m/Y H:i') }}</span></p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-7 text-sm">
                <div>
                    <h3 class="font-semibold text-emerald-400 mb-3 border-b border-emerald-500/30 pb-2 uppercase tracking-wider text-xs" x-text="$store.i18n.t('report.patientData')"></h3>
                    <div class="grid grid-cols-[100px_1fr] gap-y-1.5 text-zinc-300">
                        <span class="text-zinc-500" x-text="$store.i18n.t('form.nameLabel')"></span> <span class="font-medium text-white">{{ $historia->pet?->name ?? 'N/A' }}</span>
                        <span class="text-zinc-500" x-text="$store.i18n.t('table.species') || 'Especie:'">Especie:</span> <span class="text-white">{{ $historia->pet?->especie?->name ?? 'N/A' }}</span>
                        <span class="text-zinc-500" x-text="$store.i18n.t('table.breed') || 'Raza:'">Raza:</span> <span class="text-white">{{ $historia->pet?->raza?->name ?? 'N/A' }}</span>
                        <span class="text-zinc-500" x-text="$store.i18n.t('report.sex')"></span> <span class="text-white" x-text="$store.i18n.t('report.{{ $historia->pet?->gender === 'M' ? 'male' : 'female' }}')"></span>
                        <span class="text-zinc-500" x-text="$store.i18n.t('report.age')"></span> <span class="text-white">@if($historia->pet?->birth_date){{ \Carbon\Carbon::parse($historia->pet->birth_date)->age }} <span x-text="$store.i18n.t('report.years')"></span>@else<span x-text="$store.i18n.t('report.unknown')"></span>@endif</span>
                        <span class="text-zinc-500" x-text="$store.i18n.t('report.weightRef')"></span> <span class="text-white">@if($historia->weight){{ $historia->weight }} kg @else<span x-text="$store.i18n.t('report.notRegistered')"></span>@endif</span>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-emerald-400 mb-3 border-b border-emerald-500/30 pb-2 uppercase tracking-wider text-xs" x-text="$store.i18n.t('report.ownerData')"></h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5 text-sm text-zinc-300">
                            <span class="text-zinc-500" x-text="$store.i18n.t('form.nameLabel')"></span> <span class="font-medium text-white">{{ $historia->pet?->cliente?->nombre_completo ?? 'N/A' }}</span>
                            <span class="text-zinc-500">DNI/RUC:</span> <span class="text-white">{{ $historia->pet?->cliente?->numero_documento ?? 'N/A' }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5 text-sm text-zinc-300">
                            <span class="text-zinc-500" x-text="$store.i18n.t('report.phone')"></span> <span class="text-white">{{ $historia->pet?->cliente?->phone ?? 'N/A' }}</span>
                            <span class="text-zinc-500" x-text="$store.i18n.t('report.email')"></span> <span class="text-white">{{ $historia->pet?->cliente?->email ?? 'N/A' }}</span>
                            <span class="text-zinc-500" x-text="$store.i18n.t('report.address')"></span> <span class="text-white">{{ $historia->pet?->cliente?->address ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenido Médico --}}
        <div class="medical-record-content p-6 sm:p-8 bg-white">
            
            {{-- Primera fila: Motivo y Examen Físico --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Motivo y Signos --}}
                <div class="rounded-2xl bg-zinc-50/80 p-6 border border-zinc-200/60 h-full flex flex-col">
                    <h3 class="font-semibold text-zinc-900 mb-4 pb-3 border-b border-zinc-200/80 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-zinc-200/50 flex items-center justify-center text-zinc-500">
                            <span class="material-symbols-outlined icon-sm">assignment</span>
                        </div>
                        <span x-text="$store.i18n.t('report.reasonAndSigns') || 'Motivo y Signos Clínicos'"></span>
                    </h3>
                    <div class="flex-1 flex flex-col gap-5">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2" x-text="$store.i18n.t('report.reasonForVisit') || 'MOTIVO DE CONSULTA'"></h4>
                            <p class="text-sm whitespace-pre-wrap text-zinc-700 leading-relaxed">{{ $historia->reason ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2" x-text="$store.i18n.t('report.anamnesisSigns') || 'ANAMNESIS / SIGNOS CLÍNICOS'"></h4>
                            <p class="text-sm whitespace-pre-wrap text-zinc-700 leading-relaxed">{{ $historia->anamnesis ?? 'No especificados' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Examen Fisico --}}
                @if(
                    $historia->weight || $historia->temperature || $historia->heart_rate || $historia->respiratory_rate ||
                    $historia->examen_mucosas || $historia->examen_linfonodos || $historia->condicion_corporal ||
                    $historia->nivel_dolor !== null || $historia->nivel_hidratacion || $historia->examen_piel_pelaje ||
                    $historia->examen_ojos_oidos || $historia->examen_cardiovascular || $historia->examen_respiratorio ||
                    $historia->examen_digestivo || $historia->examen_musculoesqueletico || $historia->examen_neurologico ||
                    $historia->examen_urinario
                )
                <div class="rounded-2xl bg-emerald-50/30 p-6 border border-emerald-100/50 h-full flex flex-col">
                    <h3 class="font-semibold text-zinc-900 mb-4 pb-3 border-b border-emerald-100 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-100/50 flex items-center justify-center text-emerald-600">
                            <span class="material-symbols-outlined icon-sm">stethoscope</span>
                        </div>
                        <span x-text="$store.i18n.t('form.physicalExam') || 'Examen Físico'"></span>
                    </h3>
                    <div class="flex-1">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5 p-4 bg-white rounded-xl border border-emerald-50/80 shadow-sm">
                            @if($historia->weight)
                            <div class="text-center"><span class="text-xs font-semibold text-zinc-400 block uppercase" x-text="$store.i18n.t('form.weight') || 'PESO'"></span><span class="font-bold text-zinc-800">{{ $historia->weight }} <span class="text-xs text-zinc-500 font-normal">kg</span></span></div>
                            @endif
                            @if($historia->temperature)
                            <div class="text-center"><span class="text-xs font-semibold text-zinc-400 block uppercase" x-text="$store.i18n.t('form.temperature') || 'TEMP'"></span><span class="font-bold text-zinc-800">{{ $historia->temperature }} <span class="text-xs text-zinc-500 font-normal">°C</span></span></div>
                            @endif
                            @if($historia->heart_rate)
                            <div class="text-center"><span class="text-xs font-semibold text-zinc-400 block uppercase" x-text="$store.i18n.t('form.heartRate') || 'FC'"></span><span class="font-bold text-zinc-800">{{ $historia->heart_rate }} <span class="text-xs text-zinc-500 font-normal">lpm</span></span></div>
                            @endif
                            @if($historia->respiratory_rate)
                            <div class="text-center"><span class="text-xs font-semibold text-zinc-400 block uppercase" x-text="$store.i18n.t('form.respRate') || 'FR'"></span><span class="font-bold text-zinc-800">{{ $historia->respiratory_rate }} <span class="text-xs text-zinc-500 font-normal">rpm</span></span></div>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm">
                            @if($historia->examen_mucosas)
                            <div class="flex justify-between border-b border-emerald-100/30 pb-1"><span class="text-zinc-500" x-text="$store.i18n.t('form.mucous') || 'Mucosas'"></span> <span class="font-medium text-zinc-800 text-right">{{ $historia->examen_mucosas }}</span></div>
                            @endif
                            @if($historia->examen_linfonodos)
                            <div class="flex justify-between border-b border-emerald-100/30 pb-1"><span class="text-zinc-500" x-text="$store.i18n.t('form.lymphNodes') || 'Linfonodos'"></span> <span class="font-medium text-zinc-800 text-right">{{ $historia->examen_linfonodos }}</span></div>
                            @endif
                            @if($historia->nivel_hidratacion)
                            <div class="flex justify-between border-b border-emerald-100/30 pb-1"><span class="text-zinc-500" x-text="$store.i18n.t('form.hydration') || 'Hidratación'"></span> <span class="font-medium text-zinc-800 text-right">{{ $historia->nivel_hidratacion }}</span></div>
                            @endif
                            @if($historia->condicion_corporal)
                            <div class="flex justify-between border-b border-emerald-100/30 pb-1"><span class="text-zinc-500" x-text="$store.i18n.t('form.bodyCondition') || 'Condición Corp.'"></span> <span class="font-medium text-zinc-800 text-right">{{ $historia->condicion_corporal }}/9</span></div>
                            @endif
                            @if($historia->nivel_dolor !== null)
                            <div class="flex justify-between border-b border-emerald-100/30 pb-1"><span class="text-zinc-500" x-text="$store.i18n.t('form.painLevel') || 'Nivel Dolor'"></span> <span class="font-medium text-zinc-800 text-right">{{ $historia->nivel_dolor }}/10</span></div>
                            @endif
                        </div>
                        
                        @php
                            $sistemas = [
                                ['label' => 'form.skinCoat', 'default' => 'Piel y Pelaje', 'val' => $historia->examen_piel_pelaje],
                                ['label' => 'form.eyesEars', 'default' => 'Ojos y Oídos', 'val' => $historia->examen_ojos_oidos],
                                ['label' => 'form.cardiovascular', 'default' => 'Cardiovascular', 'val' => $historia->examen_cardiovascular],
                                ['label' => 'form.respiratory', 'default' => 'Respiratorio', 'val' => $historia->examen_respiratorio],
                                ['label' => 'form.digestive', 'default' => 'Digestivo / Abdomen', 'val' => $historia->examen_digestivo],
                                ['label' => 'form.musculoskeletal', 'default' => 'Musculoesquelético', 'val' => $historia->examen_musculoesqueletico],
                                ['label' => 'form.neurological', 'default' => 'Neurológico', 'val' => $historia->examen_neurologico],
                                ['label' => 'form.urinary', 'default' => 'Urinario / Genital', 'val' => $historia->examen_urinario],
                            ];
                        @endphp
                        @if(collect($sistemas)->filter(fn($s) => $s['val'])->isNotEmpty())
                            <div class="mt-4 pt-4 border-t border-emerald-100/50 space-y-3">
                                @foreach($sistemas as $sis)
                                    @if($sis['val'])
                                        <div>
                                            <h4 class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1" x-text="$store.i18n.t('{{ $sis['label'] }}') || '{{ $sis['default'] }}'"></h4>
                                            <p class="text-sm text-zinc-700">{{ $sis['val'] }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Segunda fila: Diagnóstico y Tratamiento --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Diagnostico --}}
                <div class="rounded-2xl bg-emerald-500/5 p-6 border border-emerald-500/10 h-full flex flex-col">
                    <h3 class="font-semibold text-emerald-900 mb-3 pb-2 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                            <span class="material-symbols-outlined icon-sm">medical_information</span>
                        </div>
                        <span x-text="$store.i18n.t('report.diagnosis') || 'Diagnóstico Presuntivo / Definitivo'"></span>
                    </h3>
                    <div class="flex-1 bg-white rounded-xl p-4 border border-emerald-500/5 shadow-sm">
                        <p class="text-sm whitespace-pre-wrap font-medium text-emerald-900">{{ $historia->diagnostico_presuntivo ?? 'No especificado' }}</p>
                    </div>
                </div>

                {{-- Tratamiento --}}
                <div class="rounded-2xl bg-blue-500/5 p-6 border border-blue-500/10 h-full flex flex-col">
                    <h3 class="font-semibold text-blue-900 mb-3 pb-2 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-600">
                            <span class="material-symbols-outlined icon-sm">vaccines</span>
                        </div>
                        <span x-text="$store.i18n.t('report.treatmentIndications') || 'Tratamiento e Indicaciones'"></span>
                    </h3>
                    <div class="flex-1 bg-white rounded-xl p-4 border border-blue-500/5 shadow-sm">
                        <p class="text-sm whitespace-pre-wrap text-blue-900">{{ $historia->tratamiento_indicaciones ?? 'Ninguno' }}</p>
                    </div>
                </div>
            </div>
            
            {{-- Tercera fila: Prescripciones y Notas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Prescripciones --}}
                @if($historia->prescripciones && count($historia->prescripciones) > 0)
                <div class="rounded-2xl bg-violet-50/50 p-6 border border-violet-100/50 h-full flex flex-col">
                    <h3 class="font-semibold text-violet-900 mb-4 pb-2 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center text-violet-600">
                            <span class="material-symbols-outlined icon-sm">prescriptions</span>
                        </div>
                        <span x-text="$store.i18n.t('report.prescriptions') || 'Receta Médica'"></span>
                    </h3>
                    <div class="flex-1 overflow-hidden bg-white border border-violet-100 rounded-xl shadow-sm">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-violet-50/50 text-violet-700 border-b border-violet-100/50">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider" x-text="$store.i18n.t('report.medication') || 'Medicamento'"></th>
                                    <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider" x-text="$store.i18n.t('report.dose') || 'Dosis'"></th>
                                    <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider" x-text="$store.i18n.t('report.frequency') || 'Frecuencia'"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-violet-50/50">
                                @foreach($historia->prescripciones as $rx)
                                <tr class="hover:bg-violet-50/20 transition-colors">
                                    <td class="px-4 py-3 font-medium text-zinc-800">
                                        {{ $rx->producto->name ?? $rx->medicamento }}<br>
                                        <span class="text-xs font-normal text-violet-600">{{ $rx->via_administracion }} - {{ $rx->duracion_dias }} d</span>
                                        @if($rx->indicaciones)<br><span class="text-xs italic text-zinc-500">{{ $rx->indicaciones }}</span>@endif
                                    </td>
                                    <td class="px-4 py-3 text-zinc-700 align-top">{{ $rx->dosage }}</td>
                                    <td class="px-4 py-3 text-zinc-700 align-top">{{ $rx->frequency }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                {{-- Placeholder para mantener el grid parejo si no hay prescripciones --}}
                <div class="hidden md:block"></div>
                @endif

                {{-- Observaciones adicionales --}}
                @if($historia->notas_aclaratorias || $historia->proxima_cita_recomendada)
                <div class="rounded-2xl bg-amber-50/30 p-6 border border-amber-100/50 h-full flex flex-col">
                    <h3 class="font-semibold text-amber-900 mb-4 pb-2 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-amber-100/50 flex items-center justify-center text-amber-600">
                            <span class="material-symbols-outlined icon-sm">note_alt</span>
                        </div>
                        <span x-text="$store.i18n.t('report.additionalNotes') || 'Notas Adicionales'"></span>
                    </h3>
                    <div class="flex-1 bg-white rounded-xl p-4 border border-amber-50 shadow-sm flex flex-col gap-3">
                        @if($historia->notas_aclaratorias)
                            <p class="text-sm whitespace-pre-wrap text-zinc-700">{{ $historia->notas_aclaratorias }}</p>
                        @endif
                        @if($historia->proxima_cita_recomendada)
                            <div class="mt-auto pt-3 border-t border-amber-50 flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-amber-500 icon-sm">event</span>
                                <strong class="text-amber-800" x-text="$store.i18n.t('form.recommendedNextAppt') || 'Próxima Cita Recomendada'"></strong>: 
                                <span class="text-zinc-800">{{ \Carbon\Carbon::parse($historia->proxima_cita_recomendada)->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

        </div>

        {{-- Firma --}}
        <div class="bg-white border-t border-zinc-200 p-6 pt-12 flex justify-end">
            <div class="text-center w-64 border-t border-zinc-400 pt-2">
                <p class="font-bold text-zinc-900">{{ $historia->veterinario->name ?? '' }} {{ $historia->veterinario->last_name ?? '' }}</p>
                <p class="text-sm text-zinc-500" x-text="$store.i18n.t('report.vetDoctor') || 'Médico Veterinario'"></p>
                <p class="text-xs text-zinc-400 mt-1" x-text="$store.i18n.t('report.cmvp') || 'CMVP: ____________'"></p>
            </div>
        </div>
    </div>

    {{-- CSS base para vista normal y estilos de impresión optimizados para A4 --}}
    <style>
        /* Estilos base de las tarjetas médicas */
        .medical-record-content > div > h3 {
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e4e4e7;
        }

        /* Impresión A4 optimizada — todos los datos visibles y compactos */
        @media print {
            @page { size: A4 portrait; margin: 8mm 10mm; }

            /* Ocultar todo excepto el área de impresión */
            body * { visibility: hidden; }
            #historia-print-area, #historia-print-area * {
                visibility: visible;
                color: #18181b !important;
            }

            /* Posición y dimensiones del área imprimible */
            #historia-print-area {
                position: absolute;
                top: 0;
                left: 0;
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 10px !important;
            }

            /* Header: fondo blanco con borde verde para impresión */
            #historia-print-area .bg-zinc-950 {
                background: #ffffff !important;
                border-bottom: 2px solid #059669 !important;
                padding: 8px 12px !important;
            }
            #historia-print-area .bg-zinc-950 *,
            #historia-print-area .text-white,
            #historia-print-area .text-zinc-300,
            #historia-print-area .text-emerald-200,
            #historia-print-area .text-emerald-300 {
                color: #18181b !important;
            }

            /* Grid de 2 columnas forzado para paciente + propietario */
            #historia-print-area .grid.grid-cols-1.md\:grid-cols-2 {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 8px !important;
                margin-top: 8px !important;
            }

            /* Tarjetas de datos compactas */
            #historia-print-area .rounded-xl.bg-white {
                padding: 8px 10px !important;
                border: 1px solid #d4d4d8 !important;
                border-radius: 4px !important;
                box-shadow: none !important;
            }
            #historia-print-area .rounded-xl.bg-white h3 {
                font-size: 11px !important;
                margin-bottom: 4px !important;
                padding-bottom: 3px !important;
            }
            #historia-print-area .grid.grid-cols-\[100px_1fr\] {
                grid-template-columns: 80px 1fr !important;
                gap: 1px 4px !important;
                font-size: 10px !important;
            }

            /* Contenido médico compacto */
            #historia-print-area .medical-record-content {
                padding: 6px 8px !important;
                gap: 6px !important;
                background: #ffffff !important;
            }
            #historia-print-area .medical-record-content > div {
                padding: 8px 10px !important;
                margin: 0 !important;
                border-left-width: 2px !important;
                border-radius: 3px !important;
                box-shadow: none !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            #historia-print-area .medical-record-content > div > h3 {
                font-size: 11px !important;
                padding-bottom: 4px !important;
                margin-bottom: 4px !important;
            }

            /* Reducir espacios generales */
            #historia-print-area .p-6,
            #historia-print-area .sm\:p-8 {
                padding: 8px !important;
            }
            #historia-print-area .space-y-7 > :not([hidden]) ~ :not([hidden]) {
                margin-top: 6px !important;
            }
            #historia-print-area .mb-3 { margin-bottom: 3px !important; }
            #historia-print-area .mb-4 { margin-bottom: 4px !important; }
            #historia-print-area .mt-7 { margin-top: 8px !important; }
            #historia-print-area .gap-4 { gap: 4px !important; }
            #historia-print-area .gap-5 { gap: 6px !important; }

            /* Texto compacto para A4 */
            #historia-print-area .text-lg { font-size: 12px !important; }
            #historia-print-area .text-sm { font-size: 10px !important; }
            #historia-print-area .text-xs { font-size: 9px !important; }
            #historia-print-area .text-2xl { font-size: 16px !important; }

            /* Fondos transparentes para ahorrar tinta */
            .bg-emerald-50\/50, .bg-blue-50\/50, .bg-zinc-50,
            .bg-teal-50\/30, .bg-violet-50\/30, .bg-zinc-50\/60 {
                background-color: #ffffff !important;
            }

            /* Tablas de prescripciones legibles */
            #historia-print-area table {
                font-size: 10px !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            #historia-print-area table th {
                background-color: #e4e4e7 !important;
                color: #18181b !important;
                padding: 3px 6px !important;
            }
            #historia-print-area table td {
                padding: 3px 6px !important;
            }

            /* Firma al final sin corte */
            #historia-print-area .bg-white.border-t {
                padding: 8px 12px !important;
                padding-top: 20px !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            /* Ocultar botones y navegación */
            .btn-primary, .btn-secondary, a[href*="route"],
            nav, aside, header, footer:not(.footer-text) {
                display: none !important;
            }
        }
    </style>
</div>
