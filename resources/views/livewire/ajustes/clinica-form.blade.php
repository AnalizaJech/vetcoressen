<div>
    <div class="mb-6">
        <div class="vc-section-header">
            <div class="vc-section-icon">
                <span class="material-symbols-outlined">storefront</span>
            </div>
            <span class="vc-section-title" x-text="$store.i18n.t('settings.clinic') || 'Clínica'"></span>
        </div>

        @if(session('clinica_mensaje'))
            <flux:callout variant="success" icon="check-circle" class="mb-4" dismissible>
                {{ session('clinica_mensaje') }}
            </flux:callout>
        @endif

        <form wire:submit.prevent="actualizarClinica" class="space-y-4 max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('settings.clinicName') || 'Nombre de la Clínica'"></span></flux:label>
                    <flux:input wire:model="name" icon="building-storefront" />
                    <flux:error name="nombre" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('settings.ruc') || 'RUC'"></span></flux:label>
                    <flux:input wire:model="ruc" icon="identification" />
                    <flux:error name="ruc" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('settings.corporateName') || 'Razón Social'"></span></flux:label>
                    <flux:input wire:model="razon_social" icon="briefcase" />
                    <flux:error name="razon_social" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('settings.website') || 'Sitio Web'"></span></flux:label>
                    <flux:input type="url" wire:model="sitio_web" icon="globe-alt" />
                    <flux:error name="sitio_web" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.phoneLabel') || 'Teléfono'"></span></flux:label>
                    <flux:input wire:model="phone" icon="phone" />
                    <flux:error name="telefono" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.emailLabel') || 'Correo Electrónico'"></span></flux:label>
                    <flux:input type="email" wire:model="email" icon="envelope" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field class="md:col-span-2">
                    <flux:label><span x-text="$store.i18n.t('form.addressLabel') || 'Dirección'"></span></flux:label>
                    <flux:input wire:model="address" icon="map-pin" />
                    <flux:error name="direccion" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('settings.mainCurrency') || 'Moneda Principal'"></span></flux:label>
                    <x-vc-dropdown
                        wire:model="moneda_principal"
                        :options="[
                            ['value' => 'PEN', 'label' => 'PEN - Sol Peruano'],
                            ['value' => 'USD', 'label' => 'USD - Dólar Estadounidense']
                        ]"
                        :selected="$moneda_principal"
                        placeholder="Seleccionar moneda"
                    />
                    <flux:error name="moneda_principal" />
                </flux:field>
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full sm:w-auto btn-primary justify-center" wire:loading.attr="disabled">
                    <span wire:loading.remove class="flex items-center gap-2">
                        <span class="material-symbols-outlined icon-sm">storefront</span>
                        <span x-text="$store.i18n.t('btn.saveChanges') || 'Guardar Cambios'"></span>
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                        <span x-text="$store.i18n.t('btn.saving') || 'Guardando...'"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
