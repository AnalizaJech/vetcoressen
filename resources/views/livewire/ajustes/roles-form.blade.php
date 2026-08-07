<div>
    <x-slot:title>{{ $roleId ? 'Editar Rol' : 'Nuevo Rol' }}</x-slot:title>

    <div class="flex items-center gap-3 mb-6">
        <flux:button href="{{ route('roles.index') }}" variant="ghost" size="sm" icon="arrow-left" />
        <div>
            <flux:heading size="xl" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500">shield_person</span>
                <span>{{ $roleId ? 'Editar Rol' : 'Nuevo Rol' }}</span>
            </flux:heading>
        </div>
    </div>

    <form wire:submit.prevent="guardar" class="space-y-6">
        {{-- ═══ Sección: Información del Rol ═══ --}}
        <div class="vc-panel">
            <div class="vc-section-header">
                <div class="vc-section-icon">
                    <span class="material-symbols-outlined">shield</span>
                </div>
                <span class="vc-section-title">Información del Rol</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Nombre del Rol</flux:label>
                    <flux:input wire:model="name" placeholder="Ej: Recepcionista, Asistente, etc." icon="identification" />
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
                <span class="vc-section-title">Asignación de Permisos</span>
            </div>
            
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">Selecciona los permisos que tendrá este rol. Están agrupados por módulo del sistema.</p>

            @if(empty($groupedPermissions))
                <div class="col-span-full py-4 text-center text-zinc-500 text-sm bg-zinc-50 dark:bg-vc-surface-alt/50 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-700">
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
                                    <span class="text-sm font-bold text-zinc-800 dark:text-zinc-100 uppercase tracking-wider">{{ $module }}</span>
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
                                    {{ $allSelected ? 'Desmarcar todos' : 'Marcar todos' }}
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
                Cancelar
            </flux:button>
            <button type="submit" class="w-full sm:w-auto {{ $roleId ? 'btn-violet' : 'btn-primary' }} justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm">{{ $roleId ? 'edit' : 'save' }}</span>
                    <span>{{ $roleId ? 'Actualizar' : 'Registrar' }}</span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    <span>Guardando...</span>
                </span>
            </button>
        </div>
    </form>
</div>
