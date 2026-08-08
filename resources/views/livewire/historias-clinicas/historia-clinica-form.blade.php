<div x-data>
    <x-slot:title>{{ $historiaId ? 'Editar Historia Clínica' : 'Nueva Historia Clínica' }}</x-slot:title>

    {{-- Cabecera --}}
    <div class="flex items-center gap-3 mb-8">
        <flux:button href="{{ route('historias.index') }}" variant="ghost" size="sm" icon="arrow-left" />
        <div>
            <flux:heading size="xl" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500 text-3xl">medical_information</span>
                <span class="font-bold" x-text="$store.i18n.t({{ $historiaId ? '\'page.editRecord\'' : '\'page.newRecord\'' }})"></span>
            </flux:heading>
        </div>
    </div>

    <form x-on:submit.prevent="{{ $historiaId ? 'Flux.modal(\'confirmar-actualizacion\').show()' : '$wire.guardar()' }}" class="space-y-8">
        
        {{-- Banner de Citas Pendientes --}}
        @if($citas && $citas->count() > 0 && !$historiaId)
            <div class="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 animate-fade-in shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <span class="material-symbols-outlined">calendar_clock</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-indigo-900 dark:text-indigo-300">Citas pendientes detectadas</h3>
                        <p class="text-xs text-indigo-700 dark:text-indigo-400">Esta mascota tiene {{ $citas->count() }} cita(s) programada(s). ¿Deseas autocompletar la información?</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach($citas->take(1) as $citaPendiente)
                        <button type="button" class="btn-primary py-1.5 px-3 text-sm flex items-center gap-1 shadow-sm" wire:click="seleccionarCita({{ $citaPendiente->id }})">
                            <span class="material-symbols-outlined text-[16px]">bolt</span>
                            Autocompletar con cita #{{ $citaPendiente->id }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Seccion: Paciente y Consulta --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 lg:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined">pets</span>
                </div>
                <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200" x-text="$store.i18n.t('form.patientAndConsult')"></h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Cliente (cascada) --}}
                <flux:field>
                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('table.owner')"></span></flux:label>
                    <div x-data="{ placeholderText: $store.i18n.t('form.selectClient') }">
                        @php
                            $hcClienteOpts = [];
                            foreach ($clientes as $cli) {
                                $hcClienteOpts[] = ['value' => (string)$cli->id, 'label' => $cli->name_completo];
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model.live="customer_id"
                            :options="$hcClienteOpts"
                            :selected="$customer_id"
                            x-bind:placeholder="placeholderText"
                            searchable="true"
                            icon="person"
                        />
                    </div>
                    <flux:error name="customer_id" />
                </flux:field>

                {{-- Mascota --}}
                <flux:field>
                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('table.pet')"></span></flux:label>
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
                            wire:model.live="pet_id"
                            :options="$hcMascotaOpts"
                            :selected="$pet_id"
                            x-bind:placeholder="placeholderText"
                            :disabled="!$customer_id"
                            icon="pets"
                        />
                    </div>
                    <flux:error name="pet_id" />
                </flux:field>

                {{-- Veterinario (Hardcoded por ahora o desde BD) --}}
                <flux:field>
                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('table.doctor')"></span></flux:label>
                    <div x-data="{ placeholderText: $store.i18n.t('form.select') }">
                        @php
                            $hcVetOpts = [];
                            foreach ($veterinarios as $vet) {
                                $hcVetOpts[] = ['value' => (string)$vet->id, 'label' => $vet->name];
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model="veterinarian_id"
                            :options="$hcVetOpts"
                            :selected="$veterinarian_id"
                            x-bind:placeholder="placeholderText"
                            icon="medical_services"
                        />
                    </div>
                    <flux:error name="veterinarian_id" />
                </flux:field>

                {{-- Fecha --}}
                <flux:field>
                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('table.date')"></span></flux:label>
                    <flux:input type="date" wire:model="fecha_consulta" class="h-[42px]">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="fecha_consulta" />
                </flux:field>
            </div>

            <div class="mt-5" x-data="{ ph: $store.i18n.t('form.reasonPlaceholder') }">
                <flux:field>
                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('table.reason')"></span></flux:label>
                    <flux:textarea wire:model="reason" x-bind:placeholder="ph" class="resize-none" rows="2" />
                    <flux:error name="motivo_consulta" />
                </flux:field>
            </div>
        </div>

        {{-- Seccion: Triaje (Signos Vitales) --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 lg:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400">
                    <span class="material-symbols-outlined">vital_signs</span>
                </div>
                <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200" x-text="$store.i18n.t('form.triage')"></h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                <flux:field>
                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.weight')"></span></flux:label>
                    <flux:input type="number" step="0.01" wire:model.live.debounce.500ms="weight" class="h-[42px]">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">scale</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="weight" />
                    @if($alerta_peso)
                        <div class="text-amber-600 text-xs mt-1 font-medium">{{ $alerta_peso }}</div>
                    @endif
                </flux:field>

                <flux:field>
                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.temperature')"></span></flux:label>
                    <flux:input type="number" step="0.1" wire:model.live.debounce.500ms="temperatura" class="h-[42px]">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">local_fire_department</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="temperatura" />
                    @if($alerta_temp)
                        <div class="text-amber-600 text-xs mt-1 font-medium">{{ $alerta_temp }}</div>
                    @endif
                </flux:field>

                <flux:field>
                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.heartRate')"></span></flux:label>
                    <flux:input type="number" wire:model="frecuencia_cardiaca" class="h-[42px]">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">favorite</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="frecuencia_cardiaca" />
                </flux:field>

                <flux:field>
                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.respRate')"></span></flux:label>
                    <flux:input type="number" wire:model="frecuencia_respiratoria" class="h-[42px]">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">air</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="frecuencia_respiratoria" />
                </flux:field>
            </div>
        </div>

        {{-- Seccion: Anamnesis y Diagnostico --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 lg:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <span class="material-symbols-outlined">clinical_notes</span>
                </div>
                <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200" x-text="$store.i18n.t('form.diagnosis')"></h3>
            </div>

            <div class="space-y-5">
                <flux:field>
                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.anamnesis')"></span></flux:label>
                    <flux:textarea wire:model="anamnesis" rows="3" placeholder="Background, symptoms..." class="resize-none" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <flux:field>
                        <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.presumptiveDiag')"></span></flux:label>
                        <flux:textarea wire:model="diagnostico_presuntivo" rows="3" placeholder="Diagnosis based on clinical findings..." class="resize-none" />
                    </flux:field>

                    <flux:field>
                        <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.treatment')"></span></flux:label>
                        <flux:textarea wire:model="tratamiento_indicaciones" rows="3" placeholder="Treatment plan..." class="resize-none" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <flux:field>
                        <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.nextAppointment')"></span></flux:label>
                        <flux:input wire:model="proxima_cita_recomendada" type="date" class="h-[42px]">
                            <x-slot:iconLeading>
                                <span class="material-symbols-outlined text-[18px]">event</span>
                            </x-slot:iconLeading>
                        </flux:input>
                    </flux:field>

                    <flux:field>
                        <flux:label class="mb-2 font-medium">Notas Aclaratorias Anexas (Posterior a 24h)</flux:label>
                        <flux:textarea wire:model="notas_aclaratorias" rows="2" placeholder="Agregue notas o correcciones si la historia está bloqueada..." class="resize-none" />
                    </flux:field>
                </div>
            </div>
        </div>

        {{-- Seccion: Prescripciones --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 lg:p-8 shadow-sm">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center text-violet-600 dark:text-violet-400">
                        <span class="material-symbols-outlined">prescriptions</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200" x-text="$store.i18n.t('form.prescriptions')"></h3>
                </div>

                <flux:button wire:click="agregarPrescripcion" variant="ghost" size="sm" icon="plus" class="hover:bg-violet-50 dark:hover:bg-violet-500/10 hover:text-violet-600 dark:hover:text-violet-400">
                    <span x-text="$store.i18n.t('btn.addMedication')"></span>
                </flux:button>
            </div>

            @if(count($prescripciones) === 0)
                <div class="text-center py-10 bg-zinc-50 dark:bg-zinc-800/30 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-700">
                    <span class="material-symbols-outlined text-zinc-400 dark:text-zinc-500 text-4xl mb-2">medication</span>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 block mb-3">
                        <span x-text="$store.i18n.t('empty.noPrescriptions')"></span>
                    </flux:text>
                    <flux:button wire:click="agregarPrescripcion" variant="ghost" size="sm">
                        + Añadir primera prescripción
                    </flux:button>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($prescripciones as $index => $rx)
                        <div class="relative bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-700 rounded-xl p-5 animate-fade-in transition hover:border-violet-200 dark:hover:border-violet-800" wire:key="rx-{{ $index }}">
                            {{-- Boton eliminar prescripcion --}}
                            <div class="absolute top-3 right-3">
                                <flux:button
                                    wire:click="eliminarPrescripcion({{ $index }})"
                                    variant="subtle"
                                    size="sm"
                                    icon="trash"
                                    class="text-rose-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10"
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 pr-10">
                                {{-- Medicamento (ahora del inventario o manual) --}}
                                <flux:field>
                                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.medication')"></span></flux:label>
                                    @php
                                        $productosOpts = [];
                                        foreach ($productos as $prod) {
                                            $productosOpts[] = ['value' => (string)$prod->id, 'label' => $prod->name];
                                        }
                                    @endphp
                                    <div x-data="{ prodPh: 'Buscar producto...' }">
                                        {{-- Dropdown para seleccionar producto --}}
                                        <x-vc-dropdown
                                            wire:model.live="prescripciones.{{ $index }}.product_id"
                                            :options="$productosOpts"
                                            :selected="$rx['product_id'] ?? ''"
                                            x-bind:placeholder="prodPh"
                                            searchable="true"
                                            icon="vaccines"
                                        />
                                    </div>
                                    <flux:error name="prescripciones.{{ $index }}.product_id" />
                                    <div class="mt-2 flex gap-2 items-center">
                                         <flux:input size="sm"
                                            wire:model="prescripciones.{{ $index }}.medicamento"
                                            placeholder="Nombre manual (si no está en lista)"
                                         />
                                    </div>
                                </flux:field>

                                {{-- Dosis --}}
                                <flux:field>
                                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.dose')"></span></flux:label>
                                    <flux:input
                                        wire:model="prescripciones.{{ $index }}.dosage"
                                        placeholder="Ej: 0.5 ml/kg"
                                        class="h-[42px]"
                                    >
                                        <x-slot:iconLeading>
                                            <span class="material-symbols-outlined text-[18px]">medication_liquid</span>
                                        </x-slot:iconLeading>
                                    </flux:input>
                                    <flux:error name="prescripciones.{{ $index }}.dosage" />
                                </flux:field>

                                {{-- Frecuencia --}}
                                <flux:field>
                                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.frequency')"></span></flux:label>
                                    <flux:input
                                        wire:model="prescripciones.{{ $index }}.frequency"
                                        placeholder="Ej: Cada 8 horas"
                                        class="h-[42px]"
                                    >
                                        <x-slot:iconLeading>
                                            <span class="material-symbols-outlined text-[18px]">update</span>
                                        </x-slot:iconLeading>
                                    </flux:input>
                                    <flux:error name="prescripciones.{{ $index }}.frequency" />
                                </flux:field>

                                {{-- Via de administracion --}}
                                <flux:field>
                                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.route')"></span></flux:label>
                                    <x-vc-dropdown
                                        wire:model="prescripciones.{{ $index }}.via_administracion"
                                        :options="[
                                            ['value' => 'Oral', 'label' => 'Oral'],
                                            ['value' => 'Intramuscular', 'label' => 'Intramuscular'],
                                            ['value' => 'Intravenosa', 'label' => 'Intravenosa'],
                                            ['value' => 'Subcutánea', 'label' => 'Subcutánea'],
                                            ['value' => 'Tópica', 'label' => 'Tópica'],
                                        ]"
                                        :selected="$rx['via_administracion'] ?? 'Oral'"
                                        placeholder="Seleccionar vía"
                                        icon="science"
                                    />
                                </flux:field>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 pr-10 mt-5">
                                {{-- Duracion en dias --}}
                                <flux:field class="md:col-span-1">
                                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.duration')"></span> (Días)</flux:label>
                                    <flux:input
                                        wire:model="prescripciones.{{ $index }}.duracion_dias"
                                        type="number"
                                        min="1"
                                        max="365"
                                        class="h-[42px]"
                                    >
                                        <x-slot:iconLeading>
                                            <span class="material-symbols-outlined text-[18px]">today</span>
                                        </x-slot:iconLeading>
                                    </flux:input>
                                    <flux:error name="prescripciones.{{ $index }}.duracion_dias" />
                                </flux:field>

                                {{-- Indicaciones --}}
                                <flux:field class="md:col-span-3">
                                    <flux:label class="mb-2 font-medium"><span x-text="$store.i18n.t('form.instructions')"></span></flux:label>
                                    <flux:input
                                        wire:model="prescripciones.{{ $index }}.indicaciones"
                                        placeholder="Con comida, en ayunas, etc."
                                        class="h-[42px]"
                                    />
                                </flux:field>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Botones --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
            <flux:button href="{{ route('historias.index') }}" variant="ghost" class="w-full sm:w-auto h-11 px-6">
                <span x-text="$store.i18n.t('btn.cancel')"></span>
            </flux:button>
            <button
                type="{{ $historiaId ? 'button' : 'submit' }}"
                class="w-full sm:w-auto h-11 px-6 font-semibold rounded-xl text-white transition-all shadow-sm flex items-center justify-center gap-2 {{ $historiaId ? 'bg-violet-600 hover:bg-violet-700 shadow-violet-500/20' : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white shadow-zinc-900/20' }}"
                @if($historiaId) x-on:click.prevent="$dispatch('modal-show', { name: 'confirmar-actualizacion' })" @endif
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm" x-text="{{ $historiaId ? '\'edit\'' : '\'save\'' }}"></span>
                    <span x-text="$store.i18n.t({{ $historiaId ? '\'btn.update\'' : '\'btn.register\'' }})"></span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm animate-spin">progress_activity</span>
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
                    <flux:button variant="ghost" class="w-full font-medium h-11"><span x-text="$store.i18n.t('btn.cancel')"></span></flux:button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto h-11 px-6 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-xl flex items-center justify-center transition shadow-sm shadow-violet-500/20" wire:click="guardar" x-on:click="$dispatch('modal-close', { name: 'confirmar-actualizacion' })">
                    <span x-text="$store.i18n.t('btn.update')"></span>
                </button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
