<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 max-w-7xl">
    {{-- Card: Mi Perfil --}}
    <div class="bg-zinc-50/50 dark:bg-vc-surface-alt/20 border border-zinc-200/80 dark:border-zinc-700/50 rounded-3xl p-6 md:p-8 transition-colors hover:bg-zinc-50 dark:hover:bg-vc-surface-alt/40">
        <div class="vc-section-header mb-6">
            <div class="vc-section-icon bg-emerald-100/50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-xl p-2 flex items-center justify-center">
                <span class="material-symbols-outlined">person</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100" x-text="$store.i18n.t('settings.profile') || 'Mi Perfil'"></h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('settings.profileDesc') || 'Actualiza tu información personal'"></p>
            </div>
        </div>

        @if(session('perfil_mensaje'))
            <flux:callout variant="success" icon="check-circle" class="mb-6" dismissible>
                {{ session('perfil_mensaje') }}
            </flux:callout>
        @endif

        <form wire:submit.prevent="actualizarPerfil" class="space-y-5">
            <flux:field>
                <flux:label><span x-text="$store.i18n.t('form.nameLabel') || 'Nombre'"></span></flux:label>
                <flux:input wire:model="name" placeholder="Tu nombre completo" icon="user" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label><span x-text="$store.i18n.t('form.emailLabel') || 'Correo Electrónico'"></span></flux:label>
                <flux:input type="email" wire:model="email" placeholder="tucorreo@ejemplo.com" icon="envelope" />
                <flux:error name="email" />
            </flux:field>

            <div class="pt-2">
                <button type="submit" class="w-full sm:w-auto btn-primary justify-center" wire:loading.attr="disabled">
                    <span wire:loading.remove class="flex items-center gap-2">
                        <span class="material-symbols-outlined icon-sm">person_check</span>
                        <span x-text="$store.i18n.t('btn.updateProfile') || 'Actualizar Perfil'"></span>
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                        <span x-text="$store.i18n.t('btn.updating') || 'Actualizando...'"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- Card: Cambiar Contraseña --}}
    <div class="bg-zinc-50/50 dark:bg-vc-surface-alt/20 border border-zinc-200/80 dark:border-zinc-700/50 rounded-3xl p-6 md:p-8 transition-colors hover:bg-zinc-50 dark:hover:bg-vc-surface-alt/40">
        <div class="vc-section-header mb-6">
            <div class="vc-section-icon bg-emerald-100/50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-xl p-2 flex items-center justify-center">
                <span class="material-symbols-outlined">lock</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100" x-text="$store.i18n.t('settings.changePassword') || 'Cambiar Contraseña'"></h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400" x-text="$store.i18n.t('settings.changePasswordDesc') || 'Asegura tu cuenta con una nueva clave'"></p>
            </div>
        </div>

        @if(session('password_mensaje'))
            <flux:callout variant="success" icon="check-circle" class="mb-6" dismissible>
                {{ session('password_mensaje') }}
            </flux:callout>
        @endif

        <form wire:submit.prevent="actualizarPassword" class="space-y-5">
            <flux:field>
                <flux:label><span x-text="$store.i18n.t('form.currentPassword') || 'Contraseña Actual'"></span></flux:label>
                <flux:input type="password" wire:model="current_password" viewable placeholder="••••••••" icon="lock-closed" />
                <flux:error name="current_password" />
            </flux:field>

            <flux:field>
                <flux:label><span x-text="$store.i18n.t('form.newPassword') || 'Nueva Contraseña'"></span></flux:label>
                <flux:input type="password" wire:model="password" viewable placeholder="Mínimo 8 caracteres" icon="key" />
                <flux:error name="password" />
            </flux:field>

            <flux:field>
                <flux:label><span x-text="$store.i18n.t('form.confirmPassword') || 'Confirmar Nueva Contraseña'"></span></flux:label>
                <flux:input type="password" wire:model="password_confirmation" viewable placeholder="Repite la nueva contraseña" icon="key" />
                <flux:error name="password_confirmation" />
            </flux:field>

            <div class="pt-2">
                <button type="submit" class="w-full sm:w-auto btn-primary justify-center" wire:loading.attr="disabled">
                    <span wire:loading.remove class="flex items-center gap-2">
                        <span class="material-symbols-outlined icon-sm">key</span>
                        <span x-text="$store.i18n.t('btn.changePassword') || 'Cambiar Contraseña'"></span>
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                        <span x-text="$store.i18n.t('btn.changing') || 'Cambiando...'"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
