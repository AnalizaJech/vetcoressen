<div>
    <x-slot:title>{{ $clienteId ? 'Edit Client' : 'New Client' }}</x-slot:title>

    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <flux:button href="{{ route('clientes.index') }}" variant="ghost" size="sm" icon="arrow-left" />
            <div>
                <flux:heading size="xl" class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">person_add</span>
                    <span x-text="$store.i18n.t('{{ $clienteId ? 'page.editClient' : 'page.newClient' }}')"></span>
                </flux:heading>
                <flux:subheading>
                    <span x-text="$store.i18n.t('{{ $clienteId ? 'page.clientEditSub' : 'page.clientFormSub' }}')"></span>
                </flux:subheading>
            </div>
        </div>


    </div>

    <form x-on:submit.prevent="{{ $clienteId ? '$dispatch(\'modal-show\', { name: \'confirmar-actualizacion\' })' : '$wire.guardar()' }}" class="space-y-6">
        {{-- â•â•â• Secci�n: Documento e identificaci�n â•â•â• --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.identification')"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                <div>
                    <flux:label><span x-text="$store.i18n.t('form.documentType')"></span></flux:label>
                    <div class="mt-1">
                        <x-vc-dropdown
                            wire:model.live="tipo_documento"
                            :options="[
                                ['value' => 'DNI', 'label' => 'doc.dni'],
                                ['value' => 'RUC', 'label' => 'doc.ruc'],
                                ['value' => 'CE', 'label' => 'doc.ce'],
                                ['value' => 'PASAPORTE', 'label' => 'doc.passport'],
                            ]"
                            :selected="$tipo_documento"
                            placeholder="form.select"
                            icon="badge"
                        />
                    </div>
                </div>

                <div class="md:col-span-2">
                    <flux:label><span x-text="$store.i18n.t('form.documentNumber')"></span></flux:label>
                    <div class="flex items-stretch gap-2 mt-1">
                        <flux:input
                            wire:model="numero_documento"
                            x-bind:placeholder="'{{ $tipo_documento }}' === 'DNI' ? $store.i18n.t('placeholder.dni', 'Ingrese DNI (8 dígitos)') : ('{{ $tipo_documento }}' === 'RUC' ? $store.i18n.t('placeholder.ruc', 'Ingrese RUC (11 dígitos)') : $store.i18n.t('placeholder.document', 'Ingrese documento'))"
                            :maxlength="$tipo_documento === 'RUC' ? 11 : 8"
                            icon="identification"
                            class="flex-1"
                        />
                        @if(in_array($tipo_documento, ['DNI', 'RUC']))
                            <button
                                type="button"
                                class="btn-primary justify-center h-auto"
                                wire:click="consultarPeruApi"
                                wire:loading.attr="disabled"
                                wire:target="consultarPeruApi"
                            >
                                <span wire:loading.remove wire:target="consultarPeruApi" class="flex items-center gap-2">
                                    <span class="material-symbols-outlined icon-sm">search</span>
                                    <span x-text="$store.i18n.t('btn.search')"></span>
                                </span>
                                <span wire:loading wire:target="consultarPeruApi" class="flex items-center gap-2">
                                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                                    <span x-text="$store.i18n.t('btn.searching')"></span>
                                </span>
                            </button>
                        @endif
                    </div>
                    @if($peruApiError)
                        <p class="mt-1.5 text-xs" style="color: var(--vc-danger-light);">{{ $peruApiError }}</p>
                    @endif
                    <flux:error name="numero_documento" />
                </div>
            </div>
        </div>

        {{-- â•â•â• Secci�n: Datos personales â•â•â• --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">person</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.personalData')"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>
                        <span x-text="$store.i18n.t('{{ $tipo_documento === 'RUC' ? 'form.businessName' : 'form.names' }}')"></span>
                    </flux:label>
                    <flux:input wire:model="first_name" x-bind:placeholder="$store.i18n.t('{{ $tipo_documento === 'RUC' ? 'placeholder.businessName' : 'placeholder.names' }}', '{{ $tipo_documento === 'RUC' ? 'Ej. VetCorp SAC' : 'Ej. Juan Pérez' }}')">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">person</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="first_name" />
                </flux:field>

                @if($tipo_documento !== 'RUC')
                    <flux:field>
                        <flux:label><span x-text="$store.i18n.t('form.lastName')"></span></flux:label>
                        <flux:input wire:model="last_name" x-bind:placeholder="$store.i18n.t('placeholder.lastName', 'Apellidos')">
                            <x-slot:iconLeading>
                                <span class="material-symbols-outlined text-[18px]">group</span>
                            </x-slot:iconLeading>
                        </flux:input>
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('table.email')"></span></flux:label>
                    <flux:input wire:model="email" type="email" x-bind:placeholder="$store.i18n.t('placeholder.email', 'client@example.com')">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">mail</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('table.phone')"></span></flux:label>
                    <flux:input wire:model="phone" x-bind:placeholder="$store.i18n.t('placeholder.phone', '987654321')">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">phone</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="phone" />
                </flux:field>
            </div>
        </div>

        {{-- â•â•â• Secci�n: Ubicaci�n â•â•â• --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">location_on</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.location')"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.country')"></span></flux:label>
                    <div>
                        @php
                            $countryOptions = [];
                            foreach ($countries as $c) {
                                if (isset($c['iso2']) && isset($c['name'])) {
                                    $countryOptions[] = ['value' => $c['iso2'], 'label' => $c['name']];
                                }
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model.live="country"
                            :options="$countryOptions"
                            :selected="$country"
                            placeholder="form.select"
                            icon="public"
                        />
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.stateLabel') || 'Estado / Región'"></span></flux:label>
                    <div>
                        @php
                            $stateOptions = [];
                            foreach ($states as $s) {
                                if (isset($s['iso2']) && isset($s['name'])) {
                                    $stateOptions[] = ['value' => $s['iso2'], 'label' => $s['name']];
                                }
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model.live="state"
                            :options="$stateOptions"
                            :selected="$state"
                            placeholder="form.select"
                            :disabled="!$country"
                            icon="map"
                        />
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.city')"></span></flux:label>
                    <div>
                        @php
                            $cityOptions = [];
                            foreach ($cities as $ci) {
                                if (isset($ci['name'])) {
                                    $cityOptions[] = ['value' => $ci['name'], 'label' => $ci['name']];
                                }
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model="city"
                            :options="$cityOptions"
                            :selected="$city"
                            placeholder="form.select"
                            :disabled="!$state"
                            icon="location_city"
                        />
                    </div>
                </flux:field>
            </div>

            <flux:field>
                <flux:label><span x-text="$store.i18n.t('form.address')"></span></flux:label>
                <flux:input wire:model="address" x-bind:placeholder="$store.i18n.t('placeholder.address', 'Av. Principal 123')">
                    <x-slot:iconLeading>
                        <span class="material-symbols-outlined text-[18px]">location_on</span>
                    </x-slot:iconLeading>
                </flux:input>
            </flux:field>
        </div>

        {{-- â•â•â• Notas â•â•â• --}}
        <div class="vc-panel">
            <flux:field>
                <flux:label><span x-text="$store.i18n.t('form.additionalNotes')"></span></flux:label>
                <flux:textarea wire:model="notes" rows="3" x-bind:placeholder="$store.i18n.t('placeholder.notes', 'Observaciones...')" />
            </flux:field>
        </div>

        {{-- ═══ Botones ═══ --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <flux:button href="{{ route('clientes.index') }}" variant="ghost" class="w-full sm:w-auto">
                <span x-text="$store.i18n.t('btn.cancel')"></span>
            </flux:button>
            <button type="{{ $clienteId ? 'button' : 'submit' }}" 
                    class="w-full sm:w-auto {{ $clienteId ? 'btn-violet' : 'btn-primary' }} justify-center" 
                    @if($clienteId) x-on:click.prevent="$dispatch('modal-show', { name: 'confirmar-actualizacion' })" @endif
                    wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm" x-text="'{{ $clienteId ? 'edit' : 'save' }}'"></span>
                    <span x-text="$store.i18n.t('{{ $clienteId ? 'btn.update' : 'btn.register' }}')"></span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    <span x-text="$store.i18n.t('btn.saving')"></span>
                </span>
            </button>
        </div>
    </form>

    {{-- Modal de confirmacion de actualizacion --}}
    @if($clienteId)
    <flux:modal :closable="false" name="confirmar-actualizacion" class="min-w-88 overflow-y-auto max-h-[85vh]">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-violet-100/50 dark:bg-violet-500/20 text-violet-600 dark:text-violet-400 rounded-full flex items-center justify-center border border-violet-200 dark:border-violet-500/30 shadow-sm shadow-violet-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">save</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white"><span x-text="$store.i18n.t('btn.saveChanges') || 'Guardar Cambios'"></span></h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed">Se actualizará la información del cliente en el sistema.</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close class="w-full sm:w-auto">
                    <flux:button variant="ghost" class="w-full font-medium"><span x-text="$store.i18n.t('btn.cancel') === 'btn.cancel' ? 'Cancelar' : $store.i18n.t('btn.cancel')"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'"></span></span></flux:button>
                </flux:modal.close>
                <button type="button" class="w-full sm:w-auto btn-violet font-medium justify-center" wire:click="guardar" x-on:click="$dispatch('modal-close', { name: 'confirmar-actualizacion' })">
                    <span x-text="$store.i18n.t('btn.update') === 'btn.update' ? 'Actualizar' : $store.i18n.t('btn.update')">Actualizar</span>
                </button>
            </div>
        </div>
    </flux:modal>
    @endif


</div>
