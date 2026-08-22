<div x-data="{}" x-on:abrir-modal-ver-cita.window="Flux.modal('ver-cita').show()">
    <x-slot:title>Appointments</x-slot:title>

<div class="animate-slide-up">
    {{-- ═══ Header de Citas (Estándar Premium) ═══ --}}
    <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <span class="material-symbols-outlined text-2xl">calendar_month</span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                    <span x-text="$store.i18n.t('page.appointments') || 'Citas Médicas'">Citas Médicas</span>
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('page.appointmentsSub') || 'Programación y seguimiento de agenda clínica'">
                    Programación y seguimiento de agenda clínica
                </p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Toggle Circular Animado Vista Lista / Calendario --}}
            <div class="relative flex items-center bg-zinc-100 dark:bg-zinc-800/90 rounded-full p-1 border border-zinc-200/80 dark:border-zinc-700/80 shadow-inner">
                {{-- Indicador circular deslizante animado --}}
                <div class="absolute top-1 bottom-1 w-8 rounded-full bg-white dark:bg-zinc-700 shadow-xs border border-zinc-200/60 dark:border-zinc-600/60 transition-transform duration-300 ease-out"
                     style="transform: translateX({{ $vistaActiva === 'calendario' ? '0px' : '32px' }});"></div>

                {{-- Botón Calendario --}}
                <button 
                    type="button"
                    wire:click="$set('vistaActiva', 'calendario')" 
                    class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center transition-colors {{ $vistaActiva === 'calendario' ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200' }}"
                    x-bind:title="$store.i18n.t('citas.vistaCalendario') || 'Calendario'"
                >
                    <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                </button>

                {{-- Botón Lista --}}
                <button 
                    type="button"
                    wire:click="$set('vistaActiva', 'lista')" 
                    class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center transition-colors {{ $vistaActiva === 'lista' ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200' }}"
                    x-bind:title="$store.i18n.t('citas.vistaLista') || 'Lista'"
                >
                    <span class="material-symbols-outlined text-[18px]">view_list</span>
                </button>
            </div>

            <flux:modal.trigger name="reasignar-modal">
                <button type="button" class="btn-secondary text-xs px-3 py-2 flex items-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined icon-sm">sync_alt</span>
                    <span x-text="$store.i18n.t('btn.massReassign') || 'Reasignación'">Reasignación</span>
                </button>
            </flux:modal.trigger>

            <flux:modal.trigger name="emergencia-modal">
                <button type="button" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl shadow-sm text-xs font-bold flex items-center justify-center gap-1.5 transition-all">
                    <span class="material-symbols-outlined icon-sm">emergency</span>
                    <span x-text="$store.i18n.t('btn.emergencyAppt') || 'Emergencia'">Emergencia</span>
                </button>
            </flux:modal.trigger>

            <a href="{{ route('citas.crear') }}" wire:navigate class="btn-primary text-xs px-3.5 py-2 flex items-center justify-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newAppointment') || 'Nueva Cita'">Nueva Cita</span>
            </a>
        </div>
    </div>

    {{-- ═══ Barra de Filtros Dinámicos (Estilo Reportes con Labels) ═══ --}}
    <div class="vc-panel mb-6">
        @php
            $estadoOptions = [
                ['value' => '', 'label' => 'status.all'],
                ['value' => 'PENDIENTE', 'label' => 'status.pending'],
                ['value' => 'CONFIRMADA', 'label' => 'status.confirmed'],
                ['value' => 'EN_PROGRESO', 'label' => 'status.inProgress'],
                ['value' => 'COMPLETADA', 'label' => 'status.completed'],
                ['value' => 'CANCELADA', 'label' => 'status.cancelled'],
                ['value' => 'EMERGENCIA', 'label' => 'status.emergency'],
            ];
            $vetOptions = [['value' => '', 'label' => 'table.allVets']];
            foreach ($veterinarios as $vet) {
                $vetOptions[] = ['value' => (string)$vet->id, 'label' => $vet->name];
            }
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 {{ $vistaActiva === 'lista' ? 'lg:grid-cols-3' : 'lg:grid-cols-2' }} gap-4 items-end">
            {{-- Filtro de Estado --}}
            <div>
                <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('table.status') || 'Estado'">
                    Estado
                </label>
                <x-vc-dropdown
                    wire:model.live="filtroEstado"
                    :options="$estadoOptions"
                    :selected="$filtroEstado"
                    placeholder="table.status"
                    icon="flag"
                    class="w-full"
                />
            </div>

            {{-- Filtro de Veterinario --}}
            <div>
                <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('table.veterinarian') || 'Veterinario'">
                    Veterinario
                </label>
                <x-vc-dropdown
                    wire:model.live="filtroVeterinario"
                    :options="$vetOptions"
                    :selected="$filtroVeterinario"
                    placeholder="table.veterinarian"
                    icon="stethoscope"
                    searchable
                    class="w-full"
                />
            </div>

            {{-- Filtro de Fecha (en vista lista) --}}
            @if($vistaActiva === 'lista')
                <div>
                    <label class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1.5" x-text="$store.i18n.t('table.date') || 'Fecha'">
                        Fecha
                    </label>
                    <x-vc-date-picker 
                        wire:model.live="filtroFecha" 
                        x-bind:placeholder="$store.i18n.t('table.date') || 'Fecha'"
                        class="w-full"
                    />
                </div>
            @endif
        </div>
        
        @if($filtroEstado || $filtroFecha || $filtroHora || $filtroVeterinario || $filtroCliente || $filtroMascota)
            <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <flux:button variant="ghost" wire:click="limpiarFiltros" icon="x-mark" class="text-zinc-500 text-xs">
                    <span x-text="$store.i18n.t('btn.clearFilters') || 'Limpiar Filtros'">Limpiar Filtros</span>
                </flux:button>
            @endif
        </div>
    </div>

    {{-- ════ VISTA CALENDARIO ════ --}}
    <div x-show="$wire.vistaActiva === 'calendario'" class="animate-fade-in" x-cloak>
        <div 
            wire:ignore 
            x-data="vcCalendar()" 
            x-init="initCalendar($refs.calendarEl)"
            @calendar-refresh.window="refreshCalendar()"
            @vista-cambiada.window="if ($event.detail === 'calendario') setTimeout(() => updateSize(), 150)"
            class="vc-panel p-4 md:p-6 space-y-4"
        >
            {{-- Barra de Controles Superiores del Calendario --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                {{-- Navegación: Botones Prev, Hoy y Next separados con título --}}
                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="prev()" 
                        class="w-9 h-9 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 hover:text-zinc-950 dark:hover:text-white flex items-center justify-center transition-all shadow-xs"
                        title="Previous"
                    >
                        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                    </button>

                    <button 
                        type="button" 
                        @click="today()" 
                        class="px-3.5 h-9 rounded-xl border border-emerald-300/80 dark:border-emerald-700/80 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 text-xs font-extrabold flex items-center gap-1.5 transition-all shadow-xs hover:shadow"
                    >
                        <span class="material-symbols-outlined text-[16px]">today</span>
                        <span x-text="$store.i18n.t('form.today') || 'Today'">Today</span>
                    </button>

                    <button 
                        type="button" 
                        @click="next()" 
                        class="w-9 h-9 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 hover:text-zinc-950 dark:hover:text-white flex items-center justify-center transition-all shadow-xs"
                        title="Next"
                    >
                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                    </button>

                    <h2 class="text-base sm:text-lg font-black text-zinc-900 dark:text-zinc-100 ml-2 tracking-tight capitalize" x-text="currentTitle"></h2>
                </div>

                {{-- Selector de Períodos: Dropdown Custom en vez de botones --}}
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="$store.i18n.locale === 'en' ? 'Period:' : 'Período:'"></span>
                    <div class="w-44">
                        <x-vc-dropdown
                            :options="[
                                ['value' => 'timeGridDay', 'label' => 'citas.dayView', 'icon' => 'calendar_view_day'],
                                ['value' => 'timeGridWeek', 'label' => 'citas.weekView', 'icon' => 'calendar_view_week'],
                                ['value' => 'dayGridMonth', 'label' => 'citas.monthView', 'icon' => 'calendar_view_month'],
                                ['value' => 'listWeek', 'label' => 'citas.listView', 'icon' => 'list_alt'],
                            ]"
                            selected="timeGridWeek"
                            placeholder="citas.weekView"
                            icon="calendar_month"
                            @input="changeView($event.detail)"
                            @change="changeView($event.detail)"
                        />
                    </div>
                </div>
            </div>

            {{-- Contenedor del Calendario --}}
            <div wire:ignore x-ref="calendarEl" id="vc-fullcalendar" class="min-h-[600px] w-full"></div>
        </div>
    </div>

    {{-- ═══ VISTA LISTA (CARDS) ═══ --}}
    @if($vistaActiva === 'lista')
        <x-vc-table-layout 
            :data="$citas"
            :searchable="false"
            icon="calendar_month"
            emptyTitle="table.empty"
            emptyText="table.emptyText"
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
                                <span x-text="$store.i18n.t('status.' + '{{ strtolower($cita->status) }}') || '{{ $cita->status }}'"></span>
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

    {{-- Modal Reasignación Masiva --}}
    <flux:modal :closable="true" name="reasignar-modal" class="min-w-[26rem] overflow-y-auto max-h-[85vh]">
        <form wire:submit.prevent="reasignarMasivo" class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-blue-100/50 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center border border-blue-200 dark:border-blue-500/30 shadow-sm shadow-blue-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">sync_alt</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.massReassign') || 'Reasignación Masiva de Citas'"></h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.massReassignSub') || 'Mueva todas las citas de un veterinario a otro.'"></p>
                </div>
            </div>
            
            <div class="space-y-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.date') || 'Fecha'">Fecha</span></flux:label>
                    <x-vc-date-picker wire:model="reasignar_fecha" />
                    <flux:error name="reasignar_fecha" />
                </flux:field>
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.originVet') || 'Veterinario Origen (Inasistente)'">Veterinario Origen (Inasistente)</span></flux:label>
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
                        placeholder="form.selectVet"
                        icon="medical_services"
                    />
                    <flux:error name="reasignar_origen" />
                </flux:field>
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.destVet') || 'Veterinario Destino (Reemplazo)'">Veterinario Destino (Reemplazo)</span></flux:label>
                    <x-vc-dropdown
                        wire:model="reasignar_destino"
                        :options="$vetOptions"
                        :selected="$reasignar_destino"
                        placeholder="form.selectVet"
                        icon="medical_services"
                    />
                    <flux:error name="reasignar_destino" />
                </flux:field>
            </div>

            <div class="flex gap-2 justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button variant="ghost"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></flux:button>
                </flux:modal.close>
                <button type="submit" class="btn-primary justify-center">
                    <span class="material-symbols-outlined icon-sm">sync_alt</span>
                    <span x-text="$store.i18n.t('btn.reassign') || 'Reasignar'">Reasignar</span>
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal Cita de Emergencia --}}
    <flux:modal :closable="true" name="emergencia-modal" class="min-w-[26rem] overflow-y-auto max-h-[85vh]">
        <form wire:submit.prevent="crearEmergencia" class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">emergency</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.createEmergency') || 'Crear Cita de Emergencia'"></h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.createEmergencySub') || 'Genera una cita inmediatamente sin asignar veterinario.'"></p>
                </div>
            </div>
            
            <div class="space-y-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.client') || 'Cliente (Dueño)'">Cliente (Dueño)</span></flux:label>
                    @php
                        $clienteOptionsEmergencia = [['value' => '', 'label' => 'form.selectClient']];
                        foreach ($clientes as $cliente) {
                            $clienteOptionsEmergencia[] = ['value' => (string)$cliente->id, 'label' => $cliente->first_name . ' ' . $cliente->last_name];
                        }
                    @endphp
                    <x-vc-dropdown
                        wire:model.live="emergencia_cliente_id"
                        :options="$clienteOptionsEmergencia"
                        :selected="$emergencia_cliente_id"
                        placeholder="form.selectClient"
                        searchable
                        icon="person"
                    />
                    <flux:error name="emergencia_cliente_id" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.pet') || 'Mascota'">Mascota</span></flux:label>
                    @php
                        $mascotaOptionsEmergencia = [['value' => '', 'label' => 'form.selectPet']];
                        $mascotasDisponibles = $emergencia_cliente_id ? \App\Models\Pet::where('customer_id', $emergencia_cliente_id)->get() : [];
                        foreach ($mascotasDisponibles as $mascota) {
                            $mascotaOptionsEmergencia[] = ['value' => (string)$mascota->id, 'label' => $mascota->name];
                        }
                    @endphp
                    <x-vc-dropdown
                        wire:model="emergencia_mascota_id"
                        :options="$mascotaOptionsEmergencia"
                        :selected="$emergencia_mascota_id"
                        placeholder="form.selectPet"
                        searchable
                        icon="pets"
                    />
                    <flux:error name="emergencia_mascota_id" />
                </flux:field>
            </div>

            <div class="flex gap-2 justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:modal.close>
                    <flux:button variant="ghost"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></flux:button>
                </flux:modal.close>
                <button type="submit" class="btn-danger flex items-center justify-center gap-2 font-medium">
                    <span class="material-symbols-outlined icon-sm">emergency</span>
                    <span x-text="$store.i18n.t('btn.createEmergency') || 'Crear Emergencia'">Crear Emergencia</span>
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal Ver Cita --}}
    <flux:modal :closable="true" name="ver-cita" class="w-[90vw] md:w-full max-w-2xl overflow-y-auto max-h-[85vh]">
        @if($citaVer)
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <flux:heading size="lg"><span x-text="$store.i18n.t('modal.appointmentDetails') || 'Detalles de la Cita'">Detalles de la Cita</span></flux:heading>
                <div class="flex items-center gap-2">
                    <a x-bind:href="'{{ route('citas.pdf', $citaVer->id) }}?lang=' + ($store.i18n?.locale || localStorage.getItem('vc_locale') || 'es')" target="_blank" class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 rounded-lg text-xs font-bold flex items-center gap-1 transition-all">
                        <span class="material-symbols-outlined text-[15px]">picture_as_pdf</span>
                        <span x-text="$store.i18n.t('btn.pdf') || 'PDF'">PDF</span>
                    </a>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300">
                        <span x-text="$store.i18n.t('status.' + '{{ strtolower($citaVer->status) }}') || '{{ $citaVer->status }}'"></span>
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-vc-primary uppercase tracking-wider flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">info</span> <span x-text="$store.i18n.t('form.generalInfo') || 'Información General'">Información General</span>
                    </h3>
                    <ul class="space-y-3 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-700/50">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">event</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.dateTime') || 'Fecha y Hora'">Fecha y Hora</p>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $citaVer->fecha_hora?->format('d/m/Y H:i') }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">person</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.client') || 'Cliente'">Cliente</p>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $citaVer->cliente?->nombre_completo }}</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">pets</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.pet') || 'Mascota'">Mascota</p>
                                <p class="text-sm font-bold text-vc-primary dark:text-vc-primary-light">{{ $citaVer->mascota?->name }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-vc-primary uppercase tracking-wider flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">medical_services</span> <span x-text="$store.i18n.t('form.clinicalDetails') || 'Detalles Clínicos'">Detalles Clínicos</span>
                    </h3>
                    <ul class="space-y-3 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-700/50 h-[calc(100%-2rem)]">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">stethoscope</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.veterinarian') || 'Veterinario'">Veterinario</p>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    @if($citaVer->veterinario)
                                        {{ $citaVer->veterinario->name }}
                                    @else
                                        <span x-text="$store.i18n.t('form.notAssigned') || 'No asignado'">No asignado</span>
                                    @endif
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-purple-50 dark:bg-purple-500/10 text-purple-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">assignment</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('table.reason') || 'Motivo'">Motivo</p>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $citaVer->reason }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            @if($citaVer->notes)
            <div class="mt-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30">
                <p class="text-[10px] font-bold text-amber-600 dark:text-amber-500 mb-2 uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">sticky_note_2</span> <span x-text="$store.i18n.t('form.additionalNotes') || 'Notas adicionales'">Notas adicionales</span>
                </p>
                <p class="text-sm text-amber-900 dark:text-amber-200 whitespace-pre-line">{{ $citaVer->notes }}</p>
            </div>
            @endif
            
            <div class="flex flex-col sm:flex-row flex-wrap justify-between items-center gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                <flux:modal.close>
                    <button type="button" class="w-full sm:w-auto px-4 py-2 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 transition-all">
                        <span class="material-symbols-outlined icon-sm">close</span>
                        <span x-text="$store.i18n.t('btn.close') || 'Cerrar'">Cerrar</span>
                    </button>
                </flux:modal.close>

                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
                    @if($citaVer->status === 'PENDIENTE')
                        <button type="button" wire:click="cambiarEstado({{ $citaVer->id }}, 'CONFIRMADA')" class="w-full sm:w-auto px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-sm hover:shadow text-sm font-semibold flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined icon-sm">check_circle</span>
                            <span x-text="$store.i18n.t('btn.confirm') || 'Confirmar'">Confirmar</span>
                        </button>
                    @endif

                    @if($citaVer->historiaClinica)
                        <a href="{{ route('historias.ver', $citaVer->historiaClinica->id) }}" class="w-full sm:w-auto px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow-sm hover:shadow text-sm font-semibold flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined icon-sm">description</span>
                            <span x-text="$store.i18n.t('btn.viewHC') || 'Ver HC'">Ver HC</span>
                        </a>
                    @elseif(in_array($citaVer->status, ['CONFIRMADA', 'EN_PROGRESO']) && $citaVer->fecha_hora?->isToday())
                        <button type="button" wire:click="iniciarAtencion({{ $citaVer->id }})" class="w-full sm:w-auto px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-sm hover:shadow text-sm font-semibold flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined icon-sm">clinical_notes</span>
                            <span x-text="$store.i18n.t('btn.startAttention') || 'Iniciar Atención'">Iniciar Atención</span>
                        </button>
                    @endif

                    <a href="{{ route('citas.editar', $citaVer->id) }}" class="w-full sm:w-auto px-3.5 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl shadow-sm hover:shadow text-sm font-semibold flex items-center justify-center transition-all" x-bind:aria-label="$store.i18n.t('btn.edit') || 'Edit'" x-bind:title="$store.i18n.t('btn.edit') || 'Edit'">
                        <span class="material-symbols-outlined icon-sm">edit</span>
                    </a>
                    
                    <button type="button" @click="$wire.citaEliminarId = {{ $citaVer->id }}; Flux.modal('ver-cita').close(); Flux.modal('confirmar-eliminar').show()" class="btn-danger w-full sm:w-auto justify-center">
                        <span class="material-symbols-outlined icon-sm">delete</span>
                    </button>
                </div>
            </div>
            </div>
        </div>
        @endif
    </flux:modal>

    {{-- Modal Eliminar --}}
    <flux:modal :closable="false" name="confirmar-eliminar" class="min-w-[22rem]">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">delete</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.deleteAppointment') || 'Eliminar Cita'">Eliminar Cita</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.deleteAppointmentSub') || 'Esta acción no se puede deshacer. ¿Está seguro que desea eliminar esta cita del sistema?'">Esta acción no se puede deshacer. ¿Está seguro que desea eliminar esta cita del sistema?</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></span></flux:button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto btn-danger font-medium justify-center" wire:click="eliminar" x-on:click="$dispatch('modal-close', { name: 'confirmar-eliminar' })">
                    <span x-text="$store.i18n.t('btn.delete') || 'Eliminar'"><span x-text="$store.i18n.t('btn.delete') || 'Eliminar'"></span></span>
                </button>
            </div>
        </div>
    </flux:modal>

