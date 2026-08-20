<div>
    <x-slot:title>{{ $roleId ? 'Editar Rol' : 'Nuevo Rol' }}</x-slot:title>

    {{-- ═══ Header de Formulario de Rol (Estándar Premium) ═══ --}}
    <div class="vc-panel flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('roles.index') }}" wire:navigate class="w-10 h-10 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-600 dark:text-zinc-400 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <span class="material-symbols-outlined text-2xl">shield</span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 font-display">
                    @if($roleId)
                        <span x-text="$store.i18n.t('title.editar_rol') || 'Editar Rol'">Editar Rol</span>
                    @else
                        <span x-text="$store.i18n.t('title.nuevo_rol') || 'Nuevo Rol'">Nuevo Rol</span>
                    @endif
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('settings.rolesSub') || 'Defina los permisos y accesos para este perfil de usuario'">
                    Defina los permisos y accesos para este perfil de usuario
                </p>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="guardar" class="space-y-6">
        {{-- ═══ Sección: Información del Rol ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">shield</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('role.info') || 'Información del Rol'">Información del Rol</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label><span x-text="$store.i18n.t('table.roleName') || 'Nombre del Rol'">Nombre del Rol</span></flux:label>
                    <flux:input wire:model="name" x-bind:placeholder="$store.i18n.t('role.namePlaceholder') || 'Ej: Recepcionista, Asistente, etc.'" icon="shield-check" />
                    <flux:error name="name" />
                </flux:field>
            </div>
        </div>

        {{-- ═══ Sección: Permisos agrupados por módulo ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">vpn_key</span>
                </div>
                <span class="vc-section-title" x-text="$store.i18n.t('role.permissionsAssignment') || 'Asignación de Permisos'">Asignación de Permisos</span>
            </div>
            
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6" x-text="$store.i18n.t('role.permissionsSubtitle') || 'Selecciona los permisos que tendrá este rol. Están agrupados por módulo del sistema.'">
                Selecciona los permisos que tendrá este rol. Están agrupados por módulo del sistema.
            </p>

            @if(empty($groupedPermissions))
                <div class="col-span-full py-4 text-center text-zinc-500 text-sm bg-zinc-50 dark:bg-vc-surface-alt/50 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-700" x-text="$store.i18n.t('role.noPermissions') || 'No hay permisos registrados en el sistema.'">
                    No hay permisos registrados en el sistema.
                </div>
            @else
                <div class="space-y-6">
                    @foreach($groupedPermissions as $module => $perms)
                        <div class="bg-zinc-50/50 dark:bg-vc-surface-alt/30 rounded-2xl border border-zinc-200/50 dark:border-zinc-700/30 overflow-hidden">
                            {{-- Encabezado del módulo --}}
                            <div class="flex items-center justify-between px-5 py-3 bg-zinc-100/50 dark:bg-vc-surface-alt/50 border-b border-zinc-200/50 dark:border-zinc-700/30">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-vc-primary/10 border border-vc-primary/20 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-vc-primary text-base">{{ $moduleIcons[$module] ?? 'extension' }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-zinc-800 dark:text-zinc-100 uppercase tracking-wider">
                                        <span x-text="$store.i18n.t('sidebar.{{ strtolower(str_replace(' ', '_', $module)) }}') || '{{ $module }}'">{{ $module }}</span>
                                    </span>
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500 font-medium">({{ count($perms) }})</span>
                                </div>
                                {{-- Botón marcar/desmarcar todos del módulo --}}
                                @php
                                    $modulePermNames = array_column($perms, 'name');
                                    $allSelected = count(array_intersect($modulePermNames, $selectedPermissions)) === count($modulePermNames);
                                @endphp
                                <button type="button" 
                                    wire:click="$set('selectedPermissions', {{ json_encode(
                                        $allSelected 
                                            ? array_values(array_diff($selectedPermissions, $modulePermNames))
                                            : array_values(array_unique(array_merge($selectedPermissions, $modulePermNames)))
                                    ) }})"
                                    class="text-xs font-medium px-3 py-1 rounded-lg transition-colors
                                        {{ $allSelected 
                                            ? 'bg-vc-primary/20 text-vc-primary-light border border-vc-primary/30' 
                                            : 'bg-zinc-200/50 dark:bg-zinc-700/50 text-zinc-500 dark:text-zinc-400 border border-zinc-300/50 dark:border-zinc-600/50 hover:bg-vc-primary/10 hover:text-vc-primary' 
                                        }}"
                                >
                                    @if($allSelected)
                                        <span x-text="$store.i18n.t('btn.uncheckAll') || 'Desmarcar todos'">Desmarcar todos</span>
                                    @else
                                        <span x-text="$store.i18n.t('btn.checkAll') || 'Marcar todos'">Marcar todos</span>
                                    @endif
                                </button>
                            </div>

                            {{-- Checkboxes de permisos --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2 p-4">
                                @foreach($perms as $perm)
                                    <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all duration-200
                                        {{ in_array($perm['name'], $selectedPermissions)
                                            ? 'bg-vc-primary/10 border border-vc-primary/30 shadow-sm' 
                                            : 'bg-white/50 dark:bg-vc-surface-alt/30 border border-transparent hover:border-zinc-300 dark:hover:border-zinc-600 hover:bg-zinc-50 dark:hover:bg-vc-surface-alt/50'
                                        }}"
                                    >
                                        <flux:checkbox wire:model="selectedPermissions" value="{{ $perm['name'] }}" />
                                        <span class="text-sm font-medium leading-tight
                                            {{ in_array($perm['name'], $selectedPermissions)
                                                ? 'text-vc-primary dark:text-vc-primary-light' 
                                                : 'text-zinc-700 dark:text-zinc-300'
                                            }}"
                                            x-text="$store.i18n.t('permissions.{{ $perm['name'] }}') || '{{ $perm['label'] }}'"
                                        >{{ $perm['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <flux:error name="selectedPermissions" />
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <flux:button href="{{ route('roles.index') }}" variant="ghost" class="w-full sm:w-auto">
                <span x-text="$store.i18n.t('btn.cancel') || 'Cancelar'">Cancelar</span>
            </flux:button>
            <button type="submit" class="w-full sm:w-auto {{ $roleId ? 'btn-violet' : 'btn-primary' }} justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm">{{ $roleId ? 'edit' : 'save' }}</span>
                    @if($roleId)
                        <span x-text="$store.i18n.t('btn.update') || 'Actualizar'">Actualizar</span>
                    @else
                        <span x-text="$store.i18n.t('btn.register') || 'Registrar'">Registrar</span>
                    @endif
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    <span x-text="$store.i18n.t('btn.saving') || 'Guardando...'">Guardando...</span>
                </span>
            </button>
        </div>
    </form>
</div>
