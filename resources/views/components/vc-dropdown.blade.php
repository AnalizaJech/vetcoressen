{{-- Componente Custom Dropdown - Regla 13: nunca usar select nativo
     Usa div custom con opciones, muestra valor seleccionado, cierra al click fuera.
     Soporte: search, disabled, dark/light mode via CSS vars --}}

@props([
    'options' => [],
    'selected' => null,
    'placeholder' => 'form.select',
    'disabled' => false,
    'icon' => null,
    'allowCustom' => false,
])

@php
    // Encontrar el label de la opción seleccionada
    $selectedLabel = $placeholder;
    foreach ($options as $opt) {
        if ((string) ($opt['value'] ?? '') === (string) $selected) {
            $selectedLabel = $opt['label'] ?? $opt['value'];
            break;
        }
    }
    $hasValue = $selected !== null && $selected !== '';
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selectedLabel: '',
        get placeholderText() {
            let key = '{{ addslashes($placeholder) }}';
            return this.$store.i18n?.t(key) || key;
        },
        get filteredOptions() {
            let opts = JSON.parse($el.dataset.options || '[]');
            if (this.search === '' || this.search === this.selectedLabel) return opts.slice(0, 50);
            
            const term = (this.search || '').toString().toLowerCase();
            let result = opts.filter(opt => {
                let labelStr = String(this.$store.i18n?.t(opt.label) || opt.label || '');
                return labelStr.toLowerCase().includes(term);
            });
            return result.slice(0, 50);
        },
        init() {
            // Translate initial selected label if possible
            let initialRawLabel = '{{ addslashes($selectedLabel) }}';
            let initialPlaceholder = '{{ addslashes($placeholder) }}';
            
            if (initialRawLabel === initialPlaceholder) {
                this.selectedLabel = this.placeholderText;
                this.search = '';
            } else {
                this.selectedLabel = this.$store.i18n?.t(initialRawLabel) || initialRawLabel;
                this.search = this.selectedLabel;
            }
            
            // Listen to language changes
            window.addEventListener('language-changed', () => {
                this.updateTranslation();
            });
            
            // Check again shortly in case i18n wasn't loaded yet
            setTimeout(() => {
                this.updateTranslation();
            }, 50);
            setTimeout(() => {
                this.updateTranslation();
            }, 500);
        },
        updateTranslation() {
            if (this.search === this.selectedLabel || this.search === '') {
                let newOpts = JSON.parse($el.dataset.options || '[]');
                let selOpt = newOpts.find(o => o.value == $el.dataset.selected);
                if (selOpt) {
                    let translated = this.$store.i18n?.t(selOpt.label) || selOpt.label;
                    if (this.selectedLabel !== translated && translated !== selOpt.label) {
                        this.selectedLabel = translated;
                        this.search = this.selectedLabel;
                    }
                } else {
                    let translatedPh = this.placeholderText;
                    if (this.selectedLabel !== translatedPh) {
                        this.selectedLabel = translatedPh;
                        if (this.search !== '') this.search = '';
                    }
                }
            }
        }
    }"
    @click.outside="
        open = false; 
        if (search === '') { 
            if (selectedLabel !== placeholderText) {
                @if($attributes->wire('model')->value())
                $wire.set('{{ $attributes->wire('model')->value() }}', '');
                @endif
                selectedLabel = placeholderText;
            }
        }
        @if($allowCustom)
        else if (search !== selectedLabel) {
            @if($attributes->wire('model')->value())
            $wire.set('{{ $attributes->wire('model')->value() }}', search);
            @endif
            selectedLabel = search;
        }
        @else
        else if (search !== selectedLabel) {
            search = selectedLabel;
        }
        @endif
    "
    @keydown.escape.window="open = false"
    class="vc-dropdown"
    :class="{ 'z-50': open }"
    data-options="{{ json_encode($options) }}"
    data-selected="{{ (string) $selected }}"
    {{ $attributes->whereDoesntStartWith('wire:model')->whereDoesntStartWith('x-bind:placeholder') }}
>
    {{-- Trigger --}}
    <div class="relative w-full" @click="open = true">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="color: var(--vc-text-muted);">
                <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
            </div>
        @endif
        <input
            type="text"
            x-model="search"
            x-ref="input"
            @focus="open = true; $event.target.select()"
            @keydown.escape="open = false; $el.blur()"
            autocomplete="new-password"
            @input="
                open = true; 
                @if($allowCustom)
                selectedLabel = search;
                @endif
            "
            @change="
                if (search === '') {
                    @if($attributes->wire('model')->value())
                    $wire.set('{{ $attributes->wire('model')->value() }}', '');
                    @endif
                    selectedLabel = placeholderText;
                }
                @if($allowCustom)
                else {
                    @if($attributes->wire('model')->value())
                    $wire.set('{{ $attributes->wire('model')->value() }}', search);
                    @endif
                }
                @endif
            "
            class="vc-dropdown-trigger w-full focus-visible:outline-none {{ $disabled ? 'disabled' : '' }}"
            @if($attributes->has('x-bind:placeholder'))
                x-bind:placeholder="{{ $attributes->get('x-bind:placeholder') }}"
            @else
                x-bind:placeholder="placeholderText"
            @endif
            @if($disabled) disabled @endif
            style="padding-right: 1rem; padding-left: {{ $icon ? '2.5rem' : '1rem' }};"
        />
    </div>

    {{-- Lista de opciones --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        x-cloak
        class="vc-dropdown-list"
    >


        {{-- Opciones --}}
        <template x-for="option in filteredOptions" :key="option.value">
            <button
                type="button"
                class="vc-dropdown-item"
                :class="{ 'active': option.value == $root.dataset.selected }"
                @click="
                    @if($attributes->wire('model')->value())
                    $wire.set('{{ $attributes->wire('model')->value() }}', option.value);
                    @endif
                    let translated = $store.i18n?.t(option.label) || option.label;
                    selectedLabel = translated;
                    search = translated;
                    open = false;
                "
                x-text="$store.i18n?.t(option.label) || option.label"
            ></button>
        </template>

        {{-- Sin resultados --}}
        <template x-if="filteredOptions.length === 0">
            <div class="px-3 py-2 text-center text-xs" style="color: var(--vc-text-muted);">
                <span x-text="$store.i18n?.t('form.noResults') || 'No results'"></span>
            </div>
        </template>
    </div>
</div>
