@props([
    'placeholder' => 'form.selectDate',
    'icon' => 'calendar_month',
    'minDate' => null, // 'today'
    'maxDate' => null, // 'today'
    'isBirthdate' => false,
    'disabled' => false,
    'align' => 'left',
])

<div
    x-data="vcDatePicker({
        modelValue: @entangle($attributes->wire('model')),
        minDate: '{{ $minDate }}',
        maxDate: '{{ $maxDate ?? ($isBirthdate ? 'today' : '') }}',
        isBirthdate: {{ $isBirthdate ? 'true' : 'false' }},
        disabled: {{ $disabled ? 'true' : 'false' }},
        align: '{{ $align }}'
    })"
    @click.outside="open = false; viewMode = 'days'"
    @keydown.escape.window="open = false; viewMode = 'days'"
    :class="open ? 'relative w-full z-50' : 'relative w-full z-10'"
    class="w-full"
>
    {{-- Input disparador con icono a la izquierda --}}
    <div 
        class="relative w-full transition-opacity duration-150" 
        :class="isComponentDisabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'cursor-pointer'"
        @click="if (!isComponentDisabled) toggleCalendar()"
    >
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500">
                <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
            </div>
        @endif

        <input
            type="text"
            readonly
            :value="formattedValue"
            :disabled="isComponentDisabled"
            class="vc-dropdown-trigger w-full focus-visible:outline-none"
            :class="isComponentDisabled ? 'bg-zinc-100/70 dark:bg-zinc-800/40 cursor-not-allowed' : 'cursor-pointer'"
            @if($attributes->has('x-bind:placeholder'))
                x-bind:placeholder="{{ $attributes->get('x-bind:placeholder') }}"
            @else
                x-bind:placeholder="placeholderText"
            @endif
            style="padding-left: {{ $icon ? '2.5rem' : '0.875rem' }}; padding-right: 0.875rem;"
            {{ $attributes->whereDoesntStartWith('wire:model')->whereDoesntStartWith('x-bind:placeholder')->whereDoesntStartWith('disabled') }}
        />
    </div>

    {{-- Dropdown del calendario (Diseño Premium sin selects nativos y altura estable) --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
        x-cloak
        :class="align === 'right' ? 'right-0 left-auto' : 'left-0'"
        class="absolute mt-1.5 w-72 sm:w-80 min-h-[310px] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700/80 rounded-2xl shadow-2xl p-3.5 z-[99999] isolate"
    >
        {{-- Botones de acción rápida --}}
        @if(!$isBirthdate)
            <div class="flex items-center gap-2 mb-3 pb-2.5 border-b border-zinc-100 dark:border-zinc-800">
                <button type="button" @click="selectToday()" class="flex-1 py-1.5 text-xs font-bold rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 border border-emerald-200/60 dark:border-emerald-800/40 transition-all text-center">
                    <span x-text="$store.i18n?.t('form.today') || 'Today'">Today</span>
                </button>
                <button type="button" @click="selectTomorrow()" class="flex-1 py-1.5 text-xs font-bold rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 border border-emerald-200/60 dark:border-emerald-800/40 transition-all text-center">
                    <span x-text="$store.i18n?.t('form.tomorrow') || 'Tomorrow'">Tomorrow</span>
                </button>
            </div>
        @else
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-zinc-100 dark:border-zinc-800 text-xs font-bold text-zinc-700 dark:text-zinc-300">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm text-emerald-500">cake</span>
                    <span x-text="$store.i18n?.t('form.birthDate') || 'Date of Birth'">Date of Birth</span>
                </span>
                <button type="button" @click="selectToday()" class="px-2 py-0.5 text-[11px] font-bold rounded text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30">
                    <span x-text="$store.i18n?.t('form.today') || 'Today'">Today</span>
                </button>
            </div>
        @endif

        {{-- ═══ VISTA 1: DÍAS ═══ --}}
        <div x-show="viewMode === 'days'">
            {{-- Navegación del Calendario: Mes y Año Custom --}}
            <div class="flex items-center justify-between gap-1 mb-3">
                <button type="button" @click="prevMonth()" class="w-8 h-8 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center transition-colors shrink-0" :disabled="isPrevMonthDisabled()" :class="{'opacity-30 cursor-not-allowed': isPrevMonthDisabled()}">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </button>
                
                <div class="flex items-center gap-1.5">
                    {{-- Botón custom selector de Mes --}}
                    <button type="button" @click="viewMode = 'months'" class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-zinc-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 text-xs font-bold transition-colors border border-zinc-200 dark:border-zinc-700">
                        <span x-text="monthNames[month] || ''"></span>
                        <span class="material-symbols-outlined text-[14px]">expand_more</span>
                    </button>

                    {{-- Botón custom selector de Año --}}
                    <button type="button" @click="viewMode = 'years'" class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-zinc-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 text-xs font-bold transition-colors border border-zinc-200 dark:border-zinc-700">
                        <span x-text="year"></span>
                        <span class="material-symbols-outlined text-[14px]">expand_more</span>
                    </button>
                </div>

                <button type="button" @click="nextMonth()" class="w-8 h-8 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center transition-colors shrink-0" :disabled="isNextMonthDisabled()" :class="{'opacity-30 cursor-not-allowed': isNextMonthDisabled()}">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </button>
            </div>

            {{-- Días de la semana --}}
            <div class="grid grid-cols-7 gap-1 mb-1.5">
                <template x-for="day in daysOfWeek" :key="day">
                    <div class="text-center text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider py-1" x-text="day"></div>
                </template>
            </div>

            {{-- Grilla de Días --}}
            <div class="grid grid-cols-7 gap-1">
                <template x-for="blank in blankDays" :key="'blank-' + blank">
                    <div class="w-8 h-8"></div>
                </template>

                <template x-for="date in daysInMonth" :key="'date-' + date">
                    <button
                        type="button"
                        @click="!isDisabled(date) && selectDate(date)"
                        class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center transition-all"
                        :class="{
                            'bg-emerald-600 text-white font-extrabold shadow-md shadow-emerald-500/20': isSelected(date),
                            'text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-emerald-600 font-medium': !isSelected(date) && !isDisabled(date),
                            'text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700': isToday(date) && !isSelected(date),
                            'text-zinc-300 dark:text-zinc-600 cursor-not-allowed opacity-30': isDisabled(date)
                        }"
                        :disabled="isDisabled(date)"
                        x-text="date"
                    ></button>
                </template>
            </div>
        </div>

        {{-- ═══ VISTA 2: SELECTOR DE MESES (12 Meses Custom) ═══ --}}
        <div x-show="viewMode === 'months'" class="space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-100 dark:border-zinc-800">
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-emerald-500">calendar_month</span>
                    <span x-text="$store.i18n?.locale === 'en' ? 'Select Month' : 'Seleccionar Mes'">Select Month</span>
                </span>
                <button type="button" @click="viewMode = 'days'" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                    <span x-text="$store.i18n?.locale === 'en' ? 'Back' : 'Volver'">Back</span>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <template x-for="(mName, idx) in monthNames" :key="idx">
                    <button
                        type="button"
                        @click="setMonth(idx)"
                        class="py-2 px-1 text-xs font-semibold rounded-xl text-center transition-all"
                        :class="month === idx ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600'"
                        x-text="mName.slice(0, 3)"
                    ></button>
                </template>
            </div>
        </div>

        {{-- ═══ VISTA 3: SELECTOR DE AÑOS (Años Custom con Paginación) ═══ --}}
        <div x-show="viewMode === 'years'" class="space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-100 dark:border-zinc-800">
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-emerald-500">event</span>
                    <span x-text="$store.i18n?.locale === 'en' ? 'Select Year' : 'Seleccionar Año'">Select Year</span>
                </span>
                <button type="button" @click="viewMode = 'days'" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                    <span x-text="$store.i18n?.locale === 'en' ? 'Back' : 'Volver'">Back</span>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto pr-1">
                <template x-for="y in availableYears" :key="y">
                    <button
                        type="button"
                        @click="setYear(y)"
                        class="py-2 text-xs font-semibold rounded-xl text-center transition-all"
                        :class="year === y ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600'"
                        x-text="y"
                    ></button>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof window.vcDatePicker !== 'function') {
        window.vcDatePicker = function(config) {
            return {
                value: config.modelValue,
                minDate: config.minDate,
                maxDate: config.maxDate,
                isComponentDisabled: config.disabled || false,
                align: config.align || 'left',
                open: false,
                viewMode: 'days', // 'days' | 'months' | 'years'
                month: new Date().getMonth(),
                year: new Date().getFullYear(),
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                blankDays: [],
                daysInMonth: [],
                availableYears: [],
                
                get placeholderText() {
                    let key = '{{ addslashes($placeholder) }}';
                    return this.$store?.i18n?.t(key) || 'Select date...';
                },

                get formattedValue() {
                    if (!this.value || typeof this.value !== 'string') return '';
                    const parts = this.value.split('-');
                    if (parts.length === 3) {
                        const y = parseInt(parts[0], 10);
                        const m = parseInt(parts[1], 10) - 1;
                        const d = parseInt(parts[2], 10);
                        if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
                            const isEn = (this.$store?.i18n?.locale || localStorage.getItem('vc_locale')) === 'en';
                            const monthsShortEn = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                            const monthsShortEs = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                            const mStr = isEn ? monthsShortEn[m] : monthsShortEs[m];
                            const dStr = d < 10 ? '0' + d : d;
                            return isEn ? `${mStr} ${dStr}, ${y}` : `${dStr} ${mStr} ${y}`;
                        }
                    }
                    return this.value;
                },

                init() {
                    this.generateAvailableYears();
                    this.updateLocalization();
                    
                    if (this.$el) {
                        const obs = new MutationObserver(() => {
                            const d = this.$el.hasAttribute('disabled') || this.$el.getAttribute('aria-disabled') === 'true';
                            if (d !== this.isComponentDisabled) {
                                this.isComponentDisabled = d;
                            }
                        });
                        obs.observe(this.$el, { attributes: true });
                    }

                    if (this.$watch) {
                        this.$watch('$store.i18n.locale', () => {
                            this.updateLocalization();
                        });

                        this.$watch('value', (newVal) => {
                            this.syncFromValue(newVal);
                            this.calculateDays();
                        });

                        this.$watch('open', (isOpen) => {
                            if (isOpen) {
                                this.viewMode = 'days';
                                this.syncFromValue(this.value);
                                this.calculateDays();
                            }
                        });
                    }

                    this.syncFromValue(this.value);
                    this.calculateDays();
                },

                toggleCalendar() {
                    this.open = !this.open;
                    if (this.open) {
                        this.viewMode = 'days';
                        this.syncFromValue(this.value);
                        this.calculateDays();
                    }
                },

                generateAvailableYears() {
                    const currentYear = new Date().getFullYear();
                    let startYear = this.isBirthdate ? currentYear - 25 : currentYear - 5;
                    let endYear = this.isBirthdate ? currentYear : currentYear + 5;
                    let years = [];
                    for (let y = endYear; y >= startYear; y--) {
                        years.push(y);
                    }
                    this.availableYears = years;
                },

                syncFromValue(val) {
                    const now = new Date();
                    if (val && typeof val === 'string' && val.includes('-')) {
                        const parts = val.split('-');
                        if (parts.length === 3) {
                            const y = parseInt(parts[0], 10);
                            const m = parseInt(parts[1], 10) - 1;
                            if (!isNaN(y) && y > 1900) this.year = y;
                            if (!isNaN(m) && m >= 0 && m <= 11) this.month = m;
                            return;
                        }
                    }
                    if (isNaN(this.year) || this.year < 1900) this.year = now.getFullYear();
                    if (isNaN(this.month) || this.month < 0 || this.month > 11) this.month = now.getMonth();
                },

                updateLocalization() {
                    const isEn = (this.$store?.i18n?.locale || localStorage.getItem('vc_locale')) === 'en';
                    if (isEn) {
                        this.daysOfWeek = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
                        this.monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    } else {
                        this.daysOfWeek = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'];
                        this.monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    }
                },

                setMonth(mIdx) {
                    this.month = mIdx;
                    this.calculateDays();
                    this.viewMode = 'days';
                },

                setYear(yVal) {
                    this.year = yVal;
                    this.calculateDays();
                    this.viewMode = 'days';
                },

                calculateDays() {
                    const now = new Date();
                    let y = (!isNaN(this.year) && this.year > 1900) ? parseInt(this.year, 10) : now.getFullYear();
                    let m = (!isNaN(this.month) && this.month >= 0 && this.month <= 11) ? parseInt(this.month, 10) : now.getMonth();
                    
                    this.year = y;
                    this.month = m;

                    const daysInMonthCount = new Date(y, m + 1, 0).getDate();
                    const firstDayOfWeek = new Date(y, m, 1).getDay();
                    
                    let blanks = [];
                    for (let i = 0; i < firstDayOfWeek; i++) {
                        blanks.push(i);
                    }
                    
                    let days = [];
                    for (let i = 1; i <= daysInMonthCount; i++) {
                        days.push(i);
                    }
                    
                    this.blankDays = blanks;
                    this.daysInMonth = days;
                },

                formatDate(year, month, day) {
                    return year + '-' + ('0' + (month + 1)).slice(-2) + '-' + ('0' + day).slice(-2);
                },

                selectDate(date) {
                    this.value = this.formatDate(this.year, this.month, date);
                    if (this.$el) {
                        this.$el.dispatchEvent(new CustomEvent('input', { bubbles: true, detail: this.value }));
                        this.$el.dispatchEvent(new CustomEvent('change', { bubbles: true, detail: this.value }));
                    }
                    this.open = false;
                    this.viewMode = 'days';
                },

                selectToday() {
                    const today = new Date();
                    this.year = today.getFullYear();
                    this.month = today.getMonth();
                    this.value = this.formatDate(this.year, this.month, today.getDate());
                    this.calculateDays();
                    if (this.$el) {
                        this.$el.dispatchEvent(new CustomEvent('input', { bubbles: true, detail: this.value }));
                        this.$el.dispatchEvent(new CustomEvent('change', { bubbles: true, detail: this.value }));
                    }
                    this.open = false;
                    this.viewMode = 'days';
                },

                selectTomorrow() {
                    if (this.maxDate === 'today' || this.isBirthdate) return;
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    this.year = tomorrow.getFullYear();
                    this.month = tomorrow.getMonth();
                    this.value = this.formatDate(this.year, this.month, tomorrow.getDate());
                    this.calculateDays();
                    if (this.$el) {
                        this.$el.dispatchEvent(new CustomEvent('input', { bubbles: true, detail: this.value }));
                        this.$el.dispatchEvent(new CustomEvent('change', { bubbles: true, detail: this.value }));
                    }
                    this.open = false;
                    this.viewMode = 'days';
                },

                isToday(date) {
                    const today = new Date();
                    return today.getFullYear() === this.year && today.getMonth() === this.month && today.getDate() === date;
                },

                isSelected(date) {
                    if (!this.value || typeof this.value !== 'string') return false;
                    const parts = this.value.split('-');
                    if (parts.length !== 3) return false;
                    return parseInt(parts[0], 10) === this.year && 
                           (parseInt(parts[1], 10) - 1) === this.month && 
                           parseInt(parts[2], 10) === date;
                },

                isDisabled(date) {
                    const current = new Date(this.year, this.month, date);
                    if (this.minDate === 'today') {
                        const today = new Date();
                        today.setHours(0,0,0,0);
                        if (current < today) return true;
                    }
                    if (this.maxDate === 'today' || this.isBirthdate) {
                        const today = new Date();
                        today.setHours(23,59,59,999);
                        if (current > today) return true;
                    }
                    return false;
                },

                isPrevMonthDisabled() {
                    if (this.minDate === 'today') {
                        const today = new Date();
                        return this.year < today.getFullYear() || (this.year === today.getFullYear() && this.month <= today.getMonth());
                    }
                    return false;
                },

                isNextMonthDisabled() {
                    if (this.maxDate === 'today' || this.isBirthdate) {
                        const today = new Date();
                        return this.year > today.getFullYear() || (this.year === today.getFullYear() && this.month >= today.getMonth());
                    }
                    return false;
                },

                prevMonth() {
                    if (this.isPrevMonthDisabled()) return;
                    
                    if (this.month == 0) {
                        this.year--;
                        this.month = 11;
                    } else {
                        this.month--;
                    }
                    this.calculateDays();
                },

                nextMonth() {
                    if (this.isNextMonthDisabled()) return;

                    if (this.month == 11) {
                        this.year++;
                        this.month = 0;
                    } else {
                        this.month++;
                    }
                    this.calculateDays();
                }
            };
        };

        if (window.Alpine) {
            window.Alpine.data('datePicker', window.vcDatePicker);
            window.Alpine.data('vcDatePicker', window.vcDatePicker);
        } else {
            document.addEventListener('alpine:init', () => {
                window.Alpine.data('datePicker', window.vcDatePicker);
                window.Alpine.data('vcDatePicker', window.vcDatePicker);
            });
        }
    }
</script>
