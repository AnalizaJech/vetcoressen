<div x-data>
    <x-slot:title x-text="$store.i18n.t('sidebar.users') || 'Usuarios'">Usuarios</x-slot:title>

    {{-- Cabecera con icono --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="kpi-icon kpi-icon--emerald">
                <span class="material-symbols-outlined">group</span>
            </div>
            <div>
                <flux:heading size="xl"><span x-text="$store.i18n.t('sidebar.users') || 'Usuarios'"></span></flux:heading>
                <flux:subheading><span x-text="$store.i18n.t('settings.usersSub') || 'Gestión de usuarios del sistema'"></span></flux:subheading>
            </div>
        </div>
        <div class="w-full sm:w-auto mt-2 sm:mt-0">
            <a href="{{ route('usuarios.crear') }}" class="w-full sm:w-auto btn-primary justify-center">
                <span class="material-symbols-outlined icon-sm">add</span>
                <span x-text="$store.i18n.t('btn.newUser') || 'Nuevo Usuario'"></span>
            </a>
        </div>
    </div>

<div class="animate-slide-up">
    @if(session('mensaje_error'))
        <div class="mb-4">
            <flux:callout variant="danger" icon="error" dismissible>
                {{ session('mensaje_error') }}
            </flux:callout>
        </div>
    @endif

    <x-vc-table-layout 
        :data="$usuarios"
        icon="group"
        emptyTitle="Sin usuarios"
        emptyTitleKey="empty.noUsers"
        emptyText="No hay usuarios que coincidan con los filtros."
        emptyTextKey="empty.noUsersSub"
        :searchable="true"
        searchModel="busqueda"
        x-bind:searchPlaceholder="$store.i18n.t('btn.search') || 'Buscar...'"
        searchPlaceholderKey="placeholder.searchUsers"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            @foreach($usuarios as $user)
                <div class="vc-card flex flex-col justify-between p-5 rounded-2xl bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow relative">
                    {{-- Avatar y Nombre --}}
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl">person</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $user->name }} {{ $user->last_name }}</h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                @if($user->hasRole('super_admin'))
                                    <span class="badge badge-purple">Super Admin</span>
                                @elseif($user->hasRole('veterinario'))
                                    <span class="badge badge-blue">Veterinario</span>
                                @else
                                    <span class="badge badge-zinc">Usuario</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Info Principal --}}
                    <div class="space-y-3 mb-6 flex-1">
                        <div>
                            <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.emailLabel') || 'Email'"></p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="material-symbols-outlined text-zinc-400 icon-sm">mail</span>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-300 truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold" x-text="$store.i18n.t('form.phoneLabel') || 'Teléfono'"></p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="material-symbols-outlined text-zinc-400 icon-sm">call</span>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-300 truncate">{{ $user->phone ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-1.5 items-center">
                        <button type="button" class="vc-btn-action vc-btn-view" data-vc-tooltip="Ver" x-bind:data-vc-tooltip="$store.i18n.t('btn.view') || 'Ver'" 
                            @click="$wire.ver({{ $user->id }}); $dispatch('modal-show', { name: 'ver-usuario' })">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                        <a href="{{ route('usuarios.editar', $user) }}" class="vc-btn-action vc-btn-edit" x-bind:data-vc-tooltip="$store.i18n.t('btn.edit') || 'Editar'">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        @if($user->id !== auth()->id())
                            <button type="button" class="vc-btn-action vc-btn-delete" data-vc-tooltip="Eliminar" x-bind:data-vc-tooltip="$store.i18n.t('btn.delete') || 'Eliminar'"
                                @click="$wire.set('usuarioEliminarId', {{ $user->id }}); $dispatch('modal-show', { name: 'confirmar-eliminar-usuario' })"
                            >
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $usuarios->links() }}
        </div>
    </x-vc-table-layout>
