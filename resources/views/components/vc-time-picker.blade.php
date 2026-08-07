@props([
    'placeholder' => 'Seleccionar hora...',
    'icon' => 'schedule',
    'startHour' => 7,
    'endHour' => 21,
    'interval' => 30,
])

<div
    x-data="{
        open: false,
        value: @entangle($attributes->wire('model')),
        get formattedValue() {
            if (!this.value) return '';
            const parts = this.value.split(':');
            if (parts.length < 2) return this.value;
            let h = parseInt(parts[0], 10);
            const m = parts[1];
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            h = h ? h : 12;
            return String(h).padStart(2, '0') + ':' + m + ' ' + ampm;
        },
        select(time) {
            this.value = time;
            this.open = false;
        },
        clear() {
            this.value = '';
            this.open = false;
        }
    }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="relative w-full"
>
    {{-- Trigger Input --}}
    <div class="relative w-full" @click="open = !open">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="color: var(--vc-text-muted);">
                <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
            </div>
        @endif

        <input
            type="text"
            readonly
            :value="formattedValue"
            class="vc-dropdown-trigger w-full focus-visible:outline-none cursor-pointer"
            placeholder="{{ $placeholder }}"
            style="padding-right: 2.5rem; padding-left: {{ $icon ? '3rem' : '1rem' }};"
            {{ $attributes->whereDoesntStartWith('wire:model') }}
        />

        {{-- Botón limpiar --}}
        <button
            type="button"
            x-show="value"
            @click.stop="clear()"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
        >
            <span class="material-symbols-outlined text-[16px]">close</span>
        </button>
    </div>

    {{-- Time Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        x-cloak
        class="absolute z-50 mt-1 w-48 bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl overflow-hidden"
    >
        {{-- Encabezado --}}
        <div class="px-4 py-2.5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-vc-surface">
            <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Hora</span>
        </div>

        {{-- Lista de horas --}}
        <div class="max-h-56 overflow-y-auto p-1.5 space-y-0.5">
            @for($h = $startHour; $h <= $endHour; $h++)
                @for($m = 0; $m < 60; $m += $interval)
                    @if($h < $endHour || $m === 0)
                        @php
                            $time = str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
                            $display = str_pad($h > 12 ? $h - 12 : ($h === 0 ? 12 : $h), 2, '0', STR_PAD_LEFT)
                                     . ':' . str_pad($m, 2, '0', STR_PAD_LEFT)
                                     . ' ' . ($h >= 12 ? 'PM' : 'AM');
                        @endphp
                        <button
                            type="button"
                            @click="select('{{ $time }}')"
                            class="w-full px-3 py-2 text-sm font-medium rounded-lg text-left transition-colors flex items-center justify-between"
                            :class="value === '{{ $time }}'
                                ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-vc-surface-alt'"
                        >
                            <span>{{ $display }}</span>
                            <span x-show="value === '{{ $time }}'" class="material-symbols-outlined text-emerald-500 text-[16px]">check</span>
                        </button>
                    @endif
                @endfor
            @endfor
        </div>
    </div>
</div>
