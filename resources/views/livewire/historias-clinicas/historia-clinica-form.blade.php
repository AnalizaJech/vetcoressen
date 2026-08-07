<div x-data>
    <x-slot:title>{{ $historiaId ? 'Edit Medical Record' : 'New Medical Record' }}</x-slot:title>

    {{-- Cabecera --}}
    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('historias.index') }}" variant="ghost" size="sm" icon="arrow-left" />
        <div>
            <flux:heading size="xl" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500">medical_information</span>
                <span x-text="$store.i18n.t({{ $historiaId ? '\'page.editRecord\'' : '\'page.newRecord\'' }})"></span>
            </flux:heading>
        </div>
    </div>

    <form x-on:submit.prevent="{{ $historiaId ? 'Flux.modal(\'confirmar-actualizacion\').show()' : '$wire.guardar()' }}" class="space-y-6">
        {{-- â•â•â• Secci�n: Paciente y Consulta â•â•â• --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">pets</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.patientAndConsult')"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Cliente (cascada) --}}
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('table.owner')"></span></flux:label>
                    <div x-data="{ placeholderText: $store.i18n.t('form.selectClient') }">
                        @php
                            $hcClienteOpts = [];
                            foreach ($clientes as $cli) {
                                $hcClienteOpts[] = ['value' => (string)$cli->id, 'label' => $cli->name_completo];
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model.live="cliente_id"
                            :options="$hcClienteOpts"
                            :selected="$cliente_id"
                            x-bind:placeholder="placeholderText"
                            searchable="true"
                            icon="user"
                        />
                    </div>
                    <flux:error name="cliente_id" />
                </flux:field>

                {{-- Mascota --}}
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('table.pet')"></span></flux:label>
                    <div x-data="{ placeholderText: $store.i18n.t('form.select') }">
                        @php
                            $hcMascotaOpts = [];
                            if ($mascotas) {
                                foreach ($mascotas as $m) {
                                    $hcMascotaOpts[] = ['value' => (string)$m->id, 'label' => $m->name];
                                }
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model="pet_id"
                            :options="$hcMascotaOpts"
                            :selected="$mascota_id"
                            x-bind:placeholder="placeholderText"
                            :disabled="!$cliente_id"
                            icon="pets"
                        />
                    </div>
                    <flux:error name="mascota_id" />
                </flux:field>

                {{-- Veterinario (Hardcoded por ahora o desde BD) --}}
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('table.doctor')"></span></flux:label>
                    <div x-data="{ placeholderText: $store.i18n.t('form.select') }">
                        <x-vc-dropdown
                            wire:model="veterinarian_id"
                            :options="[
                                ['value' => '1', 'label' => 'Dr. Smith']
                            ]"
                            :selected="$veterinario_id"
                            x-bind:placeholder="placeholderText"
                            icon="medical_services"
                        />
                    </div>
                    <flux:error name="veterinario_id" />
                </flux:field>

                {{-- Fecha --}}
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('table.date')"></span></flux:label>
                    <flux:input type="date" wire:model="fecha_consulta" />
                    <flux:error name="fecha_consulta" />
                </flux:field>
            </div>

            <div class="mt-4" x-data="{ ph: $store.i18n.t('form.reasonPlaceholder') }">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('table.reason')"></span></flux:label>
                    <flux:textarea wire:model="reason" x-bind:placeholder="ph" />
                    <flux:error name="motivo_consulta" />
                </flux:field>
            </div>
        </div>

        {{-- â• â• â•  Secci�n: Triaje (Signos Vitales) â• â• â•  --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">vital_signs</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.triage')"></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.weight')"></span></flux:label>
                    <flux:input type="number" step="0.01" wire:model.live.debounce.500ms="weight" />
                    <flux:error name="weight" />
                    @if($alerta_peso)
                        <div class="text-amber-600 text-xs mt-1 font-medium">{{ $alerta_peso }}</div>
                    @endif
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.temperature')"></span></flux:label>
                    <flux:input type="number" step="0.1" wire:model.live.debounce.500ms="temperatura">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">fire</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="temperatura" />
                    @if($alerta_temp)
                        <div class="text-amber-600 text-xs mt-1 font-medium">{{ $alerta_temp }}</div>
                    @endif
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.heartRate')"></span></flux:label>
                    <flux:input type="number" wire:model="frecuencia_cardiaca">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">favorite</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="frecuencia_cardiaca" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.respRate')"></span></flux:label>
                    <flux:input type="number" wire:model="frecuencia_respiratoria" />
                    <flux:error name="frecuencia_respiratoria" />
                </flux:field>
            </div>
        </div>

        {{-- â• â• â•  Secci�n: Anamnesis y Diagn�stico â• â• â•  --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">clinical_notes</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.diagnosis')"></span>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.anamnesis')"></span></flux:label>
                    <flux:textarea wire:model="anamnesis" rows="3" placeholder="Background, symptoms..." />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label><span x-text="$store.i18n.t('form.presumptiveDiag')"></span></flux:label>
                        <flux:textarea wire:model="diagnostico_presuntivo" rows="3" placeholder="Diagnosis based on clinical findings..." />
                    </flux:field>

                    <flux:field>
                        <flux:label><span x-text="$store.i18n.t('form.treatment')"></span></flux:label>
                        <flux:textarea wire:model="tratamiento_indicaciones" rows="3" placeholder="Treatment plan..." />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.nextAppointment')"></span></flux:label>
                    <flux:input wire:model="proxima_cita_recomendada" type="date" />
                </flux:field>

                <flux:field>
                    <flux:label>Notas Aclaratorias Anexas (Posterior a 24h)</flux:label>
                    <flux:textarea wire:model="notas_aclaratorias" rows="2" placeholder="Agregue notas o correcciones si la historia está bloqueada..." />
                </flux:field>
            </div>
        </div>

        {{-- â• â• â•  Secci�n: Prescripciones â• â• â•  --}}
        <div class="vc-panel">
            <div class="flex items-center justify-between mb-4">
                <div class="vc-section-header mb-0">
                    <div class="vc-section-icon" style="background: var(--vc-warning-light);">
                        <span class="material-symbols-outlined" style="color: var(--vc-warning);">prescriptions</span>
                    </div>
                    <span class="vc-section-title" x-text="$store.i18n.t('form.prescriptions')"></span>
                </div>

                <flux:button wire:click="agregarPrescripcion" variant="ghost" size="sm" icon="plus">
                    <span x-text="$store.i18n.t('btn.addMedication')"></span>
                </flux:button>
            </div>

            @if(count($prescripciones) === 0)
                <div class="text-center py-6">
                    <flux:text size="sm" class="text-vc-text-muted">
                        <span x-text="$store.i18n.t('empty.noPrescriptions')"></span>
                    </flux:text>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($prescripciones as $index => $rx)
                        <div class="relative border border-vc-border rounded-lg p-4 animate-fade-in" wire:key="rx-{{ $index }}">
                            {{-- Bot�n eliminar prescripci�n --}}
                            <div class="absolute top-2 right-2">
                                <flux:button
                                    wire:click="eliminarPrescripcion({{ $index }})"
                                    variant="ghost"
                                    size="xs"
                                    icon="x-mark"
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pr-8">
                                {{-- Medicamento (libre o del inventario) --}}
                                <flux:field>
                                    <flux:label><span x-text="$store.i18n.t('form.medication')"></span></flux:label>
                                    <flux:input
                                        wire:model="prescripciones.{{ $index }}.medicamento"
                                        placeholder="Medication name"
                                        list="productos-list"
                                    />
                                    <flux:error name="prescripciones.{{ $index }}.medicamento" />
                                </flux:field>

                                {{-- Dosis --}}
                                <flux:field>
                                    <flux:label><span x-text="$store.i18n.t('form.dose')"></span></flux:label>
                                    <flux:input
                                        wire:model="prescripciones.{{ $index }}.dosis"
                                        placeholder="Ex: 0.5 ml/kg"
                                    />
                                    <flux:error name="prescripciones.{{ $index }}.dosis" />
                                </flux:field>

                                {{-- Frecuencia --}}
                                <flux:field>
                                    <flux:label><span x-text="$store.i18n.t('form.frequency')"></span></flux:label>
                                    <flux:input
                                        wire:model="prescripciones.{{ $index }}.frecuencia"
                                        placeholder="Ex: Every 8 hours"
                                    />
                                    <flux:error name="prescripciones.{{ $index }}.frecuencia" />
                                </flux:field>

                                {{-- V�a de administraci�n --}}
                                <flux:field>
                                    <flux:label><span x-text="$store.i18n.t('form.route')"></span></flux:label>
                                    <x-vc-dropdown
                                        wire:model="prescripciones.{{ $index }}.via_administracion"
                                        :options="[
                                            ['value' => 'Oral', 'label' => 'Oral'],
                                            ['value' => 'Intramuscular', 'label' => 'Intramuscular'],
                                            ['value' => 'Intravenosa', 'label' => 'Intravenosa'],
                                            ['value' => 'Subcut�nea', 'label' => 'Subcut�nea'],
                                            ['value' => 'T�pica', 'label' => 'T�pica'],
                                        ]"
                                        :selected="$rx['via_administracion'] ?? 'Oral'"
                                        placeholder="Select route"
                                        icon="beaker"
                                    />
                                </flux:field>

                                {{-- Duraci�n en d�as --}}
                                <flux:field>
                                    <flux:label><span x-text="$store.i18n.t('form.duration')"></span></flux:label>
                                    <flux:input
                                        wire:model="prescripciones.{{ $index }}.duracion_dias"
                                        type="number"
                                        min="1"
                                        max="365"
                                    />
                                    <flux:error name="prescripciones.{{ $index }}.duracion_dias" />
                                </flux:field>

                                {{-- Indicaciones --}}
                                <flux:field>
                                    <flux:label><span x-text="$store.i18n.t('form.instructions')"></span></flux:label>
                                    <flux:input
                                        wire:model="prescripciones.{{ $index }}.indicaciones"
                                        placeholder="With food, etc."
                                    />
                                </flux:field>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Datalist para autocompletar medicamentos del inventario --}}
            <datalist id="productos-list">
                @foreach($productos as $prod)
                    <option value="{{ $prod->name }}">
                @endforeach
            </datalist>
        </div>

        {{-- â• â• â•  Botones â• â• â•  --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <flux:button href="{{ route('historias.index') }}" variant="ghost" class="w-full sm:w-auto">
                <span x-text="$store.i18n.t('btn.cancel')"></span>
            </flux:button>
            <button
                type="{{ $historiaId ? 'button' : 'submit' }}"
                class="w-full sm:w-auto {{ $historiaId ? 'btn-violet' : 'btn-primary' }} justify-center"
                @if($historiaId) x-on:click.prevent="$dispatch('modal-show', { name: 'confirmar-actualizacion' })" @endif
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm" x-text="{{ $historiaId ? '\'edit\'' : '\'save\'' }}"></span>
                    <span x-text="$store.i18n.t({{ $historiaId ? '\'btn.update\'' : '\'btn.register\'' }})"></span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    <span x-text="$store.i18n.t('btn.saving')"></span>
                </span>
            </button>
        </div>
    </form>

    {{-- Modal de confirmacion de actualizacion --}}
    @if($historiaId)
    <flux:modal :closable="false" name="confirmar-actualizacion" class="w-[90vw] md:w-full max-w-md">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-violet-100/50 dark:bg-violet-500/20 text-violet-600 dark:text-violet-400 rounded-full flex items-center justify-center border border-violet-200 dark:border-violet-500/30 shadow-sm shadow-violet-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">save</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.updateHistory') || 'Confirmar Actualización'"></h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.updateHistoryMsg') || '¿Estás seguro de que deseas guardar los cambios realizados?'"></p>
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