</div>

    {{-- Modal Ver Usuario --}}
    <flux:modal :closable="false" name="ver-usuario" class="w-[90vw] md:w-full max-w-2xl">
        @if($usuarioVer)
            <div class="space-y-6">
                {{-- Cabecera del Modal --}}
                <div class="flex items-start justify-between border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">person</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">{{ $usuarioVer->name }} {{ $usuarioVer->last_name }}</h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $usuarioVer->tipo_documento ?? 'Doc.' }}: {{ $usuarioVer->numero_documento ?? 'No registrado' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Contenido en Bento Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Tarjeta: Contacto --}}
                    <div class="bg-zinc-50 dark:bg-vc-surface-alt/50 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-emerald-500">contact_phone</span>
                            <span x-text="$store.i18n.t('profile.contact') || 'Contacto'">Contacto</span>
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-zinc-500 block" x-text="$store.i18n.t('form.phoneLabel') || 'Teléfono'">Teléfono</span>
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $usuarioVer->phone ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-500 block" x-text="$store.i18n.t('form.emailLabel') || 'Email'">Email</span>
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $usuarioVer->email }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta: Ubicación --}}
                    <div class="bg-zinc-50 dark:bg-vc-surface-alt/50 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-emerald-500">location_on</span>
                            <span x-text="$store.i18n.t('form.location') || 'Ubicación'">Ubicación</span>
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-zinc-500 block" x-text="$store.i18n.t('form.addressLabel') || 'Dirección'">Dirección</span>
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $usuarioVer->address ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-500 block" x-text="$store.i18n.t('profile.cityCountry') || 'Ciudad / País'">Ciudad / País</span>
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $usuarioVer->city ?? '-' }}, {{ $usuarioVer->country ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Tarjeta: Rol --}}
                    <div class="bg-zinc-50 dark:bg-vc-surface-alt/50 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-emerald-500">shield</span>
                            <span x-text="$store.i18n.t('table.roleName') || 'Rol'">Rol</span>
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-zinc-500 block" x-text="$store.i18n.t('profile.systemPermissions') || 'Permisos en el sistema'">Permisos en el sistema</span>
                                <div class="mt-1">
                                    @if($usuarioVer->hasRole('super_admin'))
                                        <span class="badge badge-purple">Super Admin</span>
                                    @elseif($usuarioVer->hasRole('veterinario'))
                                        <span class="badge badge-blue">Veterinario</span>
                                    @else
                                        <span class="badge badge-zinc">Usuario</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notas si existen --}}
                @if($usuarioVer->notes)
                    <div class="bg-amber-50 dark:bg-amber-500/10 p-4 rounded-xl border border-amber-100 dark:border-amber-500/20">
                        <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-500 mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">edit_note</span>
                            <span x-text="$store.i18n.t('form.additionalNotes') || 'Notas adicionales'">Notas adicionales</span>
                        </h3>
                        <p class="text-sm text-amber-800 dark:text-amber-400 whitespace-pre-line">{{ $usuarioVer->notes }}</p>
                    </div>
                @endif
                
                {{-- Botones de Acción Modal Ver --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:modal.close>
                        <button type="button" class="btn-primary bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 border-none px-4 py-2 font-medium flex items-center justify-center gap-2">Cerrar</button>
                    </flux:modal.close>
                </div>
            </div>
        @else
            <div class="py-8 flex justify-center">
                <span class="material-symbols-outlined animate-spin text-emerald-500 text-3xl">progress_activity</span>
            </div>
        @endif
    </flux:modal>

    {{-- Modal de confirmacion eliminar --}}
    <flux:modal :closable="false" name="confirmar-eliminar-usuario" class="w-[90vw] md:w-full max-w-md">
        <div class="space-y-6">
            <div class="flex flex-col items-center justify-center text-center space-y-5">
                <div class="w-20 h-20 bg-red-100/50 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center border border-red-200 dark:border-red-500/30 shadow-sm shadow-red-500/10">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1, 'wght' 700;">warning</span>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-zinc-900 dark:text-white">Eliminar Usuario</h2>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto leading-relaxed">Esta acción no se puede revertir y perderás toda la información de este usuario.</p>
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full mt-6">
                <flux:modal.close class="w-full sm:w-auto flex-1">
                    <flux:button variant="ghost" class="w-full text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white bg-zinc-100 hover:bg-zinc-200 dark:bg-vc-surface-alt dark:hover:bg-zinc-700 font-medium">
                        Cancelar
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="danger" class="w-full sm:w-auto flex-1 shadow-sm font-medium" wire:click="eliminar" x-on:click="$dispatch('modal-close', { name: 'confirmar-eliminar-usuario' })">
                    Sí, eliminar usuario
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

