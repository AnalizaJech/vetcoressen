<div x-data>
    <x-slot:title>{{ $citaId ? 'Edit Appointment' : 'New Appointment' }}</x-slot:title>

    {{-- Cabecera --}}
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('citas.index') }}" variant="ghost" size="sm" icon="arrow-left" />
        <div>
            <flux:heading size="xl" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500">calendar_month</span>
                <span x-text="$store.i18n.t({{ $citaId ? '\'page.editAppointment\'' : '\'page.newAppointment\'' }})"></span>
            </flux:heading>
        </div>
    </div>

    <form x-on:submit.prevent="{{ $citaId ? '$dispatch(\'modal-show\', { name: \'confirmar-actualizacion\' })' : '$wire.guardar()' }}" class="space-y-6">
        {{-- â•â•â• Paciente â•â•â• --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">pets</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.patient')"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.clientLabel')"></span></flux:label>
                        @php
                            $clienteOptions = [];
                            foreach ($clientes as $c) {
                                $clienteOptions[] = ['value' => (string)$c->id, 'label' => $c->nombre_completo];
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model.live="cliente_id"
                            :options="$clienteOptions"
                            :selected="$cliente_id"
                            placeholder="form.selectClient"
                            icon="person"
                            searchable
                        />
                    <flux:error name="cliente_id" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('table.pet')"></span></flux:label>
                        @php
                            $mascotaOptions = [];
                            foreach ($mascotas as $m) {
                                $mascotaOptions[] = ['value' => (string)$m->id, 'label' => $m->name . ' (' . ($m->especie?->name ?? 'N/A') . ')'];
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:key="mascota-dropdown-{{ $cliente_id ?? 'empty' }}"
                            wire:model="mascota_id"
                            :options="$mascotaOptions"
                            :selected="$mascota_id"
                            placeholder="form.select"
                            icon="pets"
                            :disabled="!$cliente_id"
                            searchable
                        />
                    <flux:error name="mascota_id" />
                </flux:field>
            </div>
        </div>

        {{-- --- Detalle --- --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">calendar_month</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.detail')"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('table.veterinarian')"></span></flux:label>
                        @php
                            $vetOptions = [];
                            foreach ($veterinarios as $v) {
                                $vetOptions[] = ['value' => (string)$v->id, 'label' => $v->name];
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model.live="veterinario_id"
                            :options="$vetOptions"
                            :selected="$veterinario_id"
                            placeholder="form.select"
                            icon="medical_services"
                        />
                    <flux:error name="veterinario_id" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.dateLabel')"></span></flux:label>
                    <x-vc-date-picker wire:model.live="fecha" minDate="today" x-bind:placeholder="$store.i18n.t('form.dateLabel')" />
                    <flux:error name="fecha" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.timeLabel') || 'Hora'"></span></flux:label>
                    <x-vc-time-picker 
                        wire:model="hora" 
                        placeholder="00:00"
                    />
                    <flux:error name="hora" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.stateLabel')"></span></flux:label>
                        <x-vc-dropdown
                            wire:model="estado"
                            :options="[
                                ['value' => 'PENDIENTE', 'label' => 'status.pending'],
                                ['value' => 'CONFIRMADA', 'label' => 'status.confirmed'],
                                ['value' => 'EN_PROGRESO', 'label' => 'status.inProgress'],
                                ['value' => 'COMPLETADA', 'label' => 'status.completed'],
                                ['value' => 'CANCELADA', 'label' => 'status.cancelled'],
                            ]"
                            :selected="$estado"
                            placeholder="form.select"
                            icon="info"
                        />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.consultReason')"></span></flux:label>
                    <flux:textarea wire:model="motivo" rows="3" x-bind:placeholder="$store.i18n.t('form.reasonPlaceholder') || 'Describa el motivo de la consulta...'" />
                    <flux:error name="motivo" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.notes')"></span></flux:label>
                    <flux:textarea wire:model="notas" rows="3" x-bind:placeholder="$store.i18n.t('form.notesPlaceholder') || 'Escribe notas adicionales...'" />
                </flux:field>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <flux:button href="{{ route('citas.index') }}" variant="ghost" class="w-full sm:w-auto">
                <span x-text="$store.i18n.t('btn.cancel')"></span>
            </flux:button>
            
            @if(!$citaId)
                <button type="button" 
                        class="w-full sm:w-auto btn-danger justify-center"
                        wire:click="guardarEmergencia"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="guardarEmergencia" class="flex items-center gap-2">
                        <span class="material-symbols-outlined icon-sm">emergency</span>
                        <span x-text="$store.i18n.t('btn.emergency', 'Emergencia Rápida')"></span>
                    </span>
                    <span wire:loading wire:target="guardarEmergencia" class="flex items-center gap-2">
                        <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                        <span x-text="$store.i18n.t('btn.registering', 'Registrando...')"></span>
                    </span>
                </button>
            @endif

            <button type="{{ $citaId ? 'button' : 'submit' }}" 
                    class="w-full sm:w-auto {{ $citaId ? 'btn-violet' : 'btn-primary' }} justify-center" 
                    @if($citaId) x-on:click.prevent="$dispatch('modal-show', { name: 'confirmar-actualizacion' })" @endif
                    wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm" x-text="{{ $citaId ? '\'edit\'' : '\'save\'' }}"></span>
                    <span x-text="$store.i18n.t({{ $citaId ? '\'btn.update\'' : '\'btn.register\'' }})"></span>
                </span>
                <span wire:loading wire:target="guardar" class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    <span x-text="$store.i18n.t('btn.saving')"></span>
                </span>
            </button>
        </div>
    </form>

    {{-- Modal de confirmacion de actualizacion --}}
    @if($citaId)
    <flux:modal :closable="false" name="confirmar-actualizacion" class="min-w-88 overflow-y-auto max-h-[85vh]">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-violet-100/50 dark:bg-violet-500/20 text-violet-600 dark:text-violet-400 rounded-full flex items-center justify-center border border-violet-200 dark:border-violet-500/30 shadow-sm shadow-violet-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">save</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.confirmUpdate') || 'Confirmar Actualización'"></h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.confirmUpdateMsg') || '¿Estás seguro de que deseas guardar los cambios realizados?'"></p>
                </div>
                
                <div class="w-full text-left rounded-xl p-4 border border-zinc-100 dark:border-zinc-800/50 space-y-2">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 flex justify-between">
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider text-[10px]" x-text="$store.i18n.t('table.client') || 'Cliente:'">Cliente:</span>
                        <span class="font-medium">{{ collect($clientes)->firstWhere('id', $cliente_id)?->nombre_completo ?? '-' }}</span>
                    </p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 flex justify-between">
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider text-[10px]" x-text="$store.i18n.t('table.pet') || 'Mascota:'">Mascota:</span>
                        <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ collect($mascotas)->firstWhere('id', $mascota_id)?->name ?? '-' }}</span>
                    </p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.cancel')"></span></flux:button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto btn-violet font-medium justify-center" wire:click="guardar" x-on:click="$dispatch('modal-close', { name: 'confirmar-actualizacion' })">
                    <span x-text="$store.i18n.t('btn.update')"></span>
                </button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
