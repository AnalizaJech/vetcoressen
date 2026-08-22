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

<div
    x-data="{
        open: false,
        search: '',
        selectedValue: '{{ (string) $selected }}',
        localeTrigger: 0,
        init() {
            window.addEventListener('language-changed', () => {
                this.localeTrigger++;
            });
        },
        translateKey(key) {
            if (!key) return '';
            this.localeTrigger; // reactive dependency
            this.$store.i18n?.locale; // reactive dependency
            
            let trans = this.$store.i18n?.t(key);
            if (trans && trans !== key && trans !== '') return trans;

            const isEs = (this.$store.i18n?.locale || localStorage.getItem('vc_locale')) === 'es';
            const fallbacks = {
                'filter.allClients': isEs ? 'Todos los Clientes' : 'All Clients',
                'filter.allPets': isEs ? 'Todas las Mascotas' : 'All Pets',
                'filter.allSpecies': isEs ? 'Todas las Especies' : 'All Species',
                'filter.allBranches': isEs ? 'Todas las Sucursales' : 'All Branches',
                'filter.allCategories': isEs ? 'Todas las Categorías' : 'All Categories',
                'filter.allRoles': isEs ? 'Todos los Roles' : 'All Roles',
                'filter.allSuppliers': isEs ? 'Todos los Proveedores' : 'All Suppliers',
                'filter.allVeterinarians': isEs ? 'Todos los Veterinarios' : 'All Veterinarians',
                'filter.allStatuses': isEs ? 'Todos los Estados' : 'All Statuses',
                'citas.dayView': isEs ? 'Vista Día' : 'Day View',
                'citas.weekView': isEs ? 'Vista Semana' : 'Week View',
                'citas.monthView': isEs ? 'Vista Mes' : 'Month View',
                'citas.listView': isEs ? 'Lista Agenda' : 'Agenda List',
                'form.select': isEs ? 'Seleccionar...' : 'Select...',
            };
            if (fallbacks[key]) return fallbacks[key];
            if (key.includes('.')) {
                let part = key.split('.').pop().replace(/([A-Z])/g, ' $1').trim();
                return part.charAt(0).toUpperCase() + part.slice(1);
            }
            return key;
        },
        get placeholderText() {
            return this.translateKey('{{ addslashes($placeholder) }}');
        },
        get displayLabel() {
            if (this.search !== '' && this.open) return this.search;
            let newOpts = JSON.parse($el.dataset.options || '[]');
            let currentVal = this.selectedValue || $el.dataset.selected || '{{ (string) $selected }}';
            let selOpt = newOpts.find(o => String(o.value) === String(currentVal));
            if (selOpt) {
                return this.translateKey(selOpt.label);
            }
            return this.placeholderText;
        },
        get filteredOptions() {
            let opts = JSON.parse($el.dataset.options || '[]');
            if (this.search === '') return opts.slice(0, 50);
            
            const term = this.search.toString().toLowerCase();
            let result = opts.filter(opt => {
                let labelStr = String(this.translateKey(opt.label));
                return labelStr.toLowerCase().includes(term);
            });
            return result.slice(0, 50);
        },
        selectOption(val, lbl) {
            this.selectedValue = val;
            $el.dataset.selected = val;
            this.search = '';
            this.open = false;
            
            @if($attributes->wire('model')->value())
            $wire.set('{{ $attributes->wire('model')->value() }}', val);
            @endif

            $dispatch('input', val);
            $dispatch('change', val);
        }
    }"
    @click.outside="
        open = false; 
        search = '';
    "
    @keydown.escape.window="open = false; search = ''"
    class="vc-dropdown"
    :class="{ 'z-50': open }"
    data-options="{{ json_encode($options) }}"
    data-selected="{{ (string) $selected }}"
    {{ $attributes->whereDoesntStartWith('wire:model')->whereDoesntStartWith('x-bind:placeholder')->whereDoesntStartWith('x-model') }}
>
    {{-- Trigger con ICONO A LA IZQUIERDA UNICAMENTE (Sin flecha a la derecha) --}}
    <div class="relative w-full cursor-pointer" @click="open = true">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500">
                <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
            </div>
        @endif
        <input
            type="text"
            :value="displayLabel"
            x-ref="input"
            @focus="open = true; search = ''; $event.target.select()"
            @keydown.escape="open = false; search = ''; $el.blur()"
            autocomplete="new-password"
            @input="open = true; search = $event.target.value"
            class="vc-dropdown-trigger w-full focus-visible:outline-none cursor-pointer {{ $disabled ? 'disabled' : '' }}"
            :placeholder="placeholderText"
            @if($disabled) disabled @endif
            style="padding-left: {{ $icon ? '2.5rem' : '0.875rem' }}; padding-right: 0.875rem;"
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
                class="vc-dropdown-item flex items-center justify-between"
                :class="{ 'active': option.value == (selectedValue || $el.dataset.selected) }"
                @click="selectOption(option.value, option.label)"
            >
                <span x-text="translateKey(option.label)"></span>
                <span x-show="option.value == (selectedValue || $el.dataset.selected)" class="material-symbols-outlined text-[16px] text-emerald-600 dark:text-emerald-400">check</span>
            </button>
        </template>

        {{-- Sin resultados --}}
        <template x-if="filteredOptions.length === 0">
            <div class="px-3 py-2 text-center text-xs" style="color: var(--vc-text-muted);">
                <span x-text="translateKey('form.noResults') || 'No results'"></span>
            </div>
        </template>
    </div>
</div>
