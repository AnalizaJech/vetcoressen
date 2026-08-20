<div x-data>
    <x-slot:title>{{ $usuarioId ? 'Editar Usuario' : 'Nuevo Usuario' }}</x-slot:title>

    {{-- ═══ Header de Formulario de Usuario (Estándar Premium) ═══ --}}
    <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('usuarios.index') }}" wire:navigate class="w-10 h-10 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-600 dark:text-zinc-400 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <span class="material-symbols-outlined text-2xl">person</span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                    @if($usuarioId)
                        <span x-text="$store.i18n.t('title.editar_usuario') || 'Editar Usuario'">Editar Usuario</span>
                    @else
                        <span x-text="$store.i18n.t('title.nuevo_usuario') || 'Nuevo Usuario'">Nuevo Usuario</span>
                    @endif
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('settings.usersSub') || 'Complete los datos personales y credenciales de acceso'">
                    Complete los datos personales y credenciales de acceso
                </p>
            </div>
        </div>
    </div>

    <form x-on:submit.prevent="{{ $usuarioId ? '$dispatch(\'modal-show\', { name: \'confirmar-actualizacion\' })' : '$wire.guardar()' }}" class="space-y-6">
        {{-- ═══ Sección: Documento e identificación ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.identification') || 'Identificación'">Identificación</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                <div>
                    <flux:label><span x-text="$store.i18n.t('form.docType') || 'Tipo de Documento'">Tipo de Documento</span></flux:label>
                    <div class="mt-1">
                        <x-vc-dropdown
                            wire:model.live="tipo_documento"
                            :options="[
                                ['value' => 'DNI', 'label' => 'DNI'],
                                ['value' => 'RUC', 'label' => 'RUC'],
                                ['value' => 'CE', 'label' => 'document.foreignId'],
                                ['value' => 'PASAPORTE', 'label' => 'document.passport'],
                            ]"
                            :selected="$tipo_documento"
                            placeholder="form.select"
                            icon="badge"
                        />
                    </div>
                </div>

                <div class="md:col-span-2">
                    <flux:label><span x-text="$store.i18n.t('form.docNumber') || 'Número de Documento'">Número de Documento</span></flux:label>
                    <div class="flex gap-2 mt-1">
                        <flux:input
                            wire:model="numero_documento"
                            wire:keydown.enter.prevent="consultarPeruApi"
                            placeholder="{{ $tipo_documento === 'DNI' ? '12345678' : '20123456789' }}"
                            :maxlength="$tipo_documento === 'RUC' ? 11 : 8"
                            class="flex-1"
                        >
                            <x-slot:iconLeading>
                                <span class="material-symbols-outlined text-[18px]">badge</span>
                            </x-slot:iconLeading>
                        </flux:input>
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
                                    <span x-text="$store.i18n.t('btn.search') || 'Buscar'">Buscar</span>
                                </span>
                                <span wire:loading wire:target="consultarPeruApi" class="flex items-center gap-2">
                                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                                    <span x-text="$store.i18n.t('btn.searching') || 'Buscando...'">Buscando...</span>
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
                <span class="vc-section-title" x-text="$store.i18n.t('form.personalData') || 'Datos Personales'">Datos Personales</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label><span x-text="$wire.tipo_documento === 'RUC' ? ($store.i18n.t('form.businessName') || 'Razón Social') : ($store.i18n.t('form.firstName') || 'Nombres')">Nombres</span> <span class="text-red-500">*</span></flux:label>
                    <flux:input wire:model="name">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">person</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="name" />
                </flux:field>

                @if($tipo_documento !== 'RUC')
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.lastName') || 'Apellidos'">Apellidos</span></flux:label>
                    <flux:input wire:model="last_name">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">group</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="last_name" />
                </flux:field>
                @endif

                <flux:field>
                    <flux:label x-text="$store.i18n.t('form.phoneLabel') || 'Teléfono'">Teléfono</flux:label>
                    <flux:input wire:model="phone" placeholder="987654321">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">phone</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="phone" />
                </flux:field>
            </div>
        </div>

        {{-- ═══ Sección: Credenciales y Rol ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">lock</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.credentialsAndRole') || 'Credenciales y Rol'">Credenciales y Rol</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.emailLabel') || 'Correo Electrónico'">Correo Electrónico</span> <span class="text-red-500">*</span></flux:label>
                    <flux:input wire:model="email" type="email" placeholder="usuario@clinica.com">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">mail</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.assignedBranch') || 'Sucursal Asignada'">Sucursal Asignada</span> <span class="text-red-500">*</span></flux:label>
                    <div>
                        @php
                            $branchOptions = [];
                            foreach ($sucursales as $s) {
                                $branchOptions[] = ['value' => (string) $s->id, 'label' => $s->name];
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model="branch_id"
                            :options="$branchOptions"
                            :selected="$branch_id"
                            placeholder="form.select"
                            icon="store"
                        />
                    </div>
                    <flux:error name="branch_id" />
                </flux:field>

                <flux:field>
                    <flux:label>
                        <span x-text="$store.i18n.t('form.password') || 'Contraseña'">Contraseña</span>
                        @if($usuarioId)
                            <span class="text-xs text-zinc-400 font-normal">(<span x-text="$store.i18n.t('form.optional') || 'Opcional'">Opcional</span>)</span>
                        @else
                            <span class="text-red-500">*</span>
                        @endif
                    </flux:label>
                    <flux:input wire:model="password" type="password" placeholder="••••••••">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">key</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.passwordConfirmation') || 'Confirmar Contraseña'">Confirmar Contraseña</span></flux:label>
                    <flux:input wire:model="password_confirmation" type="password" placeholder="••••••••">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">key</span>
                        </x-slot:iconLeading>
                    </flux:input>
                </flux:field>

                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('form.role') || 'Rol'">Rol</span> <span class="text-red-500">*</span></flux:label>
                    <div>
                        @php
                            $roleOptions = [];
                            foreach ($roles as $r) {
                                $roleOptions[] = [
                                    'value' => $r->name,
                                    'label' => $r->name === 'super_admin' ? 'Super Administrador' : ($r->name === 'veterinario' ? 'Veterinario' : ($r->name === 'recepcionista' ? 'Recepcionista' : ($r->name === 'cajero' ? 'Cajero' : ucfirst($r->name))))
                                ];
                            }
                        @endphp
                        <x-vc-dropdown
                            wire:model="rol"
                            :options="$roleOptions"
                            :selected="$rol"
                            placeholder="form.select"
                            icon="shield"
                        />
                    </div>
                    <flux:error name="rol" />
                </flux:field>

                @if($rol === 'veterinario')
                <flux:field>
                    <flux:label>Colegiatura (CMVP)</flux:label>
                    <flux:input wire:model="cmvp" placeholder="Ej: 12345">
                        <x-slot:iconLeading>
                            <span class="material-symbols-outlined text-[18px]">badge</span>
                        </x-slot:iconLeading>
                    </flux:input>
                    <flux:error name="cmvp" />
                </flux:field>
                @endif
            </div>
        </div>

        {{-- ═══ Sección: Ubicación ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">location_on</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('form.addressLabel') || 'Ubicación'">Ubicación</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <flux:field>
                    <flux:label x-text="$store.i18n.t('form.country') || 'País'">País</flux:label>
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
                            placeholder="form.select"
                            icon="public"
                        />
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label x-text="$store.i18n.t('form.stateLabel') || 'Departamento / Estado'">Departamento / Estado</flux:label>
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
                            placeholder="form.select"
                            :disabled="!$country"
                            icon="map"
                        />
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label x-text="$store.i18n.t('form.city') || 'Ciudad / Distrito'">Ciudad / Distrito</flux:label>
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
                            placeholder="form.select"
                            :disabled="!$state"
                            icon="location_city"
                        />
                    </div>
                </flux:field>
            </div>

            <flux:field>
                <flux:label x-text="$store.i18n.t('form.addressLabel') || 'Dirección'">Dirección</flux:label>
                <flux:input wire:model="address" x-bind:placeholder="$store.i18n.t('placeholder.addressExample') || 'Av. Principal 123'">
                    <x-slot:iconLeading>
                        <span class="material-symbols-outlined text-[18px]">location_on</span>
                    </x-slot:iconLeading>
                </flux:input>
                <flux:error name="address" />
            </flux:field>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <flux:button href="{{ route('usuarios.index') }}" wire:navigate variant="ghost" class="w-full sm:w-auto">
                <span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'">Cancelar</span>
            </flux:button>
            <button type="submit" class="w-full sm:w-auto {{ $usuarioId ? 'btn-violet' : 'btn-primary' }} justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm">{{ $usuarioId ? 'edit' : 'save' }}</span>
                    <span x-text="$store.i18n.t('{{ $usuarioId ? 'btn.update' : 'btn.register' }}') || '{{ $usuarioId ? 'Actualizar' : 'Registrar' }}'">{{ $usuarioId ? 'Actualizar' : 'Registrar' }}</span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    <span x-text="$store.i18n.t('btn.saving') || 'Guardando...'">Guardando...</span>
                </span>
            </button>
        </div>
    </form>

    {{-- Modal de Confirmación de Actualización --}}
    @if($usuarioId)
    <flux:modal name="confirmar-actualizacion" class="min-w-[22rem]">
        <div class="p-4">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center border border-blue-200 dark:border-blue-500/30">
                    <span class="material-symbols-outlined text-[32px]">info</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.confirmUpdate') || 'Confirmar Actualización'">Confirmar Actualización</h2>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto" x-text="$store.i18n.t('modal.confirmUpdateMsg') || '¿Está seguro de que desea guardar los cambios realizados?'">¿Está seguro de que desea guardar los cambios realizados?</p>
                </div>
            </div>
            <div class="flex gap-3 w-full mt-6">
                <flux:modal.close class="flex-1">
                    <flux:button variant="ghost" class="w-full"><span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'">Cancelar</span></flux:button>
                </flux:modal.close>
                <button type="button" wire:click="guardar" class="btn-primary flex-1 flex justify-center items-center gap-2" x-on:click="$dispatch('modal-close', { name: 'confirmar-actualizacion' })">
                    <span class="material-symbols-outlined icon-sm">save</span>
                    <span x-text="$store.i18n.t('btn.update') || 'Actualizar'">Actualizar</span>
                </button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
