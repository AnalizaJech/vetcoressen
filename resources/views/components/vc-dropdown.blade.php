{{-- Componente Custom Dropdown - Regla 13: nunca usar select nativo
     Usa div custom con opciones, muestra valor seleccionado, cierra al click fuera.
     Sin flechas a la derecha, solo icono a la izquierda si aplica.
     Opciones 100% reactivas en memoria. --}}

@props([
    'options' => [],
    'selected' => null,
    'placeholder' => 'form.select',
    'disabled' => false,
    'icon' => null,
    'allowCustom' => false,
    'searchable' => false,
])

<div
    x-data="{
        open: false,
        search: '',
        rawOptions: {{ json_encode($options) }},
        selectedValue: @if($attributes->wire('model')->value()) @entangle($attributes->wire('model')) @else '{{ (string) $selected }}' @endif,
        localeTrigger: 0,
        init() {
            window.addEventListener('language-changed', () => {
                this.localeTrigger++;
            });
            this.$watch('selectedValue', (val) => {
                if (this.$el && this.$el.dataset.selected !== String(val || '')) {
                    this.$el.dataset.selected = String(val || '');
                }
            });
            if (this.$el) {
                const ds = this.$el.getAttribute('data-selected');
                if (ds !== null && ds !== undefined && ds !== '' && this.selectedValue !== ds) {
                    this.selectedValue = ds;
                }
                const obs = new MutationObserver(() => {
                    const newDs = this.$el.getAttribute('data-selected');
                    if (newDs !== null && newDs !== undefined && newDs !== '' && newDs !== this.selectedValue) {
                        this.selectedValue = newDs;
                    }
                });
                obs.observe(this.$el, { attributes: true, attributeFilter: ['data-selected'] });
            }
        },
        translateKey(key) {
            if (!key) return '';
            this.localeTrigger;
            this.$store.i18n?.locale;
            
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
                'dashboard.filter': isEs ? 'Período' : 'Period',
                'dashboard.lastWeek': isEs ? 'Esta Semana' : 'This Week',
                'report.today': isEs ? 'Hoy' : 'Today',
                'report.thisWeek': isEs ? 'Esta Semana' : 'This Week',
                'report.thisMonth': isEs ? 'Este Mes' : 'This Month',
                'report.thisYear': isEs ? 'Este Año' : 'This Year',
                'report.custom': isEs ? 'Personalizado' : 'Custom',
                'dashboard.thisMonth': isEs ? 'Este Mes' : 'This Month',
                'dashboard.thisYear': isEs ? 'Este Año' : 'This Year',
                'table.status': isEs ? 'Todos los Estados' : 'All Statuses',
                'table.veterinarian': isEs ? 'Todos los Veterinarios' : 'All Veterinarians',
                'table.allVets': isEs ? 'Todos los Veterinarios' : 'All Veterinarians',
            };
            if (fallbacks[key]) return typeof fallbacks[key] === 'function' ? fallbacks[key]() : fallbacks[key];
            if (key.includes('.')) {
                let part = key.split('.').pop().replace(/([A-Z])/g, ' $1').trim();
                return part.charAt(0).toUpperCase() + part.slice(1);
            }
            return key;
        },
        get placeholderText() {
            return this.translateKey('{{ addslashes($placeholder) }}');
        },
        get optionsList() {
            if (Array.isArray(this.rawOptions) && this.rawOptions.length > 0) {
                return this.rawOptions;
            }
            try {
                return JSON.parse(this.$el?.dataset?.options || '[]');
            } catch(e) {
                return [];
            }
        },
        get displayLabel() {
            let opts = this.optionsList;
            let currentVal = (this.selectedValue !== null && this.selectedValue !== undefined) ? String(this.selectedValue) : (this.$el?.dataset?.selected || '{{ (string) $selected }}');
            let selOpt = opts.find(o => String(o.value) === String(currentVal));
            if (selOpt) {
                return this.translateKey(selOpt.label);
            }
            return this.placeholderText;
        },
        get isPlaceholder() {
            let opts = this.optionsList;
            let currentVal = (this.selectedValue !== null && this.selectedValue !== undefined) ? String(this.selectedValue) : (this.$el?.dataset?.selected || '{{ (string) $selected }}');
            return !opts.some(o => String(o.value) === String(currentVal));
        },
        get filteredOptions() {
            let opts = this.optionsList;
            if (!this.search || this.search.trim() === '') return opts;
            
            const term = this.search.toString().toLowerCase().trim();
            return opts.filter(opt => {
                let labelStr = String(this.translateKey(opt.label));
                return labelStr.toLowerCase().includes(term);
            });
        },
        selectOption(val, lbl) {
            this.selectedValue = val;
            if (this.$el) this.$el.dataset.selected = String(val);
            this.search = '';
            this.open = false;
            
            @if($attributes->wire('model')->value())
            try {
                const wireTarget = (typeof this.$wire !== 'undefined' && this.$wire) ? this.$wire : ((typeof $wire !== 'undefined' && $wire) ? $wire : null);
                if (wireTarget) {
                    wireTarget.set('{{ $attributes->wire('model')->value() }}', val, {{ $attributes->wire('model')->hasModifier('live') ? 'true' : 'false' }});
                }
            } catch(e) {
                console.error('Error updating wire model:', e);
            }
            @endif

            this.$dispatch('input', val);
            this.$dispatch('change', val);
            if (this.$el) {
                this.$el.dispatchEvent(new CustomEvent('input', { detail: val, bubbles: true }));
                this.$el.dispatchEvent(new CustomEvent('change', { detail: val, bubbles: true }));
            }
        }
    }"
    @click.outside="open = false; search = ''"
    @keydown.escape.window="open = false; search = ''"
    class="vc-dropdown relative w-full select-none"
    :class="{ 'z-50': open }"
    data-options="{{ json_encode($options) }}"
    data-selected="{{ (string) $selected }}"
    {{ $attributes->whereDoesntStartWith('wire:model')->whereDoesntStartWith('x-bind:placeholder')->whereDoesntStartWith('x-model') }}
