<div x-data>
    <x-slot:title x-text="$store.i18n.t('page.records') || 'Historias Clínicas'">Historias Clínicas</x-slot:title>

    <div class="animate-slide-up">
        {{-- Cabecera con icono --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="kpi-icon kpi-icon--emerald">
                    <span class="material-symbols-outlined">folder_shared</span>
                </div>
                <div>
                    <flux:heading size="xl"><span x-text="$store.i18n.t('page.records') || 'Directorio Clínico'"></span></flux:heading>
                    <flux:subheading><span x-text="$store.i18n.t('page.recordsSub') || 'Gestiona las historias clínicas por cliente y mascota'"></span></flux:subheading>
                </div>
            </div>
            
            @if($clienteSeleccionado)
                <flux:button variant="ghost" wire:click="volver" icon="arrow-left" class="text-zinc-500">
                    <span x-text="$store.i18n.t('btn.back') || 'Volver'">Volver</span>
                </flux:button>
            @endif
        </div>

        @if(!$clienteSeleccionado)
            {{-- Filtros y Buscador --}}
            <div class="vc-card p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-1">
                        <x-vc-dropdown
                            wire:model.live="filtroCliente"
                            :options="$clientesOptions"
                            placeholder="Todos los clientes"
                            searchable
                            class="w-full"
                        />
                    </div>
                    <div class="md:col-span-1">
                        <x-vc-dropdown
                            wire:model.live="filtroMascota"
                            :options="$mascotasOptions"
                            placeholder="Todas las mascotas"
                            searchable
                            class="w-full"
                        />
                    </div>
                    <div class="md:col-span-1">
                        @php
                            $especieOptions = [['value' => '', 'label' => 'filter.allSpecies']];
                            foreach ($especies as $especie) {
                                $especieOptions[] = ['value' => (string)$especie->id, 'label' => $especie->name];
                            }
                        @endphp
                        <div x-data="{ ph: $store.i18n.t('filter.allSpecies') || 'Todas las Especies' }">
                            <x-vc-dropdown
                                wire:model.live="especie_id"
                                :options="$especieOptions"
                                :selected="$especie_id"
                                x-bind:placeholder="ph"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid de Clientes (3 por línea como solicitado) --}}
            @if($clientes->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 px-4 text-center bg-white dark:bg-vc-surface rounded-2xl border border-zinc-200 dark:border-zinc-800">
                    <div class="w-16 h-16 bg-zinc-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-3xl text-zinc-400">folder_off</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-1" x-text="$store.i18n.t('table.noResults') || 'Sin resultados'">Sin resultados</h3>
                    <p class="text-zinc-500 max-w-sm" x-text="$store.i18n.t('table.noClientsFound') || 'No hay clientes o mascotas que coincidan con la búsqueda.'">No hay clientes o mascotas que coincidan con la búsqueda.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($clientes as $cliente)
                        <div wire:key="cliente-{{ $cliente->id }}" class="vc-card flex flex-col p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all relative cursor-pointer h-full group" wire:click="seleccionarCliente({{ $cliente->id }})">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-full bg-blue-50 group-hover:bg-blue-100 dark:bg-blue-500/10 dark:group-hover:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider shadow-sm shrink-0 transition-colors">
                                    {{ substr($cliente->first_name, 0, 1) }}{{ substr($cliente->last_name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 truncate" title="{{ $cliente->nombre_completo }}">{{ $cliente->nombre_completo }}</h3>
                                    <p class="text-xs text-zinc-500 font-medium mt-0.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">badge</span> {{ $cliente->numero_documento }}
                                    </p>
                                </div>
                                <div class="shrink-0 text-zinc-300 group-hover:text-blue-500 transition-colors">
                                    <span class="material-symbols-outlined">chevron_right</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center mt-auto">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[11px] font-semibold text-zinc-500 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">call</span> {{ $cliente->phone ?? 'S/N' }}</span>
                                    <span class="text-[11px] font-semibold text-zinc-500 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">event</span> Última cita: {{ $cliente->citas()->latest('fecha_hora')->first()?->fecha_hora?->format('d/m/Y') ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center">
                                    @foreach($cliente->mascotas->take(3) as $m)
                                        <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/10 border-2 border-white dark:border-vc-surface flex items-center justify-center text-emerald-600 dark:text-emerald-400 -ml-2 relative z-[{{ 3 - $loop->index }}]" title="{{ $m->name }}">
                                            <span class="material-symbols-outlined text-[14px]">pets</span>
                                        </div>
                                    @endforeach
                                    @if($cliente->mascotas->count() > 3)
                                        <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 border-2 border-white dark:border-vc-surface flex items-center justify-center text-xs font-bold text-zinc-500 -ml-2 relative z-0">
                                            +{{ $cliente->mascotas->count() - 3 }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    {{ $clientes->links(data: ['scrollTo' => false]) }}
                </div>
            @endif

        @else
            {{-- Detalle del Cliente Seleccionado (Drill-down) --}}
            <div class="vc-card p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xl uppercase tracking-wider shadow-sm shrink-0">
                            {{ substr($clienteSeleccionado->first_name, 0, 1) }}{{ substr($clienteSeleccionado->last_name, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $clienteSeleccionado->nombre_completo }}</h2>
                            <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-zinc-500">
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">badge</span> {{ $clienteSeleccionado->numero_documento }}</span>
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">call</span> {{ $clienteSeleccionado->phone ?? 'S/N' }}</span>
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">mail</span> {{ $clienteSeleccionado->email ?? 'S/N' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500">pets</span>
                Mascotas de {{ $clienteSeleccionado->first_name }}
            </h3>

            <div class="space-y-4">
                @foreach($clienteSeleccionado->mascotas as $mascota)
                    <div wire:key="acordeon-mascota-{{ $mascota->id }}" x-data="{ mascotaActiva: false }" class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-700/80 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 flex items-center justify-between cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" @click="mascotaActiva = !mascotaActiva">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <span class="material-symbols-outlined text-[24px]">pets</span>
                                </div>
                                <div>
                                    <p class="font-bold text-base text-zinc-800 dark:text-zinc-200">{{ $mascota->name }}</p>
                                    <p class="text-xs text-zinc-500 font-medium mt-1 flex gap-2">
                                        <span>{{ $mascota->especie?->name ?? 'Mascota' }}</span> 
                                        <span>&bull;</span> 
                                        <span>Historias: {{ $mascota->historiasClinicas->count() }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('mascotas.historial.pdf', $mascota->id) }}" target="_blank" class="btn-primary py-2 px-4 flex gap-2 items-center text-sm rounded-xl transition-all shadow-sm" @click.stop>
                                    <span class="material-symbols-outlined text-[18px]">print</span>
                                    <span x-text="$store.i18n.t('action.print', 'Imprimir')"></span>
                                </a>
                                <span class="material-symbols-outlined text-zinc-400 transition-transform duration-300" :class="mascotaActiva ? 'rotate-180' : ''">expand_more</span>
                            </div>
                        </div>

                        <div x-show="mascotaActiva" x-collapse class="border-t border-zinc-100 dark:border-zinc-800 p-5 bg-zinc-50/50 dark:bg-zinc-800/30">
                            @if($mascota->historiasClinicas->isEmpty())
                                <div class="flex flex-col items-center justify-center py-8 text-zinc-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 opacity-50">history</span>
                                    <p class="text-sm font-medium">Sin registros clínicos</p>
                                </div>
                            @else
                                <div class="relative border-l-2 border-emerald-200 dark:border-emerald-800 ml-5 pl-6 space-y-6 my-2">
                                    @foreach($mascota->historiasClinicas as $historia)
                                        <div wire:key="acordeon-historia-{{ $historia->id }}" class="relative" x-data="{ detallesAbiertos: false }">
                                            <div class="absolute -left-[34px] w-4 h-4 rounded-full bg-emerald-500 border-4 border-zinc-50 dark:border-zinc-900 mt-1"></div>
                                            <div class="bg-white dark:bg-vc-surface rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors group overflow-hidden">
                                                <div class="p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 cursor-pointer" @click="detallesAbiertos = !detallesAbiertos">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                                            <span class="text-sm font-extrabold text-zinc-900 dark:text-zinc-100">{{ $historia->date?->format('d/m/Y') }}</span>
                                                            <span class="text-xs font-medium text-zinc-500">{{ $historia->date?->format('h:i A') }}</span>
                                                            <span class="ml-2 text-[10px] bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 px-2 py-0.5 rounded-md font-bold uppercase tracking-wider flex items-center gap-1 border border-blue-100 dark:border-blue-500/20">
                                                                <span class="material-symbols-outlined text-[12px]">medical_services</span>
                                                                {{ $historia->veterinario?->name ?? 'No asignado' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 mb-1 line-clamp-1">Motivo: {{ $historia->reason ?? '-' }}</p>
                                                        <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2">Diagnóstico: {{ $historia->diagnostico_presuntivo ?? 'Sin especificar' }}</p>
                                                    </div>
                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <button type="button" class="vc-btn-action w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center transition-transform duration-300" :class="detallesAbiertos ? 'rotate-180' : ''">
                                                            <span class="material-symbols-outlined text-[18px]">expand_more</span>
                                                        </button>
                                                        <a href="{{ route('historias.ver', $historia->id) }}" class="vc-btn-action w-9 h-9 rounded-lg bg-blue-50 hover:bg-blue-100 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 transition-colors flex items-center justify-center border border-transparent hover:border-blue-200 dark:hover:border-blue-500/30" @click.stop>
                                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                        </a>
                                                        <a href="{{ route('historias.editar', $historia->id) }}" class="vc-btn-action w-9 h-9 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 transition-colors flex items-center justify-center border border-transparent hover:border-zinc-300 dark:hover:border-zinc-600" @click.stop>
                                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                                        </a>
                                                        <button type="button" @click.stop="$wire.historiaEliminarId = {{ $historia->id }}; Flux.modal('confirmar-eliminar').show()" class="vc-btn-action w-9 h-9 rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 text-red-500 transition-colors flex items-center justify-center border border-transparent hover:border-red-200 dark:hover:border-red-500/30">
                                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div x-show="detallesAbiertos" x-collapse class="border-t border-zinc-100 dark:border-zinc-800 p-4 bg-zinc-50/30 dark:bg-zinc-900/30 text-sm">
                                                    <div class="grid grid-cols-1 gap-4">
                                                        @if($historia->anamnesis)
                                                        <div>
                                                            <h4 class="font-bold text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-700 pb-1 mb-2" x-text="$store.i18n.t('report.anamnesisSigns') || 'Anamnesis'"></h4>
                                                            <p class="text-zinc-600 dark:text-zinc-400">{{ $historia->anamnesis }}</p>
                                                        </div>
                                                        @endif
                                                        
                                                        <div>
                                                            <h4 class="font-bold text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-700 pb-1 mb-2" x-text="$store.i18n.t('report.vitalSigns') || 'Signos Vitales'"></h4>
                                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300"><span x-text="$store.i18n.t('form.weight') || 'Peso'"></span>:</span> {{ $historia->weight ?? '-' }} kg</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300"><span x-text="$store.i18n.t('form.temperature') || 'Temp'"></span>:</span> {{ $historia->temperature ?? '-' }} °C</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300"><span x-text="$store.i18n.t('form.heartRate') || 'FC'"></span>:</span> {{ $historia->heart_rate ?? '-' }} bpm</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300"><span x-text="$store.i18n.t('form.respRate') || 'FR'"></span>:</span> {{ $historia->respiratory_rate ?? '-' }} rpm</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300"><span x-text="$store.i18n.t('form.bodyCondition') || 'Cond. Corp'"></span>:</span> {{ $historia->condicion_corporal ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300"><span x-text="$store.i18n.t('form.hydration') || 'Hidratación'"></span>:</span> {{ $historia->nivel_hidratacion ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300"><span x-text="$store.i18n.t('form.painLevel') || 'Dolor'"></span>:</span> {{ $historia->nivel_dolor ?? '-' }}</div>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <h4 class="font-bold text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-700 pb-1 mb-2" x-text="$store.i18n.t('form.physicalExam') || 'Examen Físico'"></h4>
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('form.cardiovascular') || 'Cardiovascular'"></span>: {{ $historia->examen_cardiovascular ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('form.digestive') || 'Digestivo'"></span>: {{ $historia->examen_digestivo ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('form.lymphNodes') || 'Linfonodos'"></span>: {{ $historia->examen_linfonodos ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('form.mucous') || 'Mucosas'"></span>: {{ $historia->examen_mucosas ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('form.musculoskeletal') || 'Músculoesquelético'"></span>: {{ $historia->examen_musculoesqueletico ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('form.neurological') || 'Neurológico'"></span>: {{ $historia->examen_neurologico ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('form.eyesEars') || 'Ojos/Oídos'"></span>: {{ $historia->examen_ojos_oidos ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('form.skinCoat') || 'Piel/Pelaje'"></span>: {{ $historia->examen_piel_pelaje ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('form.respiratory') || 'Respiratorio'"></span>: {{ $historia->examen_respiratorio ?? '-' }}</div>
                                                                <div><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('form.urinary') || 'Urinario'"></span>: {{ $historia->examen_urinario ?? '-' }}</div>
                                                            </div>
                                                        </div>

                                                        @if($historia->tratamiento_indicaciones || $historia->notas_aclaratorias || $historia->proxima_cita_recomendada)
                                                        <div>
                                                            <h4 class="font-bold text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-700 pb-1 mb-2" x-text="$store.i18n.t('report.treatmentIndications') || 'Tratamiento y Notas'"></h4>
                                                            @if($historia->tratamiento_indicaciones)
                                                                <p class="text-zinc-600 dark:text-zinc-400 mb-1"><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('report.instructions') || 'Indicaciones:'"></span> {{ $historia->tratamiento_indicaciones }}</p>
                                                            @endif
                                                            @if($historia->notas_aclaratorias)
                                                                <p class="text-zinc-600 dark:text-zinc-400 mb-1"><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('report.additionalNotes') || 'Notas:'"></span> {{ $historia->notas_aclaratorias }}</p>
                                                            @endif
                                                            @if($historia->proxima_cita_recomendada)
                                                                <p class="text-zinc-600 dark:text-zinc-400"><span class="font-semibold text-zinc-700 dark:text-zinc-300" x-text="$store.i18n.t('report.nextAppt') || 'Próx. Cita:'"></span> {{ $historia->proxima_cita_recomendada->format('d/m/Y') }}</p>
                                                            @endif
                                                        </div>
                                                        @endif
                                                        
                                                        @if($historia->prescripciones->isNotEmpty())
                                                        <div>
                                                            <h4 class="font-bold text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-700 pb-1 mb-2">
                                                                <span class="material-symbols-outlined text-[16px] inline-block align-text-bottom mr-1">medication</span>
                                                                <span x-text="$store.i18n.t('form.prescriptions') || 'Prescripciones'"></span>
                                                            </h4>
                                                            <div class="space-y-2">
                                                                @foreach($historia->prescripciones as $presc)
                                                                    <div class="bg-white dark:bg-zinc-800 p-2 rounded-lg border border-zinc-200 dark:border-zinc-700 text-xs">
                                                                        <div class="font-bold text-zinc-800 dark:text-zinc-200">{{ $presc->producto->name ?? $presc->medicamento ?? 'Producto no encontrado' }}</div>
                                                                        <div class="grid grid-cols-2 gap-2 mt-1 text-zinc-600 dark:text-zinc-400">
                                                                            <div><span class="font-semibold"><span x-text="$store.i18n.t('form.dose') || 'Dosis:'"></span></span> {{ $presc->dosage ?? '-' }}</div>
                                                                            <div><span class="font-semibold"><span x-text="$store.i18n.t('form.frequency') || 'Frecuencia:'"></span></span> {{ $presc->frequency ?? '-' }}</div>
                                                                            <div class="col-span-2"><span class="font-semibold"><span x-text="$store.i18n.t('form.duration') || 'Duración:'"></span></span> {{ $presc->duration ?? ($presc->duracion_dias ? $presc->duracion_dias . ' días' : '-') }}</div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Modal Confirmar Eliminar Historia --}}
    <flux:modal :closable="false" name="confirmar-eliminar" class="min-w-88 overflow-y-auto max-h-[85vh]">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">warning</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.deleteRecord') || 'Eliminar Historia Clínica'">Eliminar Historia Clínica</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.deleteRecordSub') || 'Esta acción no se puede revertir y perderás toda la información asociada a este registro.'">Esta acción no se puede revertir y perderás toda la información asociada a este registro.</p>
                </div>
            </div>
            
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.cancel') === 'btn.cancel' ? 'Cancelar' : $store.i18n.t('btn.cancel')">Cancelar</span></flux:button>
                </flux:modal.close>
                <button type="button" wire:click="confirmarEliminar" x-on:click="$dispatch('modal-close', { name: 'confirmar-eliminar' })" class="w-full sm:w-auto btn-danger font-medium justify-center">
                    <span x-text="$store.i18n.t('btn.delete') === 'btn.delete' ? 'Eliminar' : $store.i18n.t('btn.delete')">Eliminar</span>
                </button>
            </div>
        </div>
    </flux:modal>
</div>
