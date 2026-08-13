@props([
    'placeholder' => 'Seleccionar fecha...',
    'icon' => 'calendar_month',
    'minDate' => null, // 'today'
])

<div
    x-data="datePicker({
        modelValue: @entangle($attributes->wire('model')),
        minDate: '{{ $minDate }}'
    })"
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
            @if($attributes->has('x-bind:placeholder'))
                x-bind:placeholder="{{ $attributes->get('x-bind:placeholder') }}"
            @else
                x-bind:placeholder="placeholderText"
            @endif
            style="padding-right: 1rem; padding-left: {{ $icon ? '3rem' : '1rem' }};"
            {{ $attributes->whereDoesntStartWith('wire:model')->whereDoesntStartWith('x-bind:placeholder') }}
        />
    </div>

    {{-- Calendar Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        x-cloak
        class="absolute z-50 mt-1 w-72 bg-white dark:bg-vc-surface border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl overflow-hidden"
    >
        {{-- Quick Actions --}}
        <div class="flex items-center gap-2 p-3 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-vc-surface">
            <button type="button" @click="selectToday()" class="flex-1 px-3 py-1.5 text-xs font-medium rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 transition-colors">
                <span x-text="$store.i18n?.t('form.today') || 'Hoy'"></span>
            </button>
            <button type="button" @click="selectTomorrow()" class="flex-1 px-3 py-1.5 text-xs font-medium rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 transition-colors">
                <span x-text="$store.i18n?.t('form.tomorrow') || 'Mañana'"></span>
            </button>
        </div>

        {{-- Calendar Header --}}
        <div class="flex items-center justify-between px-4 pt-3 pb-2">
            <button type="button" @click="prevMonth()" class="p-1 rounded-full hover:bg-zinc-100 dark:hover:bg-vc-surface-alt text-zinc-600 dark:text-zinc-400 transition-colors" :disabled="isPrevMonthDisabled()" :class="{'opacity-40 cursor-not-allowed': isPrevMonthDisabled()}">
                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
            </button>
            <div class="flex items-center gap-1">
                {{-- Month Dropdown --}}
                <div x-data="{ openMonth: false }" class="relative">
                    <button type="button" @click="openMonth = !openMonth" class="flex items-center gap-1 text-sm font-medium text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-vc-surface-alt px-2 py-1 rounded-md transition-colors">
                        <span x-text="getMonths()[month]"></span>
                        <span class="material-symbols-outlined text-[16px] text-zinc-400">arrow_drop_down</span>
                    </button>
                    <div x-show="openMonth" @click.outside="openMonth = false" x-cloak class="absolute top-full left-0 mt-1 w-32 bg-white dark:bg-vc-surface-alt border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg z-[60] max-h-48 overflow-y-auto">
                        <ul class="py-1">
                            <template x-for="(m, i) in getMonths()" :key="i">
                                <li>
                                    <button type="button" @click="month = i; openMonth = false" class="w-full text-left px-3 py-1.5 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" :class="{'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 font-semibold': month == i, 'text-zinc-700 dark:text-zinc-300': month != i}">
                                        <span x-text="m"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                {{-- Year Dropdown --}}
                <div x-data="{ openYear: false }" class="relative">
                    <button type="button" @click="openYear = !openYear" class="flex items-center gap-1 text-sm font-medium text-zinc-900 dark:text-white hover:bg-zinc-100 dark:hover:bg-vc-surface-alt px-2 py-1 rounded-md transition-colors">
                        <span x-text="year"></span>
                        <span class="material-symbols-outlined text-[16px] text-zinc-400">arrow_drop_down</span>
                    </button>
                    <div x-show="openYear" @click.outside="openYear = false" x-cloak class="absolute top-full right-0 mt-1 w-24 bg-white dark:bg-vc-surface-alt border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg z-[60] max-h-48 overflow-y-auto vc-custom-scroll">
                        <ul class="py-1">
                            <template x-for="y in getYears()" :key="y">
                                <li>
                                    <button type="button" @click="year = y; openYear = false" class="w-full text-center px-3 py-1.5 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors" :class="{'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 font-semibold': year == y, 'text-zinc-700 dark:text-zinc-300': year != y}">
                                        <span x-text="y"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
            <button type="button" @click="nextMonth()" class="p-1 rounded-full hover:bg-zinc-100 dark:hover:bg-vc-surface-alt text-zinc-600 dark:text-zinc-400 transition-colors">
                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
            </button>
        </div>

        {{-- Calendar Grid --}}
        <div class="px-4 pb-4">
            <div class="grid grid-cols-7 gap-1 mb-2">
                <template x-for="day in daysOfWeek" :key="day">
                    <div class="text-center text-[10px] font-bold text-zinc-700 dark:text-zinc-400 uppercase tracking-wider" x-text="day"></div>
                </template>
            </div>
            <div class="grid grid-cols-7 gap-1">
                <template x-for="blank in blankDays">
                    <div class="w-8 h-8"></div>
                </template>
                <template x-for="date in daysInMonth" :key="date">
                    <button
                        type="button"
                        @click="selectDate(date)"
                        :disabled="isDisabled(date)"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-sm transition-colors"
                        :class="{
                            'bg-emerald-500 text-white font-medium shadow-sm': isSelected(date),
                            'text-zinc-900 dark:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-vc-surface-alt font-medium': !isSelected(date) && !isDisabled(date),
                            'text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-900/20': isToday(date) && !isSelected(date),
                            'text-zinc-400 dark:text-zinc-600 cursor-not-allowed': isDisabled(date)
                        }"
                        x-text="date"
                    ></button>
                </template>
            </div>
        </div>
    </div>
</div>

{{-- Alpine logic logic specific for this component --}}
@once
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('datePicker', (config) => ({
            value: config.modelValue,
            minDate: config.minDate,
            open: false,
            month: new Date().getMonth(),
            year: new Date().getFullYear(),
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'], // Could be localized later
            blankDays: [],
            daysInMonth: [],
            
            get placeholderText() {
                let key = '{{ addslashes($placeholder) }}';
                return this.$store.i18n?.t(key) || key;
            },

            get formattedValue() {
                if (!this.value) return '';
                return this.value;
            },

            getMonths() {
                const locale = this.$store.i18n?.locale === 'en' ? 'en-US' : 'es-ES';
                return Array.from({length: 12}, (_, i) => {
                    const d = new Date(this.year, i, 1);
                    return d.toLocaleString(locale, { month: 'long' }).replace(/^\w/, c => c.toUpperCase());
                });
            },

            getYears() {
                const currentYear = new Date().getFullYear();
                const startYear = this.minDate === 'today' ? currentYear : currentYear - 80;
                const endYear = currentYear + 20;
                let y = [];
                for (let i = startYear; i <= endYear; i++) y.push(i);
                return y;
            },

            init() {
                // Adjust days of week based on locale
                this.$watch('$store.i18n.locale', () => {
                    this.updateDaysOfWeek();
                });
                this.updateDaysOfWeek();

                if (this.value) {
                    const d = new Date(this.value + 'T00:00:00');
                    this.month = d.getMonth();
                    this.year = d.getFullYear();
                }
                
                this.calculateDays();
                
                this.$watch('month', () => this.calculateDays());
                this.$watch('year', () => this.calculateDays());
                this.$watch('open', (isOpen) => {
                    if (isOpen && this.value) {
                        const d = new Date(this.value + 'T00:00:00');
                        this.month = d.getMonth();
                        this.year = d.getFullYear();
                    }
                });
            },

            updateDaysOfWeek() {
                const locale = this.$store.i18n?.locale === 'en' ? 'en-US' : 'es-ES';
                if (locale === 'en-US') {
                    this.daysOfWeek = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
                } else {
                    this.daysOfWeek = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'];
                }
            },

            calculateDays() {
                let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                let dayOfWeek = new Date(this.year, this.month, 1).getDay();
                let blankdaysArray = [];
                for (var i = 1; i <= dayOfWeek; i++) {
                    blankdaysArray.push(i);
                }
                let daysArray = [];
                for (var i = 1; i <= daysInMonth; i++) {
                    daysArray.push(i);
                }
                this.blankDays = blankdaysArray;
                this.daysInMonth = daysArray;
            },

            formatDate(year, month, day) {
                return year + '-' + ('0' + (month + 1)).slice(-2) + '-' + ('0' + day).slice(-2);
            },

            selectDate(date) {
                this.value = this.formatDate(this.year, this.month, date);
                this.open = false;
            },

            selectToday() {
                const today = new Date();
                this.value = this.formatDate(today.getFullYear(), today.getMonth(), today.getDate());
                this.open = false;
            },

            selectTomorrow() {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                this.value = this.formatDate(tomorrow.getFullYear(), tomorrow.getMonth(), tomorrow.getDate());
                this.open = false;
            },

            isToday(date) {
                const today = new Date();
                const d = new Date(this.year, this.month, date);
                return today.toDateString() === d.toDateString();
            },

            isSelected(date) {
                if (!this.value) return false;
                const d = new Date(this.value + 'T00:00:00');
                return d.getFullYear() === this.year && d.getMonth() === this.month && d.getDate() === date;
            },

            isDisabled(date) {
                if (this.minDate === 'today') {
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    const current = new Date(this.year, this.month, date);
                    return current < today;
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

            prevMonth() {
                if (this.isPrevMonthDisabled()) return;
                
                if (this.month == 0) {
                    this.year--;
                    this.month = 11;
                } else {
                    this.month--;
                }
            },

            nextMonth() {
                if (this.month == 11) {
                    this.year++;
                    this.month = 0;
                } else {
                    this.month++;
                }
            }
        }));
    });
</script>
@endonce