>
    {{-- Trigger sin flecha a la derecha, solo icono a la izquierda --}}
    <button
        type="button"
        @if(!$disabled) @click="open = !open; if(open && {{ $searchable ? 'true' : 'false' }}) $nextTick(() => $refs.searchInput?.focus())" @endif
        class="vc-dropdown-trigger w-full flex items-center gap-2.5 text-left cursor-pointer transition-all duration-150 {{ $disabled ? 'opacity-60 cursor-not-allowed' : '' }}"
        :class="{ 'ring-2 ring-emerald-500/20 border-emerald-500': open }"
        @if($disabled) disabled @endif
    >
        @if($icon)
            <span class="material-symbols-outlined text-[18px] text-zinc-400 dark:text-zinc-500 shrink-0">{{ $icon }}</span>
        @endif
        <span 
            class="truncate text-sm font-medium flex-1"
            :class="isPlaceholder ? 'text-zinc-400 dark:text-zinc-500' : 'text-zinc-900 dark:text-zinc-100'"
            x-text="displayLabel"
        ></span>
    </button>

    {{-- Lista de Opciones Desplegable --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
        x-cloak
        class="vc-dropdown-list absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl max-h-60 overflow-y-auto p-1.5"
    >
        @if($searchable)
            <div class="p-1 mb-1 border-b border-zinc-100 dark:border-zinc-800 sticky top-0 bg-white dark:bg-zinc-900 z-10">
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-2.5 text-[16px] text-zinc-400">search</span>
                    <input 
                        type="text" 
                        x-ref="searchInput"
                        x-model="search" 
                        @click.stop
                        class="w-full pl-8 pr-3 py-1.5 text-xs bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-emerald-500"
                        :placeholder="translateKey('form.search') || 'Buscar...'"
                    />
                </div>
            </div>
        @endif

        {{-- Opciones --}}
        <template x-for="option in filteredOptions" :key="option.value">
            <button
                type="button"
                class="vc-dropdown-item w-full flex items-center justify-between px-3 py-2 text-xs font-medium rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800/80 hover:text-zinc-950 dark:hover:text-white transition-colors"
                :class="{ 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold': String(option.value) === String(selectedValue) }"
                @click="selectOption(option.value, option.label)"
            >
                <div class="flex items-center gap-2 truncate">
                    <template x-if="option.icon">
                        <span class="material-symbols-outlined text-[16px] text-zinc-400" x-text="option.icon"></span>
                    </template>
                    <span class="truncate" x-text="translateKey(option.label)"></span>
                </div>
                <span x-show="String(option.value) === String(selectedValue)" class="material-symbols-outlined text-[16px] text-emerald-600 dark:text-emerald-400 shrink-0 ml-2">check</span>
            </button>
        </template>

        {{-- Sin resultados --}}
        <template x-if="filteredOptions.length === 0">
            <div class="px-3 py-3 text-center text-xs text-zinc-400 dark:text-zinc-500">
                <span x-text="translateKey('form.noResults') || 'No results found'"></span>
            </div>
        </template>
    </div>
</div>
