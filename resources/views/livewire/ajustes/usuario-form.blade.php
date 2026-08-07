<div>
    <x-slot:title>{{ $usuarioId ? 'Editar Usuario' : 'Nuevo Usuario' }}</x-slot:title>

    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('usuarios.index') }}" variant="ghost" size="sm" icon="arrow-left" />
        <div>
            <flux:heading size="xl" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500">person</span>
                <span>{{ $usuarioId ? 'Editar Usuario' : 'Nuevo Usuario' }}</span>
            </flux:heading>
        </div>
    </div>

    <form wire:submit.prevent="guardar" class="space-y-6">
        {{-- ═══ Sección: Documento e identificación ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <span class="vc-section-title">Identificación</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                <div>
                    <flux:label>Tipo de Documento</flux:label>
                    <div class="mt-1">
                        <x-vc-dropdown
                            wire:model.live="tipo_documento"
                            :options="[
                                ['value' => 'DNI', 'label' => 'DNI'],
                                ['value' => 'RUC', 'label' => 'RUC'],
                                ['value' => 'CE', 'label' => 'Carnet Extranjería'],
                                ['value' => 'PASAPORTE', 'label' => 'Pasaporte'],
                            ]"
                            :selected="$tipo_documento"
                            placeholder="Seleccione"
                            icon="badge"
                        />
                    </div>
                </div>

                <div class="md:col-span-2">
                    <flux:label>Número de Documento</flux:label>
                    <div class="flex gap-2 mt-1">
                        <flux:input
                            wire:model="numero_documento"
                            wire:keydown.enter.prevent="consultarPeruApi"
                            placeholder="{{ $tipo_documento === 'DNI' ? '12345678' : '20123456789' }}"
                            :maxlength="$tipo_documento === 'RUC' ? 11 : 8"
                            icon="identification"
                            class="flex-1"
                        />
                        @if(in_array($tipo_documento, ['DNI', 'RUC']))
                            <button
                                type="button"
                                class="btn-primary justify-center"
                                wire:click="consultarPeruApi"
                                wire:loading.attr="disabled"
                                wire:target="consultarPeruApi"
                            >
                                <span wire:loading.remove wire:target="consultarPeruApi" class="flex items-center gap-2">
                                    <span class="material-symbols-outlined icon-sm">search</span>
                                    <span>Buscar</span>
                                </span>
                                <span wire:loading wire:target="consultarPeruApi" class="flex items-center gap-2">
                                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                                    <span>Buscando...</span>
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

        {{-- ═══ Sección: Datos personales ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">person</span>
                </div>
                <span class="vc-section-title">Datos Personales</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ $tipo_documento === 'RUC' ? 'Razón Social' : 'Nombres' }}</flux:label>
                    <flux:input wire:model="name" icon="user" />
                    <flux:error name="name" />
                </flux:field>

                @if($tipo_documento !== 'RUC')
                    <flux:field>
                        <flux:label>Apellidos</flux:label>
                        <flux:input wire:model="last_name" icon="users" />
                        <flux:error name="last_name" />
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label>Teléfono</flux:label>
                    <flux:input wire:model="phone" placeholder="987654321" icon="phone" />
                    <flux:error name="phone" />
                </flux:field>
            </div>
        </div>

        {{-- ═══ Sección: Credenciales y Rol ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">shield</span>
                </div>
                <span class="vc-section-title">Credenciales y Rol</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Correo Electrónico</flux:label>
                    <flux:input type="email" wire:model="email" icon="envelope" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>Contraseña {{ $usuarioId ? '(Opcional)' : '' }}</flux:label>
                    <flux:input type="password" wire:model="password" viewable />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>Rol</flux:label>
                    <div class="mt-1">
                        @php
                            $roleOptions = [];
                            foreach($rolesDisponibles ?? [] as $rolDisponible) {
                                $roleOptions[] = [
                                    'value' => $rolDisponible->name,
                                    'label' => ucfirst(str_replace('_', ' ', $rolDisponible->name))
                                ];
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model="rol"
                            :options="$roleOptions"
                            :selected="$rol"
                            placeholder="Seleccionar rol"
                            icon="shield"
                        />
                    </div>
                    <flux:error name="rol" />
                </flux:field>
            </div>
        </div>

        {{-- ═══ Sección: Ubicación ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">location_on</span>
                </div>
                <span class="vc-section-title">Ubicación</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <flux:field>
                    <flux:label>País</flux:label>
                    <div>
                        @php
                            $countryOptions = [];
                            $locationService = app(\App\Services\LocationService::class);
                            $countries = $locationService->getCountries();
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
                            placeholder="Seleccione"
                            icon="public"
                        />
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label>Estado / Región</flux:label>
                    <div>
                        @php
                            $stateOptions = [];
                            if ($country) {
                                $states = $locationService->getStates($country);
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
                            placeholder="Seleccione"
                            :disabled="!$country"
                            icon="map"
                        />
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label>Ciudad</flux:label>
                    <div>
                        @php
                            $cityOptions = [];
                            if ($country && $state) {
                                $cities = $locationService->getCities($country, $state);
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
                            placeholder="Seleccione"
                            :disabled="!$state"
                            icon="location_city"
                        />
                    </div>
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Dirección</flux:label>
                <flux:input wire:model="address" placeholder="Av. Principal 123" icon="map-pin" />
                <flux:error name="address" />
            </flux:field>
        </div>

        {{-- ═══ Notas ═══ --}}
        <div class="vc-panel">
            <flux:field>
                <flux:label>Notas adicionales</flux:label>
                <flux:textarea wire:model="notes" rows="3" placeholder="Observaciones sobre el usuario..." />
                <flux:error name="notes" />
            </flux:field>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <flux:button href="{{ route('usuarios.index') }}" variant="ghost" class="w-full sm:w-auto">
                Cancelar
            </flux:button>
            <button type="submit" class="w-full sm:w-auto {{ $usuarioId ? 'btn-violet' : 'btn-primary' }} justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm">{{ $usuarioId ? 'edit' : 'save' }}</span>
                    <span>{{ $usuarioId ? 'Actualizar' : 'Registrar' }}</span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    Guardando...
                </span>
            </button>
        </div>
    </form>
</div>
