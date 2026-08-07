<div x-data class="max-w-4xl mx-auto py-8">
    <x-slot:title>Historia Clínica - {{ $historia->pet->name }}</x-slot:title>

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
            <button onclick="window.print()" class="btn-primary text-sm flex items-center gap-2">
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
                        <span class="text-zinc-500">Nombre:</span> <span class="font-medium">{{ $historia->pet->name }}</span>
                        <span class="text-zinc-500">Especie:</span> <span>{{ $historia->pet->species->name ?? 'N/A' }}</span>
                        <span class="text-zinc-500">Raza:</span> <span>{{ $historia->pet->breed->name ?? 'N/A' }}</span>
                        <span class="text-zinc-500">Sexo:</span> <span>{{ $historia->pet->gender === 'M' ? 'Macho' : 'Hembra' }}</span>
                        <span class="text-zinc-500">Edad:</span> <span>{{ $historia->pet->birth_date ? $historia->pet->birth_date->age . ' años' : 'Desconocida' }}</span>
                        <span class="text-zinc-500">Peso (Ref):</span> <span>{{ $historia->weight ?? 'No registrado' }} kg</span>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-zinc-900 mb-2 border-b pb-1">Datos del Propietario</h3>
                    <div class="grid grid-cols-[100px_1fr] gap-y-1">
                        <span class="text-zinc-500">Nombre:</span> <span class="font-medium">{{ $historia->pet->customer->name_completo ?? 'N/A' }}</span>
                        <span class="text-zinc-500">DNI/RUC:</span> <span>{{ $historia->pet->customer->document_number ?? 'N/A' }}</span>
                        <span class="text-zinc-500">Teléfono:</span> <span>{{ $historia->pet->customer->phone ?? 'N/A' }}</span>
                        <span class="text-zinc-500">Email:</span> <span>{{ $historia->pet->customer->email ?? 'N/A' }}</span>
                        <span class="text-zinc-500">Dirección:</span> <span>{{ $historia->pet->customer->address ?? 'N/A' }}</span>
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
                    Anamnesis y Signos Clínicos
                </h3>
                <div class="bg-zinc-50 rounded p-4 border border-zinc-200">
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-zinc-700 mb-1">Motivo de Consulta:</h4>
                        <p class="text-sm whitespace-pre-wrap">{{ $historia->reason ?? 'No especificado' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-zinc-700 mb-1">Signos Clínicos:</h4>
                        <p class="text-sm whitespace-pre-wrap">{{ $historia->symptoms ?? 'No especificados' }}</p>
                    </div>
                </div>
            </div>

            {{-- Diagnostico --}}
            <div>
                <h3 class="text-lg font-bold text-emerald-700 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">medical_information</span>
                    Diagnóstico
                </h3>
                <div class="bg-emerald-50/50 rounded p-4 border border-emerald-100">
                    <p class="text-sm whitespace-pre-wrap font-medium">{{ $historia->diagnosis ?? 'No especificado' }}</p>
                </div>
            </div>

            {{-- Tratamiento --}}
            <div>
                <h3 class="text-lg font-bold text-blue-700 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-500">vaccines</span>
                    Tratamiento y Receta
                </h3>
                <div class="bg-blue-50/50 rounded p-4 border border-blue-100">
                    <p class="text-sm whitespace-pre-wrap">{{ $historia->treatment ?? 'Ninguno' }}</p>
                </div>
            </div>

            {{-- Observaciones adicionales --}}
            @if($historia->notes)
            <div>
                <h3 class="text-lg font-bold text-zinc-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-zinc-400">note_alt</span>
                    Notas Adicionales
                </h3>
                <div class="bg-white rounded p-4 border border-zinc-200">
                    <p class="text-sm whitespace-pre-wrap">{{ $historia->notes }}</p>
                </div>
            </div>
            @endif

        </div>

        {{-- Firma --}}
        <div class="bg-white border-t border-zinc-200 p-6 pt-12 flex justify-end">
            <div class="text-center w-64 border-t border-zinc-400 pt-2">
                <p class="font-bold text-zinc-900">{{ $historia->veterinarian->name }} {{ $historia->veterinarian->last_name }}</p>
                <p class="text-sm text-zinc-500">Médico Veterinario</p>
                <p class="text-xs text-zinc-400 mt-1">CMVP: ____________</p>
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
