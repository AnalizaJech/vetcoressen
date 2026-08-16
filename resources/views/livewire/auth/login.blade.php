<div class="flex items-center justify-center w-full h-full px-4 py-8" x-data>
    <div class="w-full max-w-md animate-fade-in">
        @php
            $clinic = \App\Models\Clinic::first();
            $clinicName = $clinic->name ?? config('app.name');
            $clinicLogo = $clinic->logo ? asset('storage/' . $clinic->logo) : asset('favicon.svg');
        @endphp
        {{-- Logo y marca --}}
        <div class="text-center mb-8 md:mb-10 flex flex-col items-center justify-center">
            <img src="{{ $clinicLogo }}" alt="{{ $clinicName }} Logo" class="w-20 h-20 sm:w-28 sm:h-28 drop-shadow-md mb-4 object-contain" />
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight font-display" style="color: var(--vc-text);">{{ $clinicName }}</h1>
            <p class="text-sm md:text-base mt-2 font-medium" style="color: var(--vc-emerald-light);" x-text="$store.i18n.t('login.subtitle') || 'Gestión Veterinaria Premium'"></p>
        </div>

        {{-- Formulario de login --}}
        <form wire:submit="login" class="vc-login-card space-y-5">
            {{-- Email --}}
            <div class="space-y-1">
                <label for="email" class="block text-sm font-medium" style="color: var(--vc-text);" x-text="$store.i18n.t('login.email')">Correo electrónico</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px]" style="color: var(--vc-text-muted);">mail</span>
                    <input wire:model="email" id="email" type="email" :placeholder="$store.i18n.t('login.emailPlaceholder') || 'admin@vetcoressen.pe'" required autofocus class="vc-input w-full" style="padding-left: 2.75rem;" />
                </div>
                @error('email') <span class="text-xs text-red-500 font-medium mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Contraseña --}}
            <div class="space-y-1" x-data="{ show: false }">
                <label for="password" class="block text-sm font-medium" style="color: var(--vc-text);" x-text="$store.i18n.t('login.password')">Contraseña</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px]" style="color: var(--vc-text-muted);">lock</span>
                    <input wire:model="password" id="password" :type="show ? 'text' : 'password'" :placeholder="$store.i18n.t('login.passwordPlaceholder') || '••••••••'" required class="vc-input pr-10 w-full" style="padding-left: 2.75rem;" />
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 focus:outline-none" style="color: var(--vc-text-muted);">
                        <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                    </button>
                </div>
                @error('password') <span class="text-xs text-red-500 font-medium mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Recordar sesión y toggles --}}
            <div class="flex items-center justify-between pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="remember" id="remember" class="rounded border-zinc-300 dark:border-zinc-700 bg-white dark:bg-vc-surface text-emerald-600 focus:ring-emerald-600 dark:focus:ring-emerald-500 w-4 h-4 transition-colors" />
                    <span class="text-sm font-medium" style="color: var(--vc-text);" x-text="$store.i18n.t('login.remember') || 'Recordar sesión'">Recordar sesión</span>
                </label>

                <div class="flex items-center gap-2">
                    {{-- Tema --}}
                    <button type="button" @click="$store.theme.toggle()" class="flex items-center justify-center w-8 h-8 rounded-md transition-colors focus:outline-none" style="background: var(--vc-surface-elevated); color: var(--vc-emerald);" :>
                        <span class="material-symbols-outlined icon-md" x-text="$store.theme.isDark ? 'light_mode' : 'dark_mode'"></span>
                    </button>

                    {{-- Idioma --}}
                    <button type="button" @click="$store.i18n.toggle()" class="text-xs font-bold w-8 h-8 rounded-md transition-colors flex items-center justify-center focus:outline-none" style="background: var(--vc-emerald-glow); color: var(--vc-emerald-light);" x-text="$store.i18n.locale === 'en' ? 'ES' : 'EN'" :></button>
                </div>
            </div>

            {{-- Botón de login --}}
            <button
                type="submit"
                class="w-full btn-primary justify-center h-12 text-lg font-bold shadow-md hover:shadow-lg transition-all active:scale-[0.98]"
            >
                <span wire:loading.remove x-text="$store.i18n.t('login.submit')">Ingresar al sistema</span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined icon-sm vc-spinner">progress_activity</span>
                    <span x-text="$store.i18n.t('btn.verifying')">Verificando...</span>
                </span>
            </button>
        </form>

        {{-- Footer --}}
        <p class="text-center text-xs mt-6" style="color: var(--vc-text-muted);">
            &copy; {{ date('Y') }} <span x-text="$store.i18n.t('login.copyright')"></span>
        </p>
    </div>
</div>

