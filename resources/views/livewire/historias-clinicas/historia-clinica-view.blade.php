<div x-data class="max-w-4xl mx-auto py-8">
    <x-slot:title>Historia Clínica - {{ $historia->pet?->name ?? 'Sin mascota' }}</x-slot:title>

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('historias.index') }}" class="btn-secondary text-sm flex items-center gap-2">
            <span class="material-symbols-outlined icon-sm">arrow_back</span>
            Volver a Historias
        </a>
        <div class="flex gap-2">
            <a href="{{ route('historias.editar', $historia->id) }}" class="btn-secondary text-sm flex items-center gap-2">
                <span class="material-symbols-outlined icon-sm">edit</span>
                Editar
            </a>
            <a href="{{ route('historias.pdf', $historia->id) }}" target="_blank" class="btn-primary text-sm flex items-center gap-2">
                <span class="material-symbols-outlined icon-sm">picture_as_pdf</span>
                Descargar PDF
            </a>
            <button onclick="window.print()" class="btn-secondary text-sm flex items-center gap-2">
                <span class="material-symbols-outlined icon-sm">print</span>
                Imprimir
            </button>
        </div>
    </div>

    {{-- Vista para imprimir y ver --}}
    <div id="historia-print-area" class="bg-white text-zinc-900 rounded-lg shadow-sm border border-zinc-200 overflow-hidden">
        {{-- Cabecera --}}
        <div class="bg-zinc-50 border-b border-zinc-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <span class="material-symbols-outlined text-2xl">pets</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900">{{ config('app.name', 'VetCore') }}</h1>
                        <p class="text-sm text-zinc-500">Reporte Médico Clínico</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-zinc-500">Nº de Registro: <span class="font-mono font-medium text-zinc-900">{{ str_pad($historia->id, 6, '0', STR_PAD_LEFT) }}</span></p>
                    <p class="text-sm text-zinc-500">Fecha: <span class="font-medium text-zinc-900">{{ $historia->created_at->format('d/m/Y H:i') }}</span></p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 bg-white p-4 rounded border border-zinc-200 text-sm">
                <div>
                    <h3 class="font-semibold text-zinc-900 mb-2 border-b pb-1">Datos del Paciente</h3>
                    <div class="grid grid-cols-[100px_1fr] gap-y-1">
                        <span class="text-zinc-500">Nombre:</span> <span class="font-medium">{{ $historia->pet?->name ?? 'N/A' }}</span>
                        <span class="text-zinc-500" x-text="$store.i18n.t('table.species') || 'Especie:'">Especie:</span> <span>{{ $historia->pet?->especie?->name ?? 'N/A' }}</span>
                        <span class="text-zinc-500" x-text="$store.i18n.t('table.breed') || 'Raza:'">Raza:</span> <span>{{ $historia->pet?->raza?->name ?? 'N/A' }}</span>
                        <span class="text-zinc-500">Sexo:</span> <span>{{ $historia->pet?->gender === 'M' ? 'Macho' : 'Hembra' }}</span>
                        <span class="text-zinc-500">Edad:</span> <span>{{ $historia->pet?->birth_date ? \Carbon\Carbon::parse($historia->pet->birth_date)->age . ' años' : 'Desconocida' }}</span>
                        <span class="text-zinc-500">Peso (Ref):</span> <span>{{ $historia->weight ?? 'No registrado' }} kg</span>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-zinc-900 mb-2 border-b pb-1">Datos del Propietario</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1 text-sm">
                            <span class="text-zinc-500">Nombre:</span> <span class="font-medium">{{ $historia->pet?->cliente?->nombre_completo ?? 'N/A' }}</span>
                            <span class="text-zinc-500">DNI/RUC:</span> <span>{{ $historia->pet?->cliente?->numero_documento ?? 'N/A' }}</span>
                        </div>
                        <div class="flex flex-col gap-1 text-sm">
                            <span class="text-zinc-500">Teléfono:</span> <span>{{ $historia->pet?->cliente?->phone ?? 'N/A' }}</span>
                            <span class="text-zinc-500">Email:</span> <span>{{ $historia->pet?->cliente?->email ?? 'N/A' }}</span>
                            <span class="text-zinc-500">Dirección:</span> <span>{{ $historia->pet?->cliente?->address ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenido Médico --}}
        <div class="p-6 space-y-6">
            
            {{-- Motivo y Signos --}}
            <div>
                <h3 class="text-lg font-bold text-zinc-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-zinc-400">assignment</span>
                    <span x-text="$store.i18n.t('report.reasonAndSigns') || 'Motivo y Signos Clínicos'"></span>
                </h3>
                <div class="bg-zinc-50 rounded p-4 border border-zinc-200">
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-zinc-700 mb-1" x-text="$store.i18n.t('report.reasonForVisit') || 'Motivo de Consulta:'"></h4>
                        <p class="text-sm whitespace-pre-wrap">{{ $historia->reason ?? 'No especificado' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-zinc-700 mb-1" x-text="$store.i18n.t('report.anamnesisSigns') || 'Anamnesis / Signos Clínicos:'"></h4>
                        <p class="text-sm whitespace-pre-wrap">{{ $historia->anamnesis ?? 'No especificados' }}</p>
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
            <div>
                <h3 class="text-lg font-bold text-teal-700 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-teal-500">stethoscope</span>
                    <span x-text="$store.i18n.t('form.physicalExam') || 'Examen Físico'"></span>
                </h3>
                <div class="bg-teal-50/30 rounded p-4 border border-teal-100">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                        @if($historia->weight)
                        <div><span class="text-xs text-zinc-500 block" x-text="$store.i18n.t('form.weight') || 'Peso'"></span><span class="font-medium text-sm">{{ $historia->weight }} kg</span></div>
                        @endif
                        @if($historia->temperature)
                        <div><span class="text-xs text-zinc-500 block" x-text="$store.i18n.t('form.temperature') || 'Temperatura'"></span><span class="font-medium text-sm">{{ $historia->temperature }} °C</span></div>
                        @endif
                        @if($historia->heart_rate)
                        <div><span class="text-xs text-zinc-500 block" x-text="$store.i18n.t('form.heartRate') || 'FC'"></span><span class="font-medium text-sm">{{ $historia->heart_rate }} lpm</span></div>
                        @endif
                        @if($historia->respiratory_rate)
                        <div><span class="text-xs text-zinc-500 block" x-text="$store.i18n.t('form.respRate') || 'FR'"></span><span class="font-medium text-sm">{{ $historia->respiratory_rate }} rpm</span></div>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-4 text-sm">
                        @if($historia->examen_mucosas)
                        <div><span class="text-zinc-500" x-text="$store.i18n.t('form.mucous') || 'Mucosas'"></span>: <span class="font-medium">{{ $historia->examen_mucosas }}</span></div>
                        @endif
                        @if($historia->examen_linfonodos)
                        <div><span class="text-zinc-500" x-text="$store.i18n.t('form.lymphNodes') || 'Linfonodos'"></span>: <span class="font-medium">{{ $historia->examen_linfonodos }}</span></div>
                        @endif
                        @if($historia->nivel_hidratacion)
                        <div><span class="text-zinc-500" x-text="$store.i18n.t('form.hydration') || 'Hidratación'"></span>: <span class="font-medium">{{ $historia->nivel_hidratacion }}</span></div>
                        @endif
                        @if($historia->condicion_corporal)
                        <div><span class="text-zinc-500" x-text="$store.i18n.t('form.bodyCondition') || 'Condición Corporal'"></span>: <span class="font-medium">{{ $historia->condicion_corporal }}/9</span></div>
                        @endif
                        @if($historia->nivel_dolor !== null)
                        <div><span class="text-zinc-500" x-text="$store.i18n.t('form.painLevel') || 'Nivel Dolor'"></span>: <span class="font-medium">{{ $historia->nivel_dolor }}/10</span></div>
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
                    @foreach($sistemas as $sis)
                        @if($sis['val'])
                            <div class="mt-3">
                                <h4 class="text-sm font-semibold text-zinc-700" x-text="$store.i18n.t('{{ $sis['label'] }}') || '{{ $sis['default'] }}'"></h4>
                                <p class="text-sm whitespace-pre-wrap">{{ $sis['val'] }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Diagnostico --}}
            <div>
                <h3 class="text-lg font-bold text-emerald-700 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">medical_information</span>
                    <span x-text="$store.i18n.t('report.diagnosis') || 'Diagnóstico'"></span>
                </h3>
                <div class="bg-emerald-50/50 rounded p-4 border border-emerald-100">
                    <p class="text-sm whitespace-pre-wrap font-medium">{{ $historia->diagnostico_presuntivo ?? 'No especificado' }}</p>
                </div>
            </div>

            {{-- Tratamiento --}}
            <div>
                <h3 class="text-lg font-bold text-blue-700 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-500">vaccines</span>
                    <span x-text="$store.i18n.t('report.treatmentIndications') || 'Tratamiento e Indicaciones'"></span>
                </h3>
                <div class="bg-blue-50/50 rounded p-4 border border-blue-100">
                    <p class="text-sm whitespace-pre-wrap">{{ $historia->tratamiento_indicaciones ?? 'Ninguno' }}</p>
                </div>
            </div>
            
            {{-- Prescripciones --}}
            @if($historia->prescripciones && count($historia->prescripciones) > 0)
            <div>
                <h3 class="text-lg font-bold text-violet-700 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-violet-500">prescriptions</span>
                    <span x-text="$store.i18n.t('report.prescriptions') || 'Prescripciones'"></span>
                </h3>
                <div class="bg-violet-50/30 rounded p-0 border border-violet-100 overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-violet-100/50 text-violet-800 border-b border-violet-200">
                            <tr>
                                <th class="px-4 py-2 font-semibold" x-text="$store.i18n.t('report.medication') || 'Medicamento'"></th>
                                <th class="px-4 py-2 font-semibold" x-text="$store.i18n.t('report.dose') || 'Dosis'"></th>
                                <th class="px-4 py-2 font-semibold" x-text="$store.i18n.t('report.frequency') || 'Frecuencia'"></th>
                                <th class="px-4 py-2 font-semibold" x-text="$store.i18n.t('report.route') || 'Vía'"></th>
                                <th class="px-4 py-2 font-semibold" x-text="$store.i18n.t('report.duration') || 'Duración'"></th>
                                <th class="px-4 py-2 font-semibold" x-text="$store.i18n.t('report.instructions') || 'Indicaciones'"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-violet-100">
                            @foreach($historia->prescripciones as $rx)
                            <tr>
                                <td class="px-4 py-3 font-medium text-zinc-800">{{ $rx->medicamento }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $rx->dosage }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $rx->frequency }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $rx->via_administracion }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $rx->duracion_dias }} d</td>
                                <td class="px-4 py-3 text-zinc-600 text-xs">{{ $rx->indicaciones }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Observaciones adicionales --}}
            @if($historia->notas_aclaratorias)
            <div>
                <h3 class="text-lg font-bold text-zinc-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-zinc-400">note_alt</span>
                    <span x-text="$store.i18n.t('report.additionalNotes') || 'Notas Adicionales'"></span>
                </h3>
                <div class="bg-white rounded p-4 border border-zinc-200">
                    <p class="text-sm whitespace-pre-wrap">{{ $historia->notas_aclaratorias }}</p>
                </div>
            </div>
            @endif

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

    {{-- CSS especial para impresion --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #historia-print-area, #historia-print-area * {
                visibility: visible;
                color: black !important;
            }
            #historia-print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 100%;
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
            }
            .bg-emerald-50\/50, .bg-blue-50\/50, .bg-zinc-50 {
                background-color: transparent !important;
            }
        }
    </style>
</div>
