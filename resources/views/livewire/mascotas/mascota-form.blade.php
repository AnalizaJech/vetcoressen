<div>
    <x-slot:title>{{ $mascotaId ? 'Edit Pet' : 'New Pet' }}</x-slot:title>

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <flux:button href="{{ route('mascotas.index') }}" variant="ghost" size="sm" icon="arrow-left" />
            <div>
                <flux:heading size="xl" class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">pets</span>
                    <span x-text="$store.i18n.t({{ $mascotaId ? '\'page.editPet\'' : '\'page.newPet\'' }})"></span>
                </flux:heading>
            </div>
        </div>


    </div>

    <form x-on:submit.prevent="{{ $mascotaId ? '$dispatch(\'modal-show\', { name: \'confirmar-actualizacion\' })' : '$wire.guardar()' }}" class="space-y-6">
        {{-- ═══ Propietario ═══ --}}
        <div class="vc-panel" style="position: relative; z-index: 20;">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">person</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.ownerSection')"></span>
            </div>

            <flux:field>
                <flux:label><span x-text="$store.i18n.t('form.clientLabel')"></span></flux:label>
                @php
                    $clienteOptions = [];
                    foreach ($clientes as $c) {
                        $clienteOptions[] = ['value' => (string)$c->id, 'label' => $c->nombre_completo];
                    }
                @endphp
                <x-vc-dropdown
                    wire:model="customer_id"
                    :options="$clienteOptions"
                    :selected="$customer_id"
                    placeholder="form.selectClient"
                    icon="person"
                    searchable
                />
                <flux:error name="customer_id" />
            </flux:field>
        </div>

        {{-- ═══ Datos de la mascota ═══ --}}
        <div class="vc-panel" style="position: relative; z-index: 10;">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">pets</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.petData')"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.petName')"></span></flux:label>
                    <flux:input wire:model="name" x-bind:placeholder="$store.i18n.t('placeholder.petName') || 'Luna'" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.speciesLabel')"></span></flux:label>
                    @php
                        $especieOptions = [];
                        foreach ($especies as $e) {
                            $especieOptions[] = ['value' => (string)$e->id, 'label' => $e->name];
                        }
                    @endphp
                    <x-vc-dropdown
                        wire:model.live="especie_id"
                        :options="$especieOptions"
                        :selected="$especie_id"
                        placeholder="form.select"
                        icon="pets"
                        searchable
                    />
                    <flux:error name="especie_id" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.breedLabel')"></span></flux:label>
                    @php
                        $razaOptions = [];
                        foreach ($razas as $r) {
                            $razaOptions[] = ['value' => (string)$r->id, 'label' => $r->name];
                        }
                    @endphp
                    <x-vc-dropdown
                        wire:key="raza-dropdown-{{ $especie_id ?? 'empty' }}"
                        wire:model="raza_id"
                        :options="$razaOptions"
                        :selected="$raza_id"
                        placeholder="form.select"
                        icon="category"
                        searchable
                    />
                    <flux:error name="raza_id" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.sexLabel')"></span></flux:label>
                    <x-vc-dropdown
                        wire:model="gender"
                        :options="[
                            ['value' => 'M', 'label' => 'Macho'],
                            ['value' => 'H', 'label' => 'Hembra'],
                        ]"
                        :selected="$gender"
                        placeholder="form.select"
                        icon="transgender"
                    />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.color')"></span></flux:label>
                    <x-vc-dropdown
                        wire:model="color"
                        :options="[
                            ['value' => 'Blanco', 'label' => 'Blanco'],
                            ['value' => 'Negro', 'label' => 'Negro'],
                            ['value' => 'Marrón', 'label' => 'Marrón'],
                            ['value' => 'Dorado', 'label' => 'Dorado'],
                            ['value' => 'Gris', 'label' => 'Gris'],
                            ['value' => 'Manchado', 'label' => 'Manchado'],
                            ['value' => 'Otro', 'label' => 'Otro'],
                        ]"
                        :selected="$color"
                        placeholder="form.select"
                        icon="palette"
                    />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.birthDate')"></span></flux:label>
                    <x-vc-date-picker wire:model="birth_date" x-bind:placeholder="$store.i18n.t('form.birthDate')" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.currentWeight')"></span></flux:label>
                    <flux:input wire:model="peso_actual" type="number" step="0.01" x-bind:placeholder="$store.i18n.t('placeholder.weight') || '15.5'" />
                </flux:field>

                <flux:field class="flex items-end">
                    <label class="flex items-center gap-2 pb-1.5 cursor-pointer">
                        <flux:checkbox wire:model="esterilizado" />
                        <span class="text-sm" style="color: var(--vc-text-secondary);" x-text="$store.i18n.t('form.sterilized')"></span>
                    </label>
                </flux:field>
            </div>

            <div class="mt-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.medicalNotes')"></span></flux:label>
                    <flux:textarea wire:model="medical_notes" rows="3" x-bind:placeholder="$store.i18n.t('placeholder.notes') || 'Observaciones...'" />
                </flux:field>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <flux:button href="{{ route('mascotas.index') }}" variant="ghost" class="w-full sm:w-auto">
                <span x-text="$store.i18n.t('btn.cancel')"></span>
            </flux:button>
            <button type="{{ $mascotaId ? 'button' : 'submit' }}" 
                    class="w-full sm:w-auto {{ $mascotaId ? 'btn-violet' : 'btn-primary' }} justify-center" 
                    @if($mascotaId) x-on:click.prevent="$dispatch('modal-show', { name: 'confirmar-actualizacion' })" @endif
                    wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm" x-text="{{ $mascotaId ? '\'edit\'' : '\'save\'' }}"></span>
                    <span x-text="$store.i18n.t({{ $mascotaId ? '\'btn.update\'' : '\'btn.register\'' }})"></span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    <span x-text="$store.i18n.t('btn.saving')"></span>
                </span>
            </button>
        </div>
    </form>

    {{-- Modal de confirmacion de actualizacion --}}
    @if($mascotaId)
    <flux:modal :closable="false" name="confirmar-actualizacion" class="w-[90vw] md:w-full max-w-md">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-violet-100/50 dark:bg-violet-500/20 text-violet-600 dark:text-violet-400 rounded-full flex items-center justify-center border border-violet-200 dark:border-violet-500/30 shadow-sm shadow-violet-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">save</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white">Guardar Cambios</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed">Se actualizará la información de la mascota en el sistema.</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.cancel') === 'btn.cancel' ? 'Cancelar' : $store.i18n.t('btn.cancel')">Cancelar</span></flux:button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto btn-violet font-medium justify-center" wire:click="guardar" x-on:click="$dispatch('modal-close', { name: 'confirmar-actualizacion' })">
                    <span x-text="$store.i18n.t('btn.update') === 'btn.update' ? 'Actualizar' : $store.i18n.t('btn.update')">Actualizar</span>
                </button>
            </div>
        </div>
    </flux:modal>
    @endif


</div>
