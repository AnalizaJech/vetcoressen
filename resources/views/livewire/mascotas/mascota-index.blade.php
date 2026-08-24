<div>
    <x-slot:title>Pets</x-slot:title>

<div class="animate-slide-up">
    {{-- ═══ Header de Mascotas (Estándar Premium) ═══ --}}
    <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200/50 dark:border-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <span class="material-symbols-outlined text-2xl">pets</span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                    <span x-text="$store.i18n.t('page.pets') || 'Mascotas'">Mascotas</span>
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('page.petsSub') || 'Directorio y fichas de pacientes de la clínica'">
                    Directorio y fichas de pacientes de la clínica
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('mascotas.crear') }}" wire:navigate class="btn-primary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newPet') || 'Nueva Mascota'">Nueva Mascota</span>
            </a>
        </div>
    </div>

    {{-- ═══ Barra de Filtros Dinámicos (Estilo Reportes con Labels) ═══ --}}
    <div class="vc-panel mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
            {{-- Filtro de Propietario --}}
            <div>
                <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('table.owner') || 'Propietario'">
                    Propietario
                </label>
                <x-vc-dropdown
                    wire:model.live="filtroCliente"
                    :options="$clientesOptions"
                    placeholder="filter.allClients"
                    :selected="$filtroCliente"
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
                    placeholder="filter.allPets"
                    :selected="$filtroMascota"
                    icon="pets"
                    searchable
                />
            </div>
        </div>
    </div>

    <x-vc-table-layout 
        :data="$mascotas"
        :searchable="false"
        icon="pets"
        emptyTitle="Sin mascotas"
        emptyText="No hay mascotas que coincidan con los filtros."
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            @foreach($mascotas as $mascota)
                <div wire:key="mascota-{{ $mascota->id }}" class="vc-card flex flex-col justify-between p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow relative">
                    {{-- Avatar y Nombre --}}
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl">pets</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $mascota->name }}</h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="material-symbols-outlined text-[14px] text-amber-500">category</span>
                                <span class="text-xs font-medium text-amber-600 dark:text-amber-500">{{ $mascota->especie?->name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Info Principal --}}
                    <div class="space-y-3 mb-6 flex-1">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.breed')"></p>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-300 truncate">{{ $mascota->raza?->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.sex')"></p>
                                @if(in_array($mascota->gender, ['M', 'Macho']))
                                    <div class="flex items-center gap-1 text-blue-600 mt-0.5">
                                        <span class="material-symbols-outlined icon-sm">male</span>
                                        <span class="text-sm font-medium" x-text="$store.i18n.t('form.male') || 'Male'">Male</span>
                                    </div>
                                @elseif(in_array($mascota->gender, ['H', 'Hembra']))
                                    <div class="flex items-center gap-1 text-pink-500 mt-0.5">
                                        <span class="material-symbols-outlined icon-sm">female</span>
                                        <span class="text-sm font-medium" x-text="$store.i18n.t('form.female') || 'Female'">Female</span>
                                    </div>
                                @else
                                    <span class="text-sm text-zinc-500">-</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.owner')"></p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="material-symbols-outlined text-blue-500 icon-sm">person</span>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $mascota->cliente?->nombre_completo ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-1.5 items-center">
                        <a href="{{ route('historias.index', ['mascota_id' => $mascota->id]) }}" wire:navigate class="vc-btn-action vc-btn-history" x-bind:title="$store.i18n.t('records.title') || 'Historias Clínicas'">
                            <span class="material-symbols-outlined text-lg">clinical_notes</span>
                        </a>
                        <button type="button" class="vc-btn-action vc-btn-view" x-bind:title="$store.i18n.t('btn.view') || 'Ver'"
                            wire:click="ver({{ $mascota->id }})">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                        <a href="{{ route('mascotas.editar', $mascota) }}" wire:navigate class="vc-btn-action vc-btn-edit" x-bind:title="$store.i18n.t('btn.edit') || 'Editar'">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <button type="button" class="vc-btn-action vc-btn-delete" x-bind:title="$store.i18n.t('btn.delete') || 'Eliminar'"
                            @click="$wire.set('mascotaEliminarId', {{ $mascota->id }}); $dispatch('modal-show', { name: 'confirmar-eliminar' })">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $mascotas->links() }}
        </div>
    </x-vc-table-layout>