@script
<script>
    Alpine.data('vcCalendar', () => ({
        currentTitle: '',
        currentView: 'timeGridWeek',

        initCalendar(el) {
            let retries = 0;
            const tryInit = () => {
                if (window.initVetCalendar) {
                    window.initVetCalendar(el, $wire);
                } else if (retries < 50) {
                    retries++;
                    setTimeout(tryInit, 150);
                }
            };
            tryInit();

            window.addEventListener('calendar-view-updated', (e) => {
                if (e.detail?.title) {
                    this.currentTitle = e.detail.title;
                }
                if (e.detail?.type) {
                    this.currentView = e.detail.type;
                }
            });

            this.$watch('currentView', (viewName) => {
                if (viewName) {
                    window.dispatchEvent(new CustomEvent('calendar-set-view', { detail: { view: viewName } }));
                }
            });

            // Re-calcular tamaño al cerrar cualquier modal para evitar que se rompa la estilización
            window.addEventListener('modal-close', () => {
                setTimeout(() => this.updateSize(), 100);
            });
            window.addEventListener('modal-hide', () => {
                setTimeout(() => this.updateSize(), 100);
            });
        },

        changeView(v) {
            if (!v) return;
            this.currentView = v;
            if (window._vcActiveCalendar) {
                window._vcActiveCalendar.changeView(v);
                if (window._vcActiveCalendar.view) {
                    this.currentTitle = window._vcActiveCalendar.view.title;
                }
            }
            window.dispatchEvent(new CustomEvent('calendar-set-view', { detail: { view: v } }));
        },

        prev() {
            window.dispatchEvent(new CustomEvent('calendar-prev'));
            if (window._vcActiveCalendar?.view) {
                this.currentTitle = window._vcActiveCalendar.view.title;
            }
        },

        next() {
            window.dispatchEvent(new CustomEvent('calendar-next'));
            if (window._vcActiveCalendar?.view) {
                this.currentTitle = window._vcActiveCalendar.view.title;
            }
        },

        today() {
            window.dispatchEvent(new CustomEvent('calendar-today'));
            if (window._vcActiveCalendar?.view) {
                this.currentTitle = window._vcActiveCalendar.view.title;
            }
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
/* ═══ ESTILOS PREMIUM FULLCALENDAR ═══ */
:root {
    --fc-border-color: #e2e8f0;
    --fc-page-bg-color: #ffffff;
    --fc-neutral-bg-color: #f8fafc;
    --fc-today-bg-color: rgba(16, 185, 129, 0.04);
    --fc-slot-border-color: #e2e8f0;
    --fc-slot-minor-border-color: #f1f5f9;
}

html.dark, .dark {
    --fc-border-color: rgba(63, 63, 70, 0.5);
    --fc-page-bg-color: #18181b;
    --fc-neutral-bg-color: #27272a;
    --fc-today-bg-color: rgba(16, 185, 129, 0.06);
    --fc-slot-border-color: rgba(63, 63, 70, 0.4);
    --fc-slot-minor-border-color: rgba(63, 63, 70, 0.2);
}

/* Grilla y Contenedores */
.fc-theme-standard .fc-scrollgrid {
    border: 1px solid var(--fc-border-color) !important;
    border-radius: 14px;
    overflow: hidden;
    background-color: var(--fc-page-bg-color);
}
.fc-theme-standard td, .fc-theme-standard th {
    border-color: var(--fc-border-color) !important;
}

/* Encabezados de Columna (Días) */
.fc-theme-standard .fc-col-header {
    background-color: var(--fc-neutral-bg-color) !important;
}
.fc-theme-standard .fc-col-header-cell {
    background-color: var(--fc-neutral-bg-color) !important;
    border-bottom: 2px solid var(--fc-border-color) !important;
    border-right: 1px solid var(--fc-border-color) !important;
    padding: 10px 0 !important;
}
.fc-col-header-cell-cushion {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    color: #334155 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.06em !important;
    text-decoration: none !important;
    display: inline-block;
    padding: 4px 6px !important;
}
.dark .fc-col-header-cell-cushion {
    color: #cbd5e1 !important;
}

/* Eje de Horas (TimeGrid) */
.fc-theme-standard .fc-timegrid-axis {
    background-color: var(--fc-neutral-bg-color) !important;
    border-right: 1px solid var(--fc-border-color) !important;
}
.fc-theme-standard .fc-timegrid-axis-cushion,
.fc-timegrid-slot-label-cushion {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #64748b !important;
    text-decoration: none !important;
    display: block;
    text-align: right;
    padding-right: 8px !important;
}
.dark .fc-theme-standard .fc-timegrid-axis-cushion,
.dark .fc-timegrid-slot-label-cushion {
    color: #94a3b8 !important;
}
.fc-theme-standard .fc-timegrid-axis-frame,
.fc-timegrid-slot-label-frame {
    min-width: 64px !important;
    width: 64px !important;
    max-width: 64px !important;
}

/* Slots de tiempo y cuadrícula */
.fc-timegrid-slots table {
    border-collapse: collapse;
}
.fc-timegrid-slot {
    height: 48px !important;
    border-bottom: 1px solid var(--fc-slot-border-color) !important;
}
.fc-timegrid-slot-minor {
    border-bottom: 1px dashed var(--fc-slot-minor-border-color) !important;
}
.fc-timegrid-cols .fc-timegrid-col {
    border-right: 1px solid var(--fc-border-color) !important;
}
.fc-timegrid-col.fc-day-today {
    background-color: var(--fc-today-bg-color) !important;
}

/* Eventos y Cards de Citas */
.fc-timegrid-event {
    border-radius: 8px !important;
    border: none !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04) !important;
    overflow: hidden !important;
    background: transparent !important;
    margin: 1px 3px !important;
}
.fc-timegrid-event .fc-event-main {
    padding: 0 !important;
    height: 100% !important;
}
.fc-daygrid-event {
    border-radius: 6px !important;
    margin: 2px 4px !important;
}
.fc-event-time {
    display: none !important;
}

/* Now Indicator */
.fc .fc-timegrid-now-indicator-line {
    border-color: #ef4444 !important;
    border-width: 2px !important;
    z-index: 15 !important;
}
.fc .fc-timegrid-now-indicator-arrow {
    border-color: #ef4444 !important;
    border-top-color: transparent !important;
    border-bottom-color: transparent !important;
}

/* Vista Lista */
.fc-theme-standard .fc-list {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--fc-border-color) !important;
}
.fc-theme-standard .fc-list-day-cushion {
    background-color: #f8fafc !important;
    padding: 10px 16px !important;
}
.dark .fc-theme-standard .fc-list-day-cushion {
    background-color: #27272a !important;
}
.fc-list-day-text {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    text-decoration: none !important;
}
.dark .fc-list-day-text {
    color: #f4f4f5 !important;
}
.fc-list-event:hover td {
    background-color: rgba(16, 185, 129, 0.05) !important;
    cursor: pointer;
}

/* Popover More Events */
.fc-popover {
    background-color: var(--fc-page-bg-color) !important;
    border: 1px solid var(--fc-border-color) !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
    overflow: hidden;
    z-index: 50 !important;
}
.fc-popover-header {
    background-color: var(--fc-neutral-bg-color) !important;
    color: inherit !important;
    padding: 10px 12px !important;
    font-weight: bold !important;
}
</style>
    </div>
</div>
