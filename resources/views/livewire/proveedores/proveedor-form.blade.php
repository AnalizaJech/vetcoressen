<div x-data>
    <x-slot:title>{{ $isEdit ? 'Editar Proveedor' : 'Nuevo Proveedor' }}</x-slot:title>

    <div class="animate-slide-up max-w-4xl mx-auto">
        {{-- Cabecera con botón volver --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('proveedores.index') }}" wire:navigate class="p-2 rounded-xl bg-white dark:bg-vc-surface-alt border border-zinc-200 dark:border-zinc-700 text-zinc-500 hover:text-vc-primary hover:border-vc-primary/30 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <flux:heading size="xl">
                        @if($isEdit)
                            <span x-text="$store.i18n.t('title.editar_proveedor') || 'Editar Proveedor'">Editar Proveedor</span>
                        @else
                            <span x-text="$store.i18n.t('title.nuevo_proveedor') || 'Nuevo Proveedor'">Nuevo Proveedor</span>
                        @endif
                    </flux:heading>
                    <flux:subheading>
                        @if($isEdit)
                            <span x-text="$store.i18n.t('title.editar_proveedor_sub') || 'Modifica los datos del proveedor.'">Modifica los datos del proveedor.</span>
                        @else
                            <span x-text="$store.i18n.t('title.nuevo_proveedor_sub') || 'Registra un nuevo proveedor o importador en el sistema.'">Registra un nuevo proveedor o importador en el sistema.</span>
                        @endif
                    </flux:subheading>
                </div>
            </div>
        </div>

        <form wire:submit="guardar" class="space-y-6">
            <div class="vc-panel">
                <div class="vc-section-header">
                    <div class="vc-section-icon">
                        <span class="material-symbols-outlined">domain</span>
                    </div>
                    <span class="vc-section-title" x-text="$store.i18n.t('form.basicInfo') || 'Información Básica'">Información Básica</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label><span x-text="$store.i18n.t('form.companyName') || 'Razón Social / Nombre Comercial'">Razón Social</span> <span class="text-red-500">*</span></flux:label>
                        <div class="mt-1">
                            <flux:input wire:model="name" x-bind:placeholder="$store.i18n.t('placeholder.company') || 'Ej: Distribuidora Veterinaria'">
                                <x-slot:iconLeading>
                                    <span class="material-symbols-outlined text-[18px]">domain</span>
                                </x-slot:iconLeading>
                            </flux:input>
                        </div>
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label><span x-text="$store.i18n.t('form.taxIdOptional') || 'RUC / ID Tributario (Opcional)'">RUC / ID Tributario (Opcional)</span></flux:label>
                        <div class="flex gap-2 mt-1">
                            <flux:input wire:model="ruc" x-bind:placeholder="$store.i18n.t('placeholder.taxId') || 'Ej: 20123456789'" class="flex-1">
                                <x-slot:iconLeading>
                                    <span class="material-symbols-outlined text-[18px]">badge</span>
                                </x-slot:iconLeading>
                            </flux:input>
                            <button
                                type="button"
                                class="btn-primary justify-center"
                                wire:click="consultarRuc"
                                wire:loading.attr="disabled"
                                wire:target="consultarRuc"
                            >
                                <span wire:loading.remove wire:target="consultarRuc" class="flex items-center gap-2">
                                    <span class="material-symbols-outlined icon-sm">search</span>
                                    <span x-text="$store.i18n.t('btn.search') || 'Buscar'">Buscar</span>
                                </span>
                                <span wire:loading wire:target="consultarRuc" class="flex items-center gap-2">
                                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                                    <span x-text="$store.i18n.t('btn.searching') || 'Buscando...'">Buscando...</span>
                                </span>
                            </button>
                        </div>
                        @if($peruApiError)
                            <p class="mt-1.5 text-xs text-red-500">{{ $peruApiError }}</p>
                        @endif
                        <flux:error name="ruc" />
                    </flux:field>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <flux:field>
                        <flux:label x-text="$store.i18n.t('form.country') || 'País'">País</flux:label>
                        <div class="mt-1">
                            @php
                                $locationService = app(\App\Services\LocationService::class);
                                $countries = $locationService->getCountries();
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
                        <flux:label x-text="$store.i18n.t('form.stateLabel') || 'Departamento / Estado'">Departamento / Estado</flux:label>
                        <div class="mt-1">
                            @php
                                $stateOptions = [];
                                if ($country) {
                                    $states = app(\App\Services\LocationService::class)->getStates($country);
                                    foreach ($states as $s) {
                                        if (isset($s['iso2']) && isset($s['name'])) {
                                            $stateOptions[] = ['value' => $s['iso2'], 'label' => $s['name']];
                                        }
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
                        <flux:label x-text="$store.i18n.t('form.city') || 'Ciudad / Distrito'">Ciudad / Distrito</flux:label>
                        <div class="mt-1">
                            @php
                                $cityOptions = [];
                                if ($country && $state) {
                                    $cities = app(\App\Services\LocationService::class)->getCities($country, $state);
                                    foreach ($cities as $ci) {
                                        if (isset($ci['name'])) {
                                            $cityOptions[] = ['value' => $ci['name'], 'label' => $ci['name']];
                                        }
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

                <div class="mt-4">
                    <flux:field>
                        <flux:label x-text="$store.i18n.t('form.addressLabel') || 'Dirección'">Dirección</flux:label>
                        <div class="mt-1">
                            <flux:input wire:model="address" x-bind:placeholder="$store.i18n.t('placeholder.addressExample') || 'Ej: Av. Principal 123, Distrito'">
                                <x-slot:iconLeading>
                                    <span class="material-symbols-outlined text-[18px]">location_on</span>
                                </x-slot:iconLeading>
                            </flux:input>
                        </div>
                        <flux:error name="address" />
                    </flux:field>
                </div>
            </div>

            <div class="vc-panel">
                <div class="vc-section-header">
                    <div class="vc-section-icon">
                        <span class="material-symbols-outlined">contact_phone</span>
                    </div>
                    <span class="vc-section-title" x-text="$store.i18n.t('form.contactInfo') || 'Información de Contacto'">Información de Contacto</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label><span x-text="$store.i18n.t('form.contactName') || 'Nombre de Contacto'">Nombre de Contacto</span> <span class="text-red-500">*</span></flux:label>
                        <div class="mt-1">
                            <flux:input wire:model="contact_name" x-bind:placeholder="$store.i18n.t('placeholder.contactName') || 'Ej: Juan Pérez'">
                                <x-slot:iconLeading>
                                    <span class="material-symbols-outlined text-[18px]">person</span>
                                </x-slot:iconLeading>
                            </flux:input>
                        </div>
                        <flux:error name="contact_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label x-text="$store.i18n.t('form.phoneLabel') || 'Teléfono'">Teléfono</flux:label>
                        <div class="mt-1">
                            <flux:input wire:model="phone" x-bind:placeholder="$store.i18n.t('placeholder.phoneExample') || 'Ej: +51 987 654 321'">
                                <x-slot:iconLeading>
                                    <span class="material-symbols-outlined text-[18px]">phone</span>
                                </x-slot:iconLeading>
                            </flux:input>
                        </div>
                        <flux:error name="phone" />
                    </flux:field>
                </div>

                <div class="mt-4">
                    <flux:field>
                        <flux:label x-text="$store.i18n.t('form.emailLabel') || 'Correo Electrónico'">Correo Electrónico</flux:label>
                        <div class="mt-1">
                            <flux:input wire:model="email" type="email" x-bind:placeholder="$store.i18n.t('placeholder.emailExample') || 'Ej: contacto@empresa.com'">
                                <x-slot:iconLeading>
                                    <span class="material-symbols-outlined text-[18px]">mail</span>
                                </x-slot:iconLeading>
                            </flux:input>
                        </div>
                        <flux:error name="email" />
                    </flux:field>
                </div>

                <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <div class="relative inline-flex items-center mt-0.5">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="vc-switch w-11 h-6 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100" x-text="$store.i18n.t('form.activeSupplier') || 'Proveedor activo'">Proveedor activo</span>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('form.inactiveSupplierHelp') || 'Los proveedores inactivos no estarán disponibles para compras.'">Los proveedores inactivos no estarán disponibles para compras.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <flux:button href="{{ route('proveedores.index') }}" wire:navigate variant="ghost"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'">Cancelar</span></flux:button>
                <button type="submit" class="w-full sm:w-auto {{ $isEdit ? 'btn-violet' : 'btn-primary' }} justify-center" wire:loading.attr="disabled">
                    <span wire:loading.remove class="flex items-center gap-2">
                        <span class="material-symbols-outlined icon-sm">{{ $isEdit ? 'edit' : 'save' }}</span>
                        <span x-text="$store.i18n.t('{{ $isEdit ? 'btn.update' : 'btn.register' }}') || '{{ $isEdit ? 'Actualizar' : 'Registrar' }}'">{{ $isEdit ? 'Actualizar' : 'Registrar' }}</span>
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                        <span x-text="$store.i18n.t('btn.saving') || 'Guardando...'">Guardando...</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
