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
            {{-- Botón "Nueva Historia" eliminado por requerimiento UX, el flujo es desde Citas --}}
        </div>

        <x-vc-table-layout 
            :data="$clientes"
            icon="folder_shared"
            emptyTitle="Sin resultados"
            emptyText="No hay clientes o mascotas que coincidan con la búsqueda."
            searchModel="busqueda"
            searchPlaceholder="Buscar cliente, documento o mascota..."
        >
            <x-slot:filters>
                <flux:select wire:model.live="especie_id" class="w-full sm:w-48">
                    <option value="">Todas las Especies</option>
                    @foreach($especies as $especie)
                        <option value="{{ $especie->id }}">{{ $especie->name }}</option>
                    @endforeach
                </flux:select>
            </x-slot:filters>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                @foreach($clientes as $cliente)
                    <div wire:key="cliente-{{ $cliente->id }}" class="vc-card flex flex-col p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow relative cursor-pointer" @click="Flux.modal('ver-mascotas-{{ $cliente->id }}').show()">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider shadow-sm shrink-0">
                                {{ substr($cliente->first_name, 0, 1) }}{{ substr($cliente->last_name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 truncate" title="{{ $cliente->nombre_completo }}">{{ $cliente->nombre_completo }}</h3>
                                <p class="text-xs text-zinc-500 font-medium mt-0.5 truncate">Doc: {{ $cliente->numero_documento }}</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center mt-auto">
                            <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider" x-text="$store.i18n.t('page.pets') || 'Mascotas'">Mascotas</span>
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

                    <flux:modal name="ver-mascotas-{{ $cliente->id }}" class="w-[90vw] md:w-full max-w-2xl">
                        <div class="border-b border-zinc-100 dark:border-zinc-700/50 pb-4 mb-4">
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-500">folder_shared</span>
                                Historias Clínicas: {{ $cliente->nombre_completo }}
                            </h2>
                        </div>
                        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2" x-data="{ mascotaActiva: {} }">
                            @foreach($cliente->mascotas as $mascota)
                                <div wire:key="modal-mascota-{{ $mascota->id }}" class="mb-4 bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-700/80 rounded-xl shadow-sm overflow-hidden">
                                    <div 
                                        class="px-4 py-3 flex items-center justify-between cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                                        @click="mascotaActiva[{{ $mascota->id }}] = !mascotaActiva[{{ $mascota->id }}]"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                                <span class="material-symbols-outlined text-[20px]">pets</span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-zinc-800 dark:text-zinc-200">{{ $mascota->name }}</p>
                                                <p class="text-[11px] text-zinc-500 font-medium">{{ $mascota->especie?->name ?? 'Mascota' }} &bull; Historias: {{ $mascota->historiasClinicas->count() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <a href="{{ route('historias.pdf', $mascota->id) }}" target="_blank" class="text-xs bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 py-1.5 px-3 h-auto text-[11px] font-bold flex gap-1 items-center z-10 rounded-lg transition-colors border border-zinc-200 dark:border-zinc-700 shadow-sm" @click.stop>
                                                <span class="material-symbols-outlined text-[14px]">print</span> Descargar Historial
                                            </a>
                                            <span class="material-symbols-outlined text-zinc-400 transition-transform duration-300" :class="mascotaActiva[{{ $mascota->id }}] ? 'rotate-180' : ''">expand_more</span>
                                        </div>
                                    </div>

                                    {{-- Timeline de historias clínicas --}}
                                    <div x-show="mascotaActiva[{{ $mascota->id }}]" x-collapse class="border-t border-zinc-100 dark:border-zinc-800 p-5 bg-zinc-50/50 dark:bg-zinc-800/30">
                                        @if($mascota->historiasClinicas->isEmpty())
                                            <div class="flex flex-col items-center justify-center py-6 text-zinc-400">
                                                <span class="material-symbols-outlined text-4xl mb-2 opacity-50">history</span>
                                                <p class="text-sm font-medium">Sin registros clínicos</p>
                                            </div>
                                        @else
                                            <div class="relative border-l-2 border-emerald-200 dark:border-emerald-800 ml-4 pl-6 space-y-6">
                                                @foreach($mascota->historiasClinicas as $historia)
                                                    <div wire:key="modal-historia-{{ $historia->id }}" class="relative">
                                                        <div class="absolute -left-[33px] w-4 h-4 rounded-full bg-emerald-500 border-4 border-zinc-50 dark:border-zinc-900 mt-1"></div>
                                                        
                                                        <div class="bg-white dark:bg-vc-surface rounded-xl p-4 shadow-sm border border-zinc-200 dark:border-zinc-700 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors group">
                                                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
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
                                                                    <a href="{{ route('historias.ver', $historia->id) }}" class="vc-btn-action w-9 h-9 rounded-lg bg-blue-50 hover:bg-blue-100 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 transition-colors flex items-center justify-center border border-transparent hover:border-blue-200 dark:hover:border-blue-500/30">
                                                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                                    </a>
                                                                    <a href="{{ route('historias.editar', $historia->id) }}" class="vc-btn-action w-9 h-9 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 transition-colors flex items-center justify-center border border-transparent hover:border-zinc-300 dark:hover:border-zinc-600">
                                                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                                                    </a>
                                                                    <button type="button" @click="$wire.historiaEliminarId = {{ $historia->id }}; Flux.modal('confirmar-eliminar').show()" class="vc-btn-action w-9 h-9 rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 text-red-500 transition-colors flex items-center justify-center border border-transparent hover:border-red-200 dark:hover:border-red-500/30">
                                                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                                                    </button>
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
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-700/50 flex justify-end">
                            <flux:modal.close>
                                <flux:button variant="ghost"><span x-text="$store.i18n.t('btn.close') || 'Cerrar'">Cerrar</span></flux:button>
                            </flux:modal.close>
                        </div>
                    </flux:modal>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $clientes->links() }}
            </div>
        </x-vc-table-layout>
    </div>

    {{-- Modal para Eliminar --}}
    <flux:modal :closable="false" name="confirmar-eliminar" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg"><span x-text="$store.i18n.t('modal.deleteRecord') || 'Eliminar Historia Clínica'"></span></flux:heading>
                <flux:text class="mt-2"><span x-text="$store.i18n.t('modal.deleteRecordMsg') || '¿Está seguro que desea eliminar este registro? Esta acción no se puede deshacer.'"></span></flux:text>
            </div>
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></flux:button>
                </flux:modal.close>
                <flux:button
                    variant="danger"
                    wire:click="eliminar($wire.historiaEliminarId)"
                    x-on:click="Flux.modal('confirmar-eliminar').close()"
                >
                    <span x-text="$store.i18n.t('btn.delete') || 'Eliminar'"></span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>