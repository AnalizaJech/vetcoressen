@props([
    'placeholder' => 'form.selectDate',
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
    <div class="relative w-full cursor-pointer" @click="open = !open">
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
        class="absolute z-[9999] mt-1 w-72 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-2xl overflow-visible p-3"
        style="z-index: 9999;"
    >
        {{-- Quick Actions --}}
        <div class="flex items-center gap-2 mb-3 pb-2.5 border-b border-zinc-100 dark:border-zinc-700/60">
            <button type="button" @click="selectToday()" class="flex-1 py-1.5 text-xs font-bold rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 border border-emerald-200/50 dark:border-emerald-800/40 transition-colors text-center">
                <span x-text="$store.i18n?.t('form.today') || 'Hoy'">Hoy</span>
            </button>
            <button type="button" @click="selectTomorrow()" class="flex-1 py-1.5 text-xs font-bold rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 border border-emerald-200/50 dark:border-emerald-800/40 transition-colors text-center">
                <span x-text="$store.i18n?.t('form.tomorrow') || 'Mañana'">Mañana</span>
            </button>
        </div>

        {{-- Calendar Header --}}
        <div class="flex items-center justify-between mb-3 px-1">
            <button type="button" @click="prevMonth()" class="w-8 h-8 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 flex items-center justify-center transition-colors" :disabled="isPrevMonthDisabled()" :class="{'opacity-30 cursor-not-allowed': isPrevMonthDisabled()}">
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>
            
            <div class="font-extrabold text-sm text-zinc-900 dark:text-white tracking-wide flex items-center gap-1.5">
                <span x-text="monthNames[month] || 'Mes'"></span>
                <span x-text="year"></span>
            </div>

            <button type="button" @click="nextMonth()" class="w-8 h-8 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
        </div>

        {{-- Days of Week --}}
        <div class="grid grid-cols-7 gap-1 mb-1">
            <template x-for="day in daysOfWeek" :key="day">
                <div class="text-center text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider py-1" x-text="day"></div>
            </template>
        </div>

        {{-- Days Grid --}}
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
                        'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-500/20': isSelected(date),
                        'text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700': !isSelected(date) && !isDisabled(date),
                        'text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-300 dark:border-emerald-700': isToday(date) && !isSelected(date),
                        'text-zinc-300 dark:text-zinc-600 cursor-not-allowed opacity-40': isDisabled(date)
                    }"
                    :disabled="isDisabled(date)"
                    x-text="date"
                ></button>
            </template>
        </div>
    </div>
</div>

@once
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('datePicker', (config) => ({
            value: config.modelValue,
            minDate: config.minDate,
            open: false,
            month: new Date().getMonth(),
            year: new Date().getFullYear(),
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            blankDays: [],
            daysInMonth: [],
            
            get placeholderText() {
                let key = '{{ addslashes($placeholder) }}';
                return this.$store.i18n?.t(key) || 'Seleccionar fecha...';
            },

            get formattedValue() {
                if (!this.value || typeof this.value !== 'string') return '';
                return this.value;
            },

            init() {
                this.updateLocalization();
                this.$watch('$store.i18n.locale', () => {
                    this.updateLocalization();
                });

                this.syncFromValue(this.value);
                this.calculateDays();
                
                this.$watch('value', (newVal) => {
                    this.syncFromValue(newVal);
                    this.calculateDays();
                });

                this.$watch('open', (isOpen) => {
                    if (isOpen) {
                        this.syncFromValue(this.value);
                        this.calculateDays();
                    }
                });
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
                const isEn = this.$store.i18n?.locale === 'en';
                if (isEn) {
                    this.daysOfWeek = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
                    this.monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                } else {
                    this.daysOfWeek = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'];
                    this.monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                }
            },

            calculateDays() {
                const now = new Date();
                const y = (!isNaN(this.year) && this.year > 1900) ? parseInt(this.year, 10) : now.getFullYear();
                const m = (!isNaN(this.month) && this.month >= 0 && this.month <= 11) ? parseInt(this.month, 10) : now.getMonth();
                
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
                this.open = false;
            },

            selectToday() {
                const today = new Date();
                this.year = today.getFullYear();
                this.month = today.getMonth();
                this.value = this.formatDate(this.year, this.month, today.getDate());
                this.calculateDays();
                this.open = false;
            },

            selectTomorrow() {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                this.year = tomorrow.getFullYear();
                this.month = tomorrow.getMonth();
                this.value = this.formatDate(this.year, this.month, tomorrow.getDate());
                this.calculateDays();
                this.open = false;
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
                this.calculateDays();
            },

            nextMonth() {
                if (this.month == 11) {
                    this.year++;
                    this.month = 0;
                } else {
                    this.month++;
                }
                this.calculateDays();
            }
        }));
    });
</script>
@endonce
