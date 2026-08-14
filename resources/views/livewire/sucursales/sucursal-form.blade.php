<div x-data>
    <x-slot:title>{{ $isEdit ? 'Editar Sucursal' : 'Nueva Sucursal' }}</x-slot:title>

    <div class="animate-slide-up max-w-4xl mx-auto">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('sucursales.index') }}" wire:navigate class="p-2 rounded-xl bg-white dark:bg-vc-surface-alt border border-zinc-200 dark:border-zinc-700 text-zinc-500 hover:text-vc-primary hover:border-vc-primary/30 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <flux:heading size="xl">
                        @if($isEdit)
                            <span x-text="$store.i18n.t('title.editar_sucursal') || 'Editar Sucursal'"></span>
                        @else
                            <span x-text="$store.i18n.t('title.nueva_sucursal') || 'Nueva Sucursal'"></span>
                        @endif
                    </flux:heading>
                    <flux:subheading>
                        @if($isEdit)
                            <span x-text="$store.i18n.t('title.editar_sucursal_sub') || 'Modifica los datos de la sede.'"></span>
                        @else
                            <span x-text="$store.i18n.t('title.nueva_sucursal_sub') || 'Registra un nuevo local o sede de la clínica.'"></span>
                        @endif
                    </flux:subheading>
                </div>
            </div>
        </div>

        <form wire:submit="guardar" class="space-y-6">
            <div class="vc-panel">
                <div class="vc-section-header">
                    <div class="vc-section-icon">
                        <span class="material-symbols-outlined">store</span>
                    </div>
                    <span class="vc-section-title">Información Principal</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Nombre de la Sucursal <span class="text-red-500">*</span></flux:label>
                        <div class="mt-1">
                            <flux:input wire:model="name" placeholder="Ej: Sede Miraflores">
                                <x-slot:iconLeading>
                                    <span class="material-symbols-outlined text-[18px]">store</span>
                                </x-slot:iconLeading>
                            </flux:input>
                        </div>
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>RUC o Identificación Tributaria (Opcional)</flux:label>
                        <div class="flex gap-2 mt-1">
                            <flux:input wire:model="ruc" placeholder="Ej: 20123456789" class="flex-1">
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
                                    <span>Buscar</span>
                                </span>
                                <span wire:loading wire:target="consultarRuc" class="flex items-center gap-2">
                                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                                    <span>Buscando...</span>
                                </span>
                            </button>
                        </div>
                        @if($peruApiError)
                            <p class="mt-1.5 text-xs text-red-500">{{ $peruApiError }}</p>
                        @endif
                        <flux:error name="ruc" />
                    </flux:field>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>País</flux:label>
                            <div class="mt-1" x-data="{ ph: 'Seleccionar...' }">
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
                                    x-bind:placeholder="ph"
                                    icon="public"
                                />
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>Estado / Región</flux:label>
                            <div class="mt-1" x-data="{ ph: 'Seleccionar...' }">
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
                                    x-bind:placeholder="ph"
                                    :disabled="!$country"
                                    icon="map"
                                />
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>Ciudad</flux:label>
                            <div class="mt-1" x-data="{ ph: 'Seleccionar...' }">
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
                                    x-bind:placeholder="ph"
                                    :disabled="!$state"
                                    icon="location_city"
                                />
                            </div>
                        </flux:field>
                    </div>

                    <flux:field class="mt-4">
                        <flux:label>Dirección</flux:label>
                        <div class="mt-1">
                            <flux:input wire:model="address" placeholder="Ej: Av. Larco 1234">
                                <x-slot:iconLeading>
                                    <span class="material-symbols-outlined text-[18px]">location_on</span>
                                </x-slot:iconLeading>
                            </flux:input>
                        </div>
                        <flux:error name="address" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <flux:field>
                        <flux:label>Teléfono</flux:label>
                        <div class="mt-1">
                            <flux:input wire:model="phone" placeholder="Ej: +51 987 654 321">
                                <x-slot:iconLeading>
                                    <span class="material-symbols-outlined text-[18px]">phone</span>
                                </x-slot:iconLeading>
                            </flux:input>
                        </div>
                        <flux:error name="phone" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Correo Electrónico</flux:label>
                        <div class="mt-1">
                            <flux:input wire:model="email" type="email" placeholder="Ej: miraflores@veterinaria.com">
                                <x-slot:iconLeading>
                                    <span class="material-symbols-outlined text-[18px]">mail</span>
                                </x-slot:iconLeading>
                            </flux:input>
                        </div>
                        <flux:error name="email" />
                    </flux:field>
                </div>
            </div>

            <div class="vc-panel">
                <div class="vc-section-header">
                    <div class="vc-section-icon">
                        <span class="material-symbols-outlined">settings</span>
                    </div>
                    <span class="vc-section-title">Configuración</span>
                </div>

                <div class="space-y-4">
                    <flux:checkbox 
                        wire:model="is_main" 
                        label="Establecer como Sucursal Principal" 
                        description="Esta sede será la principal para las operaciones del sistema. Si marcas esto, se desmarcará la anterior sede principal." 
                    />
                    
                    <flux:checkbox 
                        wire:model="is_active" 
                        label="Sucursal Activa" 
                        description="Las sucursales inactivas no estarán disponibles para asignar citas o registrar operaciones." 
                        x-bind:disabled="$wire.is_main"
                    />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <flux:button href="{{ route('sucursales.index') }}" wire:navigate variant="ghost">Cancelar</flux:button>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined icon-sm">save</span>
                    <span>{{ $isEdit ? 'Actualizar' : 'Guardar' }} Sucursal</span>
                </button>
            </div>
        </form>
    </div>
</div>