</div>

    {{-- Modal eliminar --}}
    <flux:modal :closable="false" name="confirmar-eliminar" class="min-w-88 overflow-y-auto max-h-[85vh]">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">warning</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.deletePet') || 'Eliminar Mascota'">Eliminar Mascota</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.deletePetMsg') || 'Esta acción no se puede revertir y perderás toda la información asociada a este registro.'">Esta acción no se puede revertir y perderás toda la información asociada a este registro.</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <button type="button" class="btn-secondary w-full sm:w-auto text-xs px-4 py-2 flex items-center justify-center gap-1.5">
                        <span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'">Cancelar</span>
                    </button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto btn-danger text-xs px-4 py-2 flex items-center justify-center gap-1.5" wire:click="eliminar" x-on:click="$dispatch('modal-close', { name: 'confirmar-eliminar' })">
                    <span x-text="$store.i18n.t('btn.delete') || 'Eliminar'">Eliminar</span>
                </button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Ver Mascota --}}
    <flux:modal :closable="false" name="ver-mascota" class="w-[90vw] md:w-full max-w-2xl overflow-y-auto max-h-[85vh]">
        @if($mascotaVer)
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500 shrink-0">
                    <span class="material-symbols-outlined text-3xl">pets</span>
                </div>
                <div>
                    <flux:heading size="xl" class="font-bold">{{ $mascotaVer->name }}</flux:heading>
                    <flux:subheading class="flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-sm">category</span>
                        {{ $mascotaVer->especie?->name }} • {{ $mascotaVer->raza?->name }}
                    </flux:subheading>
                </div>
            </div>
            
            <div class="bg-emerald-50/50 dark:bg-emerald-900/10 rounded-2xl p-6 border border-emerald-100/80 dark:border-emerald-800/30">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-800/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">person</span>
                    </div>
                    <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100" x-text="$store.i18n.t('modal.ownerInfo') || 'Información del Propietario'">Información del Propietario</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 ml-13">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400">
                            <span class="material-symbols-outlined text-[16px]">badge</span>
                            <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.name') || 'Nombre'">Nombre</p>
                        </div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $mascotaVer->cliente?->nombre_completo }}</p>
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400">
                            <span class="material-symbols-outlined text-[16px]">id_card</span>
                            <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.identification') || 'Identificación'">Identificación</p>
                        </div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $mascotaVer->cliente?->tipo_documento }}: {{ $mascotaVer->cliente?->numero_documento }}</p>
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400">
                            <span class="material-symbols-outlined text-[16px]">call</span>
                            <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.contact') || 'Contacto'">Contacto</p>
                        </div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $mascotaVer->cliente?->phone ?? '-' }}</p>
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400">
                            <span class="material-symbols-outlined text-[16px]">location_on</span>
                            <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.location') || 'Ubicación'">Ubicación</p>
                        </div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate" title="{{ $mascotaVer->cliente?->address }}">
                            {{ $mascotaVer->cliente?->address ? $mascotaVer->cliente?->address : ($mascotaVer->cliente?->distrito ?? '-') }}
                        </p>
                    </div>
                </div>
                @if($mascotaVer->cliente?->notes)
                <div class="mt-5 ml-13 pt-4 border-t border-emerald-100 dark:border-emerald-800/30">
                    <div class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400 mb-2">
                        <span class="material-symbols-outlined text-[16px]">note</span>
                        <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.ownerNotes') || 'Notas del Propietario'">Notas del Propietario</p>
                    </div>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">{{ $mascotaVer->cliente->notes }}</p>
                </div>
                @endif
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                <div>
                    <div class="flex items-center gap-2 text-zinc-500 mb-1">
                        <span class="material-symbols-outlined text-sm">transgender</span>
                        <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.sex') || 'Sexo'">Sexo</p>
                    </div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 pl-6" x-text="{{ in_array($mascotaVer->gender, ['M', 'Macho']) ? 'true' : 'false' }} ? ($store.i18n.t('form.male') || 'Male') : ($store.i18n.t('form.female') || 'Female')"></p>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-zinc-500 mb-1">
                        <span class="material-symbols-outlined text-sm">palette</span>
                        <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.color') || 'Color'">Color</p>
                    </div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 pl-6">{{ $mascotaVer->color ?? '-' }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-zinc-500 mb-1">
                        <span class="material-symbols-outlined text-sm">cake</span>
                        <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.birthDate') || 'Date of Birth'">Date of Birth</p>
                    </div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 pl-6">{{ $mascotaVer->birth_date ? $mascotaVer->birth_date->format('M d, Y') : '-' }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-zinc-500 mb-1">
                        <span class="material-symbols-outlined text-sm">weight</span>
                        <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.weight') || 'Peso'">Peso</p>
                    </div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 pl-6">{{ $mascotaVer->current_weight ? $mascotaVer->current_weight . ' kg' : '-' }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-zinc-500 mb-1">
                        <span class="material-symbols-outlined text-sm">content_cut</span>
                        <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.sterilized') || 'Sterilized'">Sterilized</p>
                    </div>
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 pl-6" x-text="{{ $mascotaVer->esterilizado ? 'true' : 'false' }} ? ($store.i18n.t('form.yes') || 'Yes') : ($store.i18n.t('form.no') || 'No')"></p>
                </div>
            </div>
            
            @if($mascotaVer->medical_notes)
            <div>
                <div class="flex items-center gap-2 text-zinc-500 mb-2">
                    <span class="material-symbols-outlined text-sm">medical_information</span>
                    <p class="text-xs uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.medicalNotes') || 'Notas Médicas'">Notas Médicas</p>
                </div>
                <div class="p-3 bg-zinc-50 dark:bg-vc-surface-alt/50 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 ml-6">
                    {{ $mascotaVer->medical_notes }}
                </div>
            </div>
            @endif

            <div class="flex justify-end mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 w-full">
                <flux:modal.close class="w-full sm:w-auto">
                    <button type="button" class="btn-secondary text-xs px-4 py-2 flex items-center justify-center gap-1.5 w-full sm:w-auto">
                        <span x-text="$store.i18n.t('btn.close') || 'Cerrar'">Cerrar</span>
                    </button>
                </flux:modal.close>
            </div>
        </div>
        @endif
    </flux:modal>
</div>


