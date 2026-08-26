<div>
    <x-slot:title>Historias Clínicas</x-slot:title>

    {{-- ═══ Header de Historias Clínicas (Estándar Premium) ═══ --}}
    <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            @if($clienteSeleccionado)
                <button wire:click="volver" class="p-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 transition-all flex items-center gap-1.5 text-xs font-bold shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span x-text="$store.i18n.t('btn.back') || 'Volver'">Volver</span>
                </button>
            @else
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <span class="material-symbols-outlined text-2xl">medical_information</span>
                </div>
            @endif
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                    <span x-text="$store.i18n.t('records.title') || 'Historias Clínicas'">Historias Clínicas</span>
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('records.subtitle') || 'Registro y seguimiento clínico integral de pacientes'">
                    Registro y seguimiento clínico integral de pacientes
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('historias.crear') }}" wire:navigate class="btn-primary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newRecord') || 'Nueva Historia'">Nueva Historia</span>
            </a>
        </div>
    </div>

    @if(!$clienteSeleccionado)
        {{-- ═══ Barra de Filtros Dinámicos (Estilo Reportes) ═══ --}}
        <div class="vc-panel mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                {{-- Filtro de Cliente --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('table.client') || 'Cliente'">
                        Cliente
                    </label>
                    <x-vc-dropdown
                        wire:model.live="filtroCliente"
                        :options="$clientesOptions"
                        :selected="$filtroCliente"
                        placeholder="filter.allClients"
                        icon="person"
                        searchable
                    />
                </div>

                {{-- Filtro de Mascota --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('filter.pet') || 'Mascota'">
                        Mascota
                    </label>
                    <x-vc-dropdown
                        wire:model.live="filtroMascota"
                        :options="$mascotasOptions"
                        :selected="$filtroMascota"
                        placeholder="filter.allPets"
                        icon="pets"
                        searchable
                    />
                </div>

                {{-- Filtro de Especie --}}
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('filter.species') || 'Especie'">
                        Especie
                    </label>
                    <x-vc-dropdown
                        wire:model.live="especie_id"
                        :options="$especiesOptions"
                        :selected="$especie_id"
                        placeholder="filter.allSpecies"
                        icon="category"
                    />
                </div>
            </div>
        </div>

        {{-- ═══ Grid de Tarjetas de Clientes (Diseño Premium) ═══ --}}
        @if($clientes->isEmpty())
            <div class="vc-panel py-16 text-center text-zinc-400">
                <span class="material-symbols-outlined text-4xl mb-2 text-zinc-300 dark:text-zinc-600">folder_open</span>
                <h3 class="text-sm font-bold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('table.noRecordsFound') || 'No se encontraron registros'">
                    No se encontraron registros
                </h3>
                <p class="text-xs text-zinc-400 mt-1" x-text="$store.i18n.t('table.emptyText') || 'No hay clientes o historias que coincidan con la búsqueda.'">
                    No hay clientes o historias que coincidan con la búsqueda.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($clientes as $cliente)
                    <div wire:key="cliente-{{ $cliente->id }}" 
                         wire:click="seleccionarCliente({{ $cliente->id }})"
                         class="vc-card p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200/80 dark:border-zinc-800 shadow-xs hover:shadow-md hover:border-emerald-500/50 cursor-pointer transition-all duration-200 group flex flex-col justify-between relative overflow-hidden">
                        
                        {{-- Acento visual superior sutil --}}
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div>
                            {{-- Header Cliente --}}
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-500/10 dark:to-teal-500/10 border border-emerald-200/60 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-extrabold text-sm shadow-xs shrink-0">
                                        {{ strtoupper(substr($cliente->first_name, 0, 1) . substr($cliente->last_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-extrabold text-sm text-zinc-900 dark:text-zinc-100 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                            {{ $cliente->nombre_completo }}
                                        </h3>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="badge badge-gray text-[10px] py-0.5 px-1.5 font-mono">
                                                {{ $cliente->tipo_documento ?? 'DNI' }}: {{ $cliente->numero_documento }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-7 h-7 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 flex items-center justify-center text-zinc-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-950/40 transition-all">
                                    <span class="material-symbols-outlined text-base group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                                </div>
                            </div>

                            {{-- Datos de Contacto y Última Atención --}}
                            <div class="space-y-2 text-xs text-zinc-600 dark:text-zinc-400 mb-4 bg-zinc-50/50 dark:bg-zinc-800/20 p-2.5 rounded-xl border border-zinc-100 dark:border-zinc-800/60">
                                <div class="flex items-center justify-between">
                                    <span class="text-zinc-400 text-[11px] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">call</span>
                                        <span x-text="$store.i18n.t('table.phone') || 'Teléfono'">Teléfono</span>
                                    </span>
                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $cliente->phone ?: '-' }}</span>
                                </div>
                                @php
                                    $ultHistoria = $cliente->mascotas->flatMap->historiasClinicas->sortByDesc('date')->first();
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="text-zinc-400 text-[11px] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">event_available</span>
                                        <span x-text="$store.i18n.t('table.lastVisit') || 'Última cita'">Última cita</span>
                                    </span>
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                        {{ $ultHistoria ? \Carbon\Carbon::parse($ultHistoria->date)->format('d/m/Y') : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Mascotas del Cliente --}}
                        <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-emerald-500">pets</span>
                                <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                    <span x-text="{{ $cliente->mascotas->count() }} + ' ' + ({{ $cliente->mascotas->count() }} === 1 ? ($store.i18n.t('table.pet') || 'mascota') : ($store.i18n.t('table.pets') || 'mascotas'))">
                                        {{ $cliente->mascotas->count() }} {{ $cliente->mascotas->count() === 1 ? 'mascota' : 'mascotas' }}
                                    </span>
                                </span>
                            </div>
                            
                            {{-- Chips con nombres de mascotas --}}
                            <div class="flex items-center gap-1 overflow-hidden max-w-[55%]">
                                @foreach($cliente->mascotas->take(3) as $m)
                                    <span class="px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[10px] font-semibold text-zinc-600 dark:text-zinc-300 truncate max-w-[70px]" title="{{ $m->name }}">
                                        {{ $m->name }}
                                    </span>
                                @endforeach
                                @if($cliente->mascotas->count() > 3)
                                    <span class="px-1.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-[10px] font-extrabold">
                                        +{{ $cliente->mascotas->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-center">
                {{ $clientes->links() }}
            </div>
        @endif
    @else
        {{-- ═══ Vista Detalle: Mascotas del Cliente y sus Historias Clínicas ═══ --}}
        <div class="space-y-6 animate-slide-up" x-data="{ historiasAbiertas: {} }">
            {{-- Panel Superior: Info del Cliente Seleccionado --}}
            <div class="vc-panel flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-500/10 dark:to-teal-500/10 border border-emerald-200/60 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-extrabold text-base shadow-xs shrink-0">
                        {{ strtoupper(substr($clienteSeleccionado->first_name, 0, 1) . substr($clienteSeleccionado->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                                {{ $clienteSeleccionado->nombre_completo }}
                            </h2>
                            <span class="badge badge-emerald text-[11px] font-mono">
                                {{ $clienteSeleccionado->tipo_documento ?? 'DNI' }}: {{ $clienteSeleccionado->numero_documento }}
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 flex flex-wrap items-center gap-3">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">call</span>
                                @if($clienteSeleccionado->phone)
                                    <span>{{ $clienteSeleccionado->phone }}</span>
                                @else
                                    <span x-text="$store.i18n.t('misc.noPhone') || 'Sin teléfono'">Sin teléfono</span>
                                @endif
                            </span>
                            @if($clienteSeleccionado->email)
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">mail</span>
                                    {{ $clienteSeleccionado->email }}
                                </span>
                            @endif
                            @if($clienteSeleccionado->address)
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    {{ $clienteSeleccionado->address }}
                                </span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if($mascotaSeleccionadaId || $mascota_id)
                        <button type="button" wire:click="limpiarFiltroMascota" class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/40 hover:bg-blue-100 flex items-center gap-1 transition-all" title="Ver todas las mascotas del propietario">
                            <span class="material-symbols-outlined text-xs">filter_alt</span>
                            <span x-text="$store.i18n.t('misc.filteredPet') || 'Mascota filtrada'">Mascota filtrada</span>
                            <span class="material-symbols-outlined text-xs ml-0.5">close</span>
                        </button>
                    @endif
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40">
                        <span x-text="{{ $clienteSeleccionado->mascotas->count() }} + ' ' + ({{ $clienteSeleccionado->mascotas->count() }} === 1 ? ($store.i18n.locale === 'en' ? 'Pet' : 'Mascota') : ($store.i18n.locale === 'en' ? 'Pets' : 'Mascotas'))">{{ $clienteSeleccionado->mascotas->count() }} Mascotas</span>
                    </span>
                </div>
            </div>

            {{-- Mascotas del Cliente --}}
            <div class="grid grid-cols-1 gap-6">
                @forelse($clienteSeleccionado->mascotas as $mascota)
                    <div wire:key="mascota-{{ $mascota->id }}" class="vc-card overflow-hidden border border-zinc-200/80 dark:border-zinc-800 rounded-2xl bg-white dark:bg-vc-surface shadow-xs">
                        {{-- Header Mascota --}}
                        <div class="p-4 bg-zinc-50/70 dark:bg-zinc-800/40 border-b border-zinc-200/60 dark:border-zinc-800/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                                    <span class="material-symbols-outlined text-[24px]">pets</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-extrabold text-base text-zinc-900 dark:text-zinc-100 font-display">{{ $mascota->name }}</h3>
                                        <span class="badge badge-emerald text-[10px]">
                                            {{ $mascota->especie->name ?? 'Mascota' }}
                                        </span>
                                        <span class="badge badge-gray text-[10px]" x-text="{{ $mascota->gender === 'M' ? 'true' : 'false' }} ? ($store.i18n.t('form.genderMale') || 'Male') : ($store.i18n.t('form.genderFemale') || 'Female')">
                                            {{ $mascota->gender === 'M' ? 'Macho' : 'Hembra' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 flex items-center gap-2">
                                        <span>{{ $mascota->raza->name ?? 'Mestizo' }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $mascota->edad_texto }}</span>
                                        @if($mascota->weight)
                                            <span>&bull;</span>
                                            <span>{{ $mascota->weight }} kg</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a x-bind:href="'{{ route('mascotas.historial.pdf', $mascota->id) }}?lang=' + ($store.i18n?.locale || localStorage.getItem('vc_locale') || 'es')" target="_blank" class="px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 border border-emerald-200/50 dark:border-emerald-800/40 transition-all flex items-center gap-1.5 shadow-xs" x-bind:title="$store.i18n.t('report.downloadHistoryPDF') || 'Download History (PDF)'">
                                    <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                                    <span x-text="$store.i18n.t('report.downloadHistoryPDF') || 'Download PDF'">Download PDF</span>
                                </a>
                            </div>
                        </div>

                        {{-- Lista de Historias Clínicas de la Mascota --}}
                        <div class="p-4">
                            @if($mascota->historiasClinicas->isEmpty())
                                <div class="py-8 text-center text-zinc-400 text-xs">
                                    <span class="material-symbols-outlined text-3xl text-zinc-300 dark:text-zinc-600 mb-1 block">clinical_notes</span>
                                    <p x-text="$store.i18n.t('records.noRecords') || 'No hay historias clínicas registradas para esta mascota.'">
                                        No hay historias clínicas registradas para esta mascota.
                                    </p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($mascota->historiasClinicas as $historia)
                                        <div wire:key="historia-{{ $historia->id }}" class="border border-zinc-200/80 dark:border-zinc-800 rounded-xl overflow-hidden bg-zinc-50/40 dark:bg-zinc-800/30 transition-all hover:border-emerald-500/30">
                                            {{-- Encabezado Historia --}}
                                            <div class="p-3.5 flex items-center justify-between cursor-pointer select-none" @click="historiasAbiertas[{{ $historia->id }}] = !historiasAbiertas[{{ $historia->id }}]">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xs shrink-0">
                                                        <span class="material-symbols-outlined text-[18px]">stethoscope</span>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs font-extrabold text-zinc-900 dark:text-zinc-100">
                                                                {{ \Carbon\Carbon::parse($historia->date)->format('M d, Y') }}
                                                            </span>
                                                            <span class="badge badge-blue text-[10px]">
                                                                {{ $historia->veterinario->name ?? 'Veterinario' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 truncate max-w-md">
                                                            <span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="($store.i18n.t('form.consultReason') || 'Motivo') + ': '">Motivo: </span>
                                                            {{ $historia->reason ?? 'Consulta General' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    {{-- Botón Ver Ficha Completa (Indigo) --}}
                                                    <a href="{{ route('historias.ver', $historia->id) }}" wire:navigate class="p-2 rounded-xl text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-500/20 border border-indigo-200/60 dark:border-indigo-500/20 transition-all shadow-xs flex items-center justify-center" @click.stop x-bind:title="$store.i18n.t('btn.viewRecord') || 'Ver Ficha'">
                                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                    </a>

                                                    {{-- Botón Imprimir PDF (Emerald) --}}
                                                    <a x-bind:href="'{{ route('historias.pdf', $historia->id) }}?lang=' + ($store.i18n?.locale || localStorage.getItem('vc_locale') || 'es')" target="_blank" class="p-2 rounded-xl text-emerald-600 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20 border border-emerald-200/60 dark:border-emerald-500/20 transition-all shadow-xs flex items-center justify-center" @click.stop x-bind:title="$store.i18n.t('btn.downloadPDF') || 'Descargar PDF'">
                                                        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                                                    </a>

                                                    {{-- Botón Editar (Amber) --}}
                                                    <a href="{{ route('historias.editar', $historia->id) }}" wire:navigate class="p-2 rounded-xl text-amber-600 bg-amber-50 hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-500/20 border border-amber-200/60 dark:border-amber-500/20 transition-all shadow-xs flex items-center justify-center" @click.stop x-bind:title="$store.i18n.t('btn.edit') || 'Editar'">
                                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                                    </a>

                                                    {{-- Botón Eliminar (Red) --}}
                                                    <button type="button" wire:click="abrirModalEliminar({{ $historia->id }})" class="p-2 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 border border-red-200/60 dark:border-red-500/20 transition-all shadow-xs flex items-center justify-center" @click.stop x-bind:title="$store.i18n.t('btn.delete') || 'Eliminar'">
                                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    </button>

                                                    <div class="p-1 text-zinc-400 transition-transform duration-200" :class="historiasAbiertas[{{ $historia->id }}] ? 'rotate-180' : ''">
                                                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Detalle Desplegable Historia --}}
                                            <div x-show="historiasAbiertas[{{ $historia->id }}]" x-collapse class="p-4 bg-white dark:bg-zinc-900 border-t border-zinc-200/80 dark:border-zinc-800 text-xs">
                                                {{-- Triaje y Signos Vitales --}}
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800">
                                                    <div>
                                                        <span class="text-[10px] uppercase font-bold text-zinc-400" x-text="$store.i18n.t('form.weight') || 'Peso'">Peso</span>
                                                        <p class="font-bold text-zinc-900 dark:text-zinc-100 mt-0.5">{{ $historia->weight ? $historia->weight . ' kg' : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-[10px] uppercase font-bold text-zinc-400" x-text="$store.i18n.t('form.temperature') || 'Temperatura'">Temperatura</span>
                                                        <p class="font-bold text-zinc-900 dark:text-zinc-100 mt-0.5">{{ $historia->temperature ? $historia->temperature . ' °C' : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-[10px] uppercase font-bold text-zinc-400" x-text="$store.i18n.t('form.heartRate') || 'Frec. Cardíaca'">Frec. Cardíaca</span>
                                                        <p class="font-bold text-zinc-900 dark:text-zinc-100 mt-0.5">{{ $historia->heart_rate ? $historia->heart_rate . ' bpm' : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-[10px] uppercase font-bold text-zinc-400" x-text="$store.i18n.t('form.respRate') || 'Frec. Resp.'">Frec. Resp.</span>
                                                        <p class="font-bold text-zinc-900 dark:text-zinc-100 mt-0.5">{{ $historia->respiratory_rate ? $historia->respiratory_rate . ' rpm' : '-' }}</p>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <h4 class="font-bold text-zinc-800 dark:text-zinc-200 mb-1 flex items-center gap-1.5">
                                                            <span class="material-symbols-outlined text-emerald-500 text-[15px]">medical_services</span>
                                                            <span x-text="$store.i18n.t('report.diagnosis') || 'Diagnóstico Presuntivo'">Diagnóstico Presuntivo</span>
                                                        </h4>
                                                        <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed bg-zinc-50 dark:bg-zinc-800/30 p-2.5 rounded-lg border border-zinc-100 dark:border-zinc-800">
                                                            {{ $historia->diagnostico_presuntivo }}
                                                            @if(!$historia->diagnostico_presuntivo)
                                                                <span x-text="$store.i18n.t('misc.notSpecified') || 'No especificado'">No especificado</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-zinc-800 dark:text-zinc-200 mb-1 flex items-center gap-1.5">
                                                            <span class="material-symbols-outlined text-blue-500 text-[15px]">medication</span>
                                                            <span x-text="$store.i18n.t('report.treatmentIndications') || 'Tratamiento e Indicaciones'">Tratamiento e Indicaciones</span>
                                                        </h4>
                                                        <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed bg-zinc-50 dark:bg-zinc-800/30 p-2.5 rounded-lg border border-zinc-100 dark:border-zinc-800">
                                                            {{ $historia->tratamiento_indicaciones ?? $historia->treatment }}
                                                            @if(!($historia->tratamiento_indicaciones ?? $historia->treatment))
                                                                <span x-text="$store.i18n.t('misc.notSpecified') || 'No especificado'">No especificado</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    @if($historia->notas_aclaratorias)
                                                        <div class="md:col-span-2">
                                                            <h4 class="font-bold text-zinc-800 dark:text-zinc-200 mb-1 flex items-center gap-1.5">
                                                                <span class="material-symbols-outlined text-amber-500 text-[15px]">notes</span>
                                                                <span x-text="$store.i18n.t('report.additionalNotes') || 'Notas Adicionales'">Notas Adicionales</span>
                                                            </h4>
                                                            <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed bg-amber-50/50 dark:bg-amber-950/20 p-2.5 rounded-lg border border-amber-200/50 dark:border-amber-900/30">
                                                                {{ $historia->notas_aclaratorias }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="vc-panel py-12 text-center text-zinc-400 text-xs">
                        <span class="material-symbols-outlined text-3xl mb-1 text-zinc-300 dark:text-zinc-600">pets</span>
                        <p x-text="$store.i18n.t('records.noPets') || 'Este cliente no tiene mascotas registradas.'">Este cliente no tiene mascotas registradas.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Modal Confirmar Eliminar Historia --}}
    <flux:modal name="confirmar-eliminar" class="min-w-[22rem]">
        <div class="p-4">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-xs">
                    <span class="material-symbols-outlined text-[32px]">warning</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.deleteRecord') || 'Eliminar Historia Clínica'">Eliminar Historia Clínica</h2>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto" x-text="$store.i18n.t('modal.deleteRecordSub') || 'Esta acción no se puede revertir y perderás toda la información asociada a este registro.'">Esta acción no se puede revertir y perderás toda la información asociada a este registro.</p>
                </div>
            </div>
            <div class="flex gap-3 w-full mt-6">
                <flux:modal.close class="flex-1">
                    <flux:button variant="ghost" class="w-full"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'">Cancelar</span></flux:button>
                </flux:modal.close>
                <button type="button" wire:click="confirmarEliminar" class="btn-danger flex-1 flex justify-center items-center gap-2" x-on:click="$dispatch('modal-close', { name: 'confirmar-eliminar' })">
                    <span class="material-symbols-outlined icon-sm">delete</span>
                    <span x-text="$store.i18n.t('btn.delete') || 'Eliminar'">Eliminar</span>
                </button>
            </div>
        </div>
    </flux:modal>
</div>
