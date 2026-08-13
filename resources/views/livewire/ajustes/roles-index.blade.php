<div x-data>
    <x-slot:title x-text="$store.i18n.t('settings.roles') || 'Roles y Permisos'"></x-slot:title>

    {{-- Cabecera con icono --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="kpi-icon kpi-icon--emerald">
                <span class="material-symbols-outlined">shield_person</span>
            </div>
            <div>
                <flux:heading size="xl"><span x-text="$store.i18n.t('settings.roles') || 'Roles y Permisos'"></span></flux:heading>
                <flux:subheading><span x-text="$store.i18n.t('settings.rolesSub') || 'Gestión de roles y control de accesos del sistema'"></span></flux:subheading>
            </div>
        </div>
        <div class="w-full sm:w-auto mt-2 sm:mt-0">
            <a href="{{ route('roles.crear') }}" class="w-full sm:w-auto btn-primary justify-center">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newRole') || 'Nuevo Rol'"></span>
            </a>
        </div>
    </div>

    @if(session('mensaje_error'))
        <div class="mb-4">
            <flux:callout variant="danger" icon="error" dismissible>
                {{ session('mensaje_error') }}
            </flux:callout>
        </div>
    @endif

    <x-vc-table-layout 
        :data="$roles"
        icon="security_update_warning"
        emptyTitle="Sin roles"
        emptyTitleKey="empty.noRoles"
        emptyText="No hay roles que coincidan con los filtros."
        emptyTextKey="empty.noRolesSub"
        searchModel="busqueda"
        searchPlaceholder="Buscar rol..."
        searchPlaceholderKey="placeholder.searchRoles"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            @foreach($roles as $role)
                <div class="vc-card flex flex-col justify-between p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow relative">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center font-bold text-emerald-500 shrink-0">
                            <span class="material-symbols-outlined icon-sm">shield</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-zinc-800 dark:text-zinc-100 capitalize">{{ str_replace('_', ' ', $role->name) }}</h3>
                            <p class="text-xs text-zinc-500 uppercase tracking-wider">{{ $role->permissions->count() }} <span x-text="$store.i18n.t('table.assignedPermissions') || 'Permisos'"></span></p>
                        </div>
                    </div>
                    
                    <div class="flex-1 mb-4 flex flex-wrap gap-1">
                        @if($role->permissions->count() > 0)
                            @foreach($role->permissions->take(6) as $permission)
                                <flux:badge size="sm" color="zinc"><span x-text="$store.i18n.t('permissions.{{ $permission->name }}') || '{{ $permission->name }}'"></span></flux:badge>
                            @endforeach
                            @if($role->permissions->count() > 6)
                                <flux:badge size="sm" color="sky">+{{ $role->permissions->count() - 6 }}</flux:badge>
                            @endif
                        @else
                            <span class="text-zinc-500 text-sm italic" x-text="$store.i18n.t('table.noSpecificPermissions') || 'Sin permisos específicos'"></span>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-1.5 items-center">
                        <a href="{{ route('roles.editar', $role->id) }}" class="vc-btn-action vc-btn-edit" x-bind:data-vc-tooltip="$store.i18n.t('btn.edit') || 'Editar'">
                            <span class="material-symbols-outlined icon-sm">edit</span>
                        </a>
                        @if($role->name !== 'super_admin')
                            <button type="button" class="vc-btn-action vc-btn-delete" x-bind:data-vc-tooltip="$store.i18n.t('btn.delete') || 'Eliminar'"
                                @click="$wire.set('roleEliminarId', {{ $role->id }}); $dispatch('modal-show', { name: 'confirmar-eliminar-rol' })">
                                <span class="material-symbols-outlined icon-sm">delete</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $roles->links() }}
        </div>
    </x-vc-table-layout>

    {{-- Modal de confirmacion eliminar --}}
    <flux:modal :closable="false" name="confirmar-eliminar-rol" class="min-w-88 overflow-y-auto max-h-[85vh]">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">warning</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white" x-text="$store.i18n.t('modal.deleteRole') || 'Eliminar Rol'"></h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed" x-text="$store.i18n.t('modal.deleteRoleMsg') || 'Esta acción no se puede revertir. Los usuarios con este rol podrían perder accesos importantes.'"></p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:modal.close class="w-full sm:w-auto flex-1">
                    <flux:button variant="ghost" class="w-full text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white bg-zinc-100 hover:bg-zinc-200 dark:bg-vc-surface-alt dark:hover:bg-zinc-700 font-medium">
                        <span x-text="$store.i18n.t('btn.cancel', 'Cancelar')"></span>
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="danger" class="w-full sm:w-auto flex-1 shadow-sm font-medium" wire:click="eliminar" x-on:click="$dispatch('modal-close', { name: 'confirmar-eliminar-rol' })">
                    <span x-text="$store.i18n.t('btn.deleteRole', 'Sí, eliminar rol')"></span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
