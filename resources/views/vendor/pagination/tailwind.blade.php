@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex items-center justify-center">
        <ul class="flex items-center gap-2">
            {{-- Flecha anterior --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="w-10 h-10 flex items-center justify-center rounded-full text-zinc-300 dark:text-zinc-600 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                </li>
            @else
                <li>
                    <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="w-10 h-10 flex items-center justify-center rounded-full text-zinc-600 dark:text-zinc-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all duration-200" aria-label="Anterior">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                </li>
            @endif

            {{-- Números de página --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    {{-- Separador "..." --}}
                    <li>
                        <span class="w-10 h-10 flex items-center justify-center text-sm text-zinc-400 dark:text-zinc-500">…</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="w-10 h-10 flex items-center justify-center rounded-full text-sm font-bold bg-emerald-500 text-white shadow-sm shadow-emerald-500/30 ring-1 ring-emerald-400/50" aria-current="page">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-200">{{ $page }}</button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Flecha siguiente --}}
            @if ($paginator->hasMorePages())
                <li>
                    <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="w-10 h-10 flex items-center justify-center rounded-full text-zinc-600 dark:text-zinc-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all duration-200" aria-label="Siguiente">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </li>
            @else
                <li>
                    <span class="w-10 h-10 flex items-center justify-center rounded-full text-zinc-300 dark:text-zinc-600 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
