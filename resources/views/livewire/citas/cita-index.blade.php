<div x-data="{}">
    <x-slot:title x-text="$store.i18n.t('page.appointments')">Appointments</x-slot:title>

<div class="animate-slide-up">
    {{-- Cabecera con icono --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="kpi-icon kpi-icon--blue">
                <span class="material-symbols-outlined">calendar_month</span>
            </div>
            <div>
                <flux:heading size="xl" class="font-extrabold text-zinc-900 dark:text-zinc-100"><span x-text="$store.i18n.t('page.appointments')"></span></flux:heading>
                <flux:subheading><span x-text="$store.i18n.t('page.appointmentsSub')"></span></flux:subheading>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto mt-2 sm:mt-0">
            {{-- Toggle Vista Lista / Calendario --}}
            <div class="flex items-center bg-zinc-100 dark:bg-vc-surface-alt rounded-xl p-1 border border-zinc-200 dark:border-zinc-700">
                <button 
                    wire:click="$set('vistaActiva', 'calendario')" 
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 flex items-center gap-1.5 {{ $vistaActiva === 'calendario' ? 'bg-vc-primary text-white shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                >
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                    Calendario
                </button>
                <button 
                    wire:click="$set('vistaActiva', 'lista')" 
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 flex items-center gap-1.5 {{ $vistaActiva === 'lista' ? 'bg-vc-primary text-white shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                >
                    <span class="material-symbols-outlined text-[16px]">view_list</span>
                    Lista
                </button>
            </div>

            <flux:modal.trigger name="reasignar-modal">
                <flux:button variant="subtle" icon="arrows-right-left">
                    Reasignación Masiva
                </flux:button>
            </flux:modal.trigger>
            <flux:modal.trigger name="emergencia-modal">
                <button type="button" class="w-full sm:w-auto px-4 py-2 bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white rounded-xl shadow-sm hover:shadow text-sm font-semibold flex items-center justify-center gap-2 transition-all border border-red-600 dark:border-red-500">
                    <span class="material-symbols-outlined icon-sm">emergency</span>
                    <span>Cita Emergencia</span>
                </button>
            </flux:modal.trigger>
            <a href="{{ route('citas.crear') }}" class="w-full sm:w-auto btn-primary justify-center">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newAppointment') || 'Nueva Cita'"></span>
            </a>
        </div>
    </div>

    {{-- Filtros compartidos para ambas vistas --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 w-full mb-6">
        @php
            $estadoOptions = [
                ['value' => '', 'label' => 'Estado (Todos)'],
                ['value' => 'PENDIENTE', 'label' => 'Pendiente'],
                ['value' => 'CONFIRMADA', 'label' => 'Confirmada'],
                ['value' => 'EN_PROGRESO', 'label' => 'En Progreso'],
                ['value' => 'COMPLETADA', 'label' => 'Completada'],
                ['value' => 'CANCELADA', 'label' => 'Cancelada'],
                ['value' => 'EMERGENCIA', 'label' => 'Emergencia'],
            ];
        @endphp
        <div x-data="{ ph: $store.i18n.t('table.status') || 'Estado' }" class="col-span-1">
            <x-vc-dropdown
                wire:model.live="filtroEstado"
                :options="$estadoOptions"
                :selected="$filtroEstado"
                x-bind:placeholder="ph"
                class="w-full"
            />
        </div>

        @php
            $vetOptions = [['value' => '', 'label' => 'Veterinario (Todos)']];
            foreach ($veterinarios as $vet) {
                $vetOptions[] = ['value' => (string)$vet->id, 'label' => $vet->name];
            }
        @endphp
        <div x-data="{ ph: $store.i18n.t('table.veterinarian') || 'Veterinario' }" class="col-span-1">
            <x-vc-dropdown
                wire:model.live="filtroVeterinario"
                :options="$vetOptions"
                :selected="$filtroVeterinario"
                x-bind:placeholder="ph"
                searchable
                class="w-full"
            />
        </div>

        @if($vistaActiva === 'lista')
            <div class="col-span-1">
                <x-vc-date-picker 
                    wire:model.live="filtroFecha" 
                    placeholder="Fecha"
                    class="w-full"
                />
            </div>

            <div class="col-span-1">
                <x-vc-time-picker 
                    wire:model.live="filtroHora" 
                    placeholder="Hora"
                    class="w-full"
                />
            </div>

            @php
                $clienteOptions = [['value' => '', 'label' => 'Cliente (Todos)']];
                foreach ($clientes as $cliente) {
                    $clienteOptions[] = ['value' => (string)$cliente->id, 'label' => $cliente->first_name . ' ' . $cliente->last_name];
                }
            @endphp
            <div x-data="{ ph: $store.i18n.t('table.client') || 'Cliente' }" class="col-span-1 md:col-span-2 lg:col-span-1">
                <x-vc-dropdown
                    wire:model.live="filtroCliente"
                    :options="$clienteOptions"
                    :selected="$filtroCliente"
                    x-bind:placeholder="ph"
                    searchable
                    class="w-full"
                />
            </div>

            @php
                $mascotaOptions = [['value' => '', 'label' => 'Mascota (Todas)']];
                foreach ($mascotas as $mascota) {
                    $mascotaOptions[] = ['value' => (string)$mascota->id, 'label' => $mascota->name];
                }
            @endphp
            <div x-data="{ ph: $store.i18n.t('table.pet') || 'Mascota' }" class="col-span-1">
                <x-vc-dropdown
                    wire:model.live="filtroMascota"
                    :options="$mascotaOptions"
                    :selected="$filtroMascota"
                    x-bind:placeholder="ph"
                    searchable
                    class="w-full"
                />
            </div>
        @endif

        @if($filtroEstado || $filtroFecha || $filtroHora || $filtroVeterinario || $filtroCliente || $filtroMascota)
            <div class="col-span-full">
                <flux:button variant="ghost" wire:click="limpiarFiltros" icon="x-mark" class="text-zinc-500 w-full sm:w-auto">Limpiar Filtros</flux:button>
            </div>
        @endif
    </div>

    {{-- ════ VISTA CALENDARIO ════ --}}
    <div x-show="$wire.vistaActiva === 'calendario'" class="animate-fade-in" x-cloak>
        <style>
            /* Ocultar texto de horas en la primera columna según petición del usuario */
            .fc-theme-standard .fc-timegrid-axis-cushion {
                display: none !important;
            }
            .fc-theme-standard .fc-timegrid-axis {
                width: 20px !important;
            }
            .fc-timegrid-slot-label-cushion {
                display: none !important;
            }
            /* Permitir que el contenido del evento se ajuste sin romper el layout absoluto */
            .fc-v-event .fc-event-main-frame {
                min-height: 100%;
            }
            .fc-timegrid-event .fc-event-main {
                padding: 4px;
                overflow-y: auto;
            }
        </style>
        <div 
            wire:ignore 
            x-data="vcCalendar()" 
            x-init="initCalendar($refs.calendarEl)"
            @calendar-refresh.window="refreshCalendar()"
            @vista-cambiada.window="if ($event.detail === 'calendario') setTimeout(() => updateSize(), 150)"
            class="vc-panel p-4 md:p-6"
        >
            <div x-ref="calendarEl" class="min-h-[600px]"></div>
        </div>
    </div>

    {{-- ═══ VISTA LISTA (CARDS) ═══ --}}
    @if($vistaActiva === 'lista')
        <x-vc-table-layout 
            :data="$citas"
            icon="calendar_month"
            emptyTitle="Sin citas"
            emptyText="No hay citas que coincidan con los filtros."
            :searchable="true"
            searchModel="busqueda"
            x-bind:searchPlaceholder="$store.i18n.t('btn.search') || 'Buscar...'"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                @foreach($citas as $cita)
                    <div class="vc-card flex flex-col justify-between p-5 rounded-2xl bg-white dark:bg-vc-surface-alt/80 border border-zinc-200 dark:border-zinc-700/50 shadow-sm hover:shadow-md transition-shadow relative">
                        {{-- Badge de estado --}}
                        @php
                            $estadoConfig = match($cita->status) {
                                'PENDIENTE' => ['color' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-800', 'icon' => 'schedule'],
                                'CONFIRMADA' => ['color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-800', 'icon' => 'check_circle'],
                                'EN_PROGRESO' => ['color' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 border-purple-200 dark:border-purple-800', 'icon' => 'play_circle'],
                                'COMPLETADA' => ['color' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800', 'icon' => 'done_all'],
                                'CANCELADA' => ['color' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-800', 'icon' => 'cancel'],
                                'EMERGENCIA' => ['color' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 border-red-200 dark:border-red-800 font-bold shadow-sm', 'icon' => 'emergency'],
                                default => ['color' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-300 border-zinc-200 dark:border-zinc-800', 'icon' => 'info'],
                            };
                        @endphp
                        <div class="absolute top-4 right-4">
                            <span class="{{ $estadoConfig['color'] }} border px-2 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wider flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-[14px]">{{ $estadoConfig['icon'] }}</span>
                                <span x-text="$store.i18n.t('status.{{ strtolower($cita->status) }}') || '{{ $cita->status }}'"></span>
                            </span>
                        </div>

                        {{-- Fecha y Hora --}}
                        <div class="flex items-center gap-3 mb-4">
                            <div class="kpi-icon kpi-icon--emerald w-10 h-10">
                                <span class="material-symbols-outlined icon-sm">event</span>
                            </div>
                            <div>
                                <p class="text-[11px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.dateTime')"></p>
                                <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ $cita->fecha_hora?->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Info Principal --}}
                        <div class="space-y-4 mb-6 flex-1 mt-2">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-vc-surface-alt flex items-center justify-center font-bold text-vc-primary shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">person</span>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[11px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.client')"></p>
                                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ $cita->cliente?->nombre_completo ?? '-' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">pets</span>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[11px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.pet')"></p>
                                    <p class="text-sm font-bold text-vc-primary dark:text-vc-primary-light truncate">{{ $cita->mascota?->name ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[16px] text-zinc-400 mt-0.5">medical_services</span>
                                    <div class="overflow-hidden">
                                        <p class="text-[11px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.veterinarian')"></p>
                                        <p class="text-xs text-zinc-700 dark:text-zinc-300 truncate">{{ $cita->veterinario?->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[16px] text-zinc-400 mt-0.5">stethoscope</span>
                                    <div class="overflow-hidden">
                                        <p class="text-[11px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.reason')"></p>
                                        <p class="text-xs text-zinc-700 dark:text-zinc-300 truncate" title="{{ $cita->reason }}">{{ $cita->reason ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-1.5 items-center">
                            <button type="button" class="btn-primary w-full sm:w-auto justify-center" 
                                @click="$wire.ver({{ $cita->id }}).then(() => Flux.modal('ver-cita').show())">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                <span x-text="$store.i18n.t('btn.view') || 'Ver Detalles'"></span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-center">
                {{ $citas->links() }}
            </div>
        </x-vc-table-layout>
    @endif

    {{-- Modal Eliminar --}}
    <flux:modal :closable="false" name="confirmar-eliminar" class="w-[90vw] md:w-full max-w-md">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">warning</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.confirmDelete') || 'Eliminar Cita'"></h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.confirmDeleteMsg') || 'Esta acción no se puede revertir.'"></p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></flux:button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto btn-danger font-medium justify-center" wire:click="eliminar" x-on:click="Flux.modal('confirmar-eliminar').close()">
                    <span x-text="$store.i18n.t('btn.delete') || 'Eliminar'"></span>
                </button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Reasignación Masiva --}}
    <flux:modal :closable="false" name="reasignar-modal" class="min-w-120">
        <form wire:submit.prevent="reasignarMasivo" class="space-y-6">
            <div>
                <flux:heading size="lg">Reasignación Masiva de Citas</flux:heading>
                <flux:subheading>
                    Mueva todas las citas pendientes o confirmadas de un veterinario a otro para una fecha específica.
                </flux:subheading>
            </div>
            
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Fecha</flux:label>
                    <x-vc-date-picker wire:model="reasignar_fecha" />
                    <flux:error name="reasignar_fecha" />
                </flux:field>
                <flux:field>
                    <flux:label>Veterinario Origen (Inasistente)</flux:label>
                    @php
                        $vetOptions = [];
                        foreach ($veterinarios as $vet) {
                            $vetOptions[] = ['value' => (string)$vet->id, 'label' => $vet->name];
                        }
                    @endphp
                    <x-vc-dropdown
                        wire:model="reasignar_origen"
                        :options="$vetOptions"
                        :selected="$reasignar_origen"
                        placeholder="Seleccione veterinario"
                        icon="medical_services"
                    />
                    <flux:error name="reasignar_origen" />
                </flux:field>
                <flux:field>
                    <flux:label>Veterinario Destino (Reemplazo)</flux:label>
                    <x-vc-dropdown
                        wire:model="reasignar_destino"
                        :options="$vetOptions"
                        :selected="$reasignar_destino"
                        placeholder="Seleccione veterinario"
                        icon="medical_services"
                    />
                    <flux:error name="reasignar_destino" />
                </flux:field>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></flux:button>
                </flux:modal.close>
                <button type="submit" class="btn-primary justify-center">
                    <span class="material-symbols-outlined icon-sm">sync_alt</span>
                    <span>Reasignar</span>
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal Cita de Emergencia --}}
    <flux:modal :closable="false" name="emergencia-modal" class="min-w-120">
        <form wire:submit.prevent="crearEmergencia" class="space-y-6">
            <div>
                <flux:heading size="lg" class="text-red-600">Crear Cita de Emergencia</flux:heading>
                <flux:subheading>
                    Genera una cita inmediatamente sin asignar veterinario. Todos los veterinarios verán esta alerta.
                </flux:subheading>
            </div>
            
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Cliente (Dueño)</flux:label>
                    @php
                        $clienteOptionsEmergencia = [['value' => '', 'label' => 'Seleccione cliente']];
                        foreach ($clientes as $cliente) {
                            $clienteOptionsEmergencia[] = ['value' => (string)$cliente->id, 'label' => $cliente->first_name . ' ' . $cliente->last_name];
                        }
                    @endphp
                    <x-vc-dropdown
                        wire:model.live="emergencia_cliente_id"
                        :options="$clienteOptionsEmergencia"
                        :selected="$emergencia_cliente_id"
                        placeholder="Seleccione cliente"
                        searchable
                        icon="person"
                    />
                    <flux:error name="emergencia_cliente_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Mascota</flux:label>
                    @php
                        $mascotaOptionsEmergencia = [['value' => '', 'label' => 'Seleccione mascota']];
                        $mascotasDisponibles = $emergencia_cliente_id ? \App\Models\Pet::where('customer_id', $emergencia_cliente_id)->get() : [];
                        foreach ($mascotasDisponibles as $mascota) {
                            $mascotaOptionsEmergencia[] = ['value' => (string)$mascota->id, 'label' => $mascota->name];
                        }
                    @endphp
                    <x-vc-dropdown
                        wire:model="emergencia_mascota_id"
                        :options="$mascotaOptionsEmergencia"
                        :selected="$emergencia_mascota_id"
                        placeholder="Seleccione mascota"
                        searchable
                        icon="pets"
                    />
                    <flux:error name="emergencia_mascota_id" />
                </flux:field>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></flux:button>
                </flux:modal.close>
                <button type="submit" class="btn-primary flex items-center justify-center gap-2" style="background-color: var(--color-vc-danger); border-color: var(--color-vc-danger); color: white;">
                    <span class="material-symbols-outlined icon-sm">emergency</span>
                    <span>Crear Emergencia</span>
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal Ver Cita --}}
    <flux:modal :closable="true" name="ver-cita" class="w-[90vw] md:w-full max-w-2xl">
        @if($citaVer)
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <flux:heading size="lg">Detalles de la Cita</flux:heading>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300">
                        {{ $citaVer->status }}
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-vc-primary uppercase tracking-wider flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">info</span> Información General
                    </h3>
                    <ul class="space-y-3 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-700/50">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">event</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">Fecha y Hora</p>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $citaVer->fecha_hora?->format('d/m/Y H:i') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">person</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">Cliente</p>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $citaVer->cliente?->nombre_completo }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">pets</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">Mascota</p>
                                <p class="text-sm font-bold text-vc-primary dark:text-vc-primary-light">{{ $citaVer->mascota?->name }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-vc-primary uppercase tracking-wider flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">medical_services</span> Detalles Clínicos
                    </h3>
                    <ul class="space-y-3 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-700/50 h-[calc(100%-2rem)]">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">stethoscope</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">Veterinario</p>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $citaVer->veterinario?->name ?? 'No asignado' }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-purple-50 dark:bg-purple-500/10 text-purple-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">assignment</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">Motivo</p>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $citaVer->reason }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            @if($citaVer->notes)
            <div class="mt-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30">
                <p class="text-[10px] font-bold text-amber-600 dark:text-amber-500 mb-2 uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">sticky_note_2</span> Notas adicionales
                </p>
                <p class="text-sm text-amber-900 dark:text-amber-200 whitespace-pre-line">{{ $citaVer->notes }}</p>
            </div>
            @endif
            
            <div class="flex flex-col sm:flex-row flex-wrap justify-end gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                <div class="flex-1 flex gap-2">
                    <a href="{{ route('citas.pdf', $citaVer->id) }}" target="_blank" class="w-full sm:w-auto px-4 py-2 bg-zinc-600 hover:bg-zinc-700 text-white rounded-xl shadow-sm hover:shadow text-sm font-semibold flex items-center justify-center gap-2 transition-all">
                        <span class="material-symbols-outlined icon-sm">picture_as_pdf</span>
                        Descargar PDF
                    </a>
                </div>
                <div class="flex flex-col-reverse sm:flex-row gap-2">
                    @if($citaVer->status === 'PENDIENTE')
                        <button type="button" wire:click="cambiarEstado({{ $citaVer->id }}, 'CONFIRMADA')" class="w-full sm:w-auto px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-sm hover:shadow text-sm font-semibold flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined icon-sm">check_circle</span>
                            Confirmar
                        </button>
                    @endif

                    @if($citaVer->historiaClinica)
                        <a href="{{ route('historias.ver', $citaVer->historiaClinica->id) }}" class="w-full sm:w-auto px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow-sm hover:shadow text-sm font-semibold flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined icon-sm">description</span>
                            Ver HC
                        </a>
                    @elseif(in_array($citaVer->status, ['CONFIRMADA', 'EN_PROGRESO']) && $citaVer->fecha_hora?->isToday())
                        <button type="button" wire:click="iniciarAtencion({{ $citaVer->id }})" class="w-full sm:w-auto px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-sm hover:shadow text-sm font-semibold flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined icon-sm">clinical_notes</span>
                            Iniciar Atención
                        </button>
                    @endif

                    <a href="{{ route('citas.editar', $citaVer->id) }}" class="w-full sm:w-auto px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl shadow-sm hover:shadow text-sm font-semibold flex items-center justify-center gap-2 transition-all">
                        <span class="material-symbols-outlined icon-sm">edit</span>
                        Editar
                    </a>
                    
                    <button type="button" @click="$wire.citaEliminarId = {{ $citaVer->id }}; Flux.modal('ver-cita').close(); Flux.modal('confirmar-eliminar').show()" class="btn-danger w-full sm:w-auto justify-center">
                        <span class="material-symbols-outlined icon-sm">delete</span>
                    </button>
                </div>
            </div>
        </div>
        @endif
    </flux:modal>

</div>

@script
<script>
    Alpine.data('vcCalendar', () => ({
        initCalendar(el) {
            let retries = 0;
            const tryInit = () => {
                if (window.initVetCalendar) {
                    window.initVetCalendar(el, $wire);
                } else if (retries < 50) {
                    retries++;
                    setTimeout(tryInit, 200);
                }
            };
            tryInit();
        },
        refreshCalendar() {
            if (window._vcActiveCalendar) {
                window._vcActiveCalendar.refetchEvents();
            }
        },
        updateSize() {
            if (window._vcActiveCalendar) {
                window._vcActiveCalendar.updateSize();
            }
        }
    }));
</script>
@endscript

<style>
/* FullCalendar Custom Premium Styling */
:root {
    --fc-border-color: #3f3f46; /* zinc-700 */
    --fc-button-bg-color: #27272a; /* zinc-800 */
    --fc-button-border-color: #3f3f46;
    --fc-button-hover-bg-color: #3f3f46;
    --fc-button-hover-border-color: #52525b;
    --fc-button-active-bg-color: #10b981; /* emerald-500 */
    --fc-button-active-border-color: #059669; /* emerald-600 */
    --fc-event-border-color: transparent;
    --fc-page-bg-color: transparent;
    --fc-neutral-bg-color: rgba(255, 255, 255, 0.05);
    --fc-list-event-hover-bg-color: rgba(16, 185, 129, 0.1);
    --fc-today-bg-color: rgba(16, 185, 129, 0.05);
}

.fc-theme-standard td, .fc-theme-standard th {
    border-color: var(--fc-border-color);
}
.fc .fc-toolbar-title {
    font-family: 'Outfit', 'Inter', sans-serif;
    font-weight: 700;
    font-size: 1.5rem;
    color: #f4f4f5; /* zinc-100 */
}
.fc .fc-button {
    font-family: 'Inter', sans-serif;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: capitalize;
    border-radius: 0.5rem;
    padding: 0.4rem 0.8rem;
    transition: all 0.2s ease;
}
.fc .fc-button-primary:not(:disabled).fc-button-active, 
.fc .fc-button-primary:not(:disabled):active {
    background-color: var(--fc-button-active-bg-color);
    border-color: var(--fc-button-active-border-color);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}
.fc .fc-col-header-cell-cushion {
    padding: 4px;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #a1a1aa; /* zinc-400 */
}
.fc .fc-button-primary:focus, 
.fc .fc-button-primary:not(:disabled).fc-button-active:focus {
    box-shadow: none !important;
    outline: none !important;
}
.fc-direction-ltr .fc-timegrid-slot-label-frame {
    text-align: right;
    font-size: 0.75rem;
    color: #a1a1aa;
    padding-right: 8px;
}
.fc .fc-list-empty {
    background-color: rgba(39, 39, 42, 0.5) !important;
    border-radius: 0.75rem;
    margin: 2rem;
}
.fc .fc-list-empty-cushion {
    padding: 3rem;
    font-family: 'Inter', sans-serif;
    color: #71717a; /* zinc-500 */
    font-weight: 500;
}
.fc-timegrid-event, .fc-daygrid-event {
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}
.fc-event-main {
    padding: 2px 4px;
    font-size: 0.75rem;
    font-weight: 500;
}
html.dark .fc .fc-list-day-cushion {
    background-color: #27272a; /* zinc-800 */
    color: #f4f4f5;
    font-weight: 700;
    padding: 6px 16px !important;
}
.fc-theme-standard .fc-list-day-cushion {
    background-color: #f4f4f5; 
    font-weight: 700;
    padding: 6px 16px !important;
}

/* Quitar padding raro y puntos en la vista Lista */
.fc-list-event-graphic {
    display: none !important;
}
.fc-list-event-time {
    display: none !important;
}
.fc .fc-list-event-title {
    padding: 0 !important;
}

/* Responsive Fixes */
@media (max-width: 768px) {
    .fc-header-toolbar {
        flex-direction: column;
        gap: 1rem;
    }
    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
        width: 100%;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .fc .fc-toolbar-title {
        font-size: 1.25rem !important;
        text-align: center;
    }
    .fc-view-harness {
        overflow-x: auto !important;
    }
    .fc-view {
        min-width: 700px; /* Permitir scroll horizontal en móviles para que no se apachurre */
    }
}

/* Quitar outline de los botones y redondear el encabezado principal */
.fc .fc-button:focus,
.fc .fc-button:active,
.fc .fc-button-primary:not(:disabled):active, 
.fc .fc-button-primary:not(:disabled).fc-button-active {
    outline: none !important;
    box-shadow: none !important;
    border-color: transparent !important;
}
.fc-theme-standard th {
    border: none !important;
    background-color: var(--fc-neutral-bg-color);
}
.fc-theme-standard .fc-scrollgrid {
    border: 1px solid var(--fc-border-color);
    border-radius: 0.5rem;
    overflow: hidden;
}
.fc-col-header {
    border-radius: 0.5rem 0.5rem 0 0;
    overflow: hidden;
}

/* Ajustes de diseño solicitados */
.fc-scrollgrid-sync-inner {
    padding: 0 !important;
}
.fc-theme-standard .fc-scrollgrid-section-header > th {
    background-color: var(--fc-neutral-bg-color) !important;
    padding: 0 !important;
}
.fc .fc-col-header-cell-cushion {
    padding: 12px 0 !important;
    display: inline-block;
    width: 100%;
    text-align: center;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #a1a1aa; 
    transition: background-color 0.2s, color 0.2s;
}
.fc .fc-col-header-cell-cushion:hover {
    background-color: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

/* Quitar rectangulo negro del scrollbar (gutter) */
.fc-scrollgrid-sync-table {
    width: 100% !important;
}
.fc-scroller-liquid-absolute {
    background: transparent !important;
}
.fc-theme-standard th {
    border: none !important;
    background-color: transparent !important;
    padding: 0 !important; /* Quitar padding interno para que cuadre exacto */
}
.fc-theme-standard .fc-scrollgrid-section-header > th {
    background-color: var(--fc-neutral-bg-color) !important;
    padding: 0 !important;
}

/* Estilo para la vista de Lista */
.fc-list-event:hover td {
    background-color: rgba(16, 185, 129, 0.05) !important;
    cursor: pointer;
}

/* Agregar título a la columna de horas - Oculto por solicitud */
.fc-timegrid-slot {
    height: 5em !important; /* Aumentar altura de las celdas para las cards */
}

/* Ocultar el punto en los eventos de mes para que luzca como bloque */
.fc-daygrid-event-dot {
    display: none !important;
}
.fc-daygrid-dot-event .fc-event-title {
    font-weight: 400 !important;
    padding: 0 !important;
}
/* Ocultar texto nativo de la hora del evento para insertarlo customizado */
.fc-event-time {
    display: none !important;
}
/* Énfasis en las cards de citas al pasar el mouse */
.fc-timegrid-event, .fc-daygrid-event {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.fc-timegrid-event:hover, .fc-daygrid-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2) !important;
    z-index: 50 !important;
}
/* Cursor pointer para las vistas de lista */
.fc-list-event {
    cursor: pointer;
}

/* -------------------------------------
   MEJORAS VISUALES EN LA VISTA LISTA 
   ------------------------------------- */
.fc-theme-standard .fc-list {
    border-radius: 0.5rem;
    overflow: hidden;
}
.fc-theme-standard .fc-list-day-cushion {
    background-color: var(--fc-neutral-bg-color) !important;
    padding: 12px 16px !important;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.fc-list-day-text, .fc-list-day-side-text {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #3f3f46 !important; /* zinc-700 */
    text-transform: capitalize;
    text-decoration: none !important; /* Asegurar que no parezca link */
}
.dark .fc-list-day-text, .dark .fc-list-day-side-text {
    color: #e4e4e7 !important; /* zinc-200 */
}
.fc-list-day-side-text {
    background: #10b98115; /* emerald-500 con opacidad */
    color: #059669 !important; /* emerald-600 */
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 12px !important;
}
.dark .fc-list-day-side-text {
    color: #34d399 !important; /* emerald-400 */
}

/* -------------------------------------
   ARREGLO DE HORAS CORTADAS EN CALENDARIO
   ------------------------------------- */
.fc-theme-standard .fc-timegrid-axis-cushion {
    max-width: none !important;
    padding: 0 8px !important;
    font-size: 11px !important;
    color: var(--vc-text-muted) !important;
}
.fc-timegrid-slot-label-cushion {
    padding-right: 8px !important;
    white-space: nowrap !important;
    font-size: 11px !important;
    color: var(--vc-text-muted) !important;
}
/* Forzar un ancho mínimo para la columna de horas */
.fc-theme-standard .fc-timegrid-axis-frame,
.fc-timegrid-slot-label-frame {
    min-width: 60px !important;
    width: 60px !important;
    max-width: 60px !important;
}
</style>
