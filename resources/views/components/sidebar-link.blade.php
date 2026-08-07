{{-- Componente de enlace del sidebar con estado activo --}}
@props(['href', 'active' => false])

<a
    href="{{ $href }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
        {{ $active
            ? 'active'
            : ''
        }}"
    style="
        @if($active)
            background-color: var(--vc-emerald-glow);
            color: var(--vc-emerald-light);
            border-left: 2px solid var(--vc-emerald);
        @else
            color: var(--vc-text-muted);
        @endif
    "
>
    <span class="shrink-0" style="color: {{ $active ? 'var(--vc-emerald-light)' : 'var(--vc-text-muted)' }};">
        {{ $icon }}
    </span>
    <span x-show="sidebarOpen" x-transition>
        {{ $slot }}
    </span>
</a>

<style>
    a:hover {
        @if(!$active)
            color: var(--vc-text) !important;
            background-color: var(--vc-surface-elevated) !important;
        @endif
    }
</style>
