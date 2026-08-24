/**
 * VETCORESSEN - Módulo de calendario para citas con FullCalendar
 * 
 * Se inicializa desde la vista cita-index.blade.php cuando la vista activa es 'calendario'.
 * Carga las citas via Livewire wire y las muestra con diseño limpio, alto contraste y tipografía ultra legible.
 */

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

window.initVetCalendar = function (el, wire) {
    if (window._vcActiveCalendar) {
        try { window._vcActiveCalendar.destroy(); } catch (e) {}
        window._vcActiveCalendar = null;
    }
    if (el._vcCalendar) {
        try { el._vcCalendar.destroy(); } catch (e) {}
    }

    const currentLocale = localStorage.getItem('vc_locale') || document.documentElement.lang || 'en';

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
        
        initialView: 'timeGridWeek',
        headerToolbar: false,

        datesSet: function (info) {
            window.dispatchEvent(new CustomEvent('calendar-view-updated', {
                detail: {
                    title: info.view.title,
                    type: info.view.type
                }
            }));
        },

        locale: currentLocale,
        firstDay: 1, // Monday
        
        // Clinic operating hours
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        slotDuration: '00:30:00',
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        },
        
        allDaySlot: false,
        nowIndicator: true,
        navLinks: true,
        navLinkDayClick: function(date, jsEvent) {
            calendar.changeView('timeGridDay', date);
            window.dispatchEvent(new CustomEvent('calendar-view-updated', {
                detail: {
                    title: calendar.view.title,
                    type: 'timeGridDay'
                }
            }));
        },
        height: 'auto',
        contentHeight: 680,
        dayMaxEvents: 4,
        dayMaxEventRows: 4,
        moreLinkClick: 'popover',
        
        editable: true,
        droppable: false,
        eventDurationEditable: false,
        slotEventOverlap: true,
        
        selectable: true,
        select: function(info) {
            const dateObj = info.start;
            const dateStr = dateObj.toLocaleDateString('en-CA');
            const timeStr = dateObj.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
            window.location.href = `/appointments/create?fecha=${dateStr}&hora=${timeStr}`;
        },

        events: function (info, successCallback, failureCallback) {
            if (!wire || !wire.getCitasCalendario) {
                successCallback([]);
                return;
            }
            wire.getCitasCalendario(info.startStr, info.endStr)
                .then(events => {
                    successCallback(events || []);
                })
                .catch(err => {
                    console.error('Error loading FullCalendar appointments:', err);
                    failureCallback(err);
                });
        },

        eventClick: function (info) {
            info.jsEvent.preventDefault();
            const citaId = parseInt(info.event.id);
            if (wire) {
                if (typeof wire.abrirModalVer === 'function') {
                    wire.abrirModalVer(citaId);
                } else if (typeof wire.ver === 'function') {
                    wire.ver(citaId).then(() => {
                        if (window.Flux) Flux.modal('ver-cita').show();
                    });
                }
            }
        },

        eventContent: function (arg) {
            const props = arg.event.extendedProps || {};
            const isMonth = arg.view.type === 'dayGridMonth';
            const isList = arg.view.type.includes('list');
            const color = arg.event.backgroundColor || '#10b981';
            const isEn = (window.Alpine?.store('i18n')?.locale || localStorage.getItem('vc_locale')) === 'en';
            
            const rawStatus = String(props.status || 'PENDIENTE').trim();
            const statusKey = rawStatus.toLowerCase();
            const statusMapEn = {
                'pendiente': 'Pending',
                'confirmada': 'Confirmed',
                'en_progreso': 'In Progress',
                'completada': 'Completed',
                'cancelada': 'Cancelled',
                'emergencia': 'Emergency',
                'excedido': 'Overdue',
                'overdue': 'Overdue',
                'atendido': 'Attended',
                'atendida': 'Attended'
            };
            const statusMapEs = {
                'pendiente': 'Pendiente',
                'confirmada': 'Confirmada',
                'en_progreso': 'En Progreso',
                'completada': 'Completada',
                'cancelada': 'Cancelada',
                'emergencia': 'Emergencia',
                'excedido': 'Excedido',
                'overdue': 'Excedido',
                'atendido': 'Atendido',
                'atendida': 'Atendida'
            };
            
            let statusName = window.Alpine?.store('i18n')?.t('status.' + statusKey);
            if (!statusName || statusName === 'status.' + statusKey || statusName === statusKey) {
                statusName = (isEn ? statusMapEn[statusKey] : statusMapEs[statusKey]) || rawStatus;
            }

            let reasonText = props.reason || '';
            if (isEn && reasonText) {
                const reasonMap = {
                    'consulta': 'Consultation',
                    'consulta general': 'General Consultation',
                    'vacunación': 'Vaccination',
                    'vacunacion': 'Vaccination',
                    'desparasitación': 'Deworming',
                    'desparasitacion': 'Deworming',
                    'cirugía': 'Surgery',
                    'cirugia': 'Surgery',
                    'control': 'Checkup',
                    'urgencia': 'Urgency',
                    'emergencia': 'Emergency'
                };
                const lowerR = reasonText.toLowerCase().trim();
                if (reasonMap[lowerR]) {
                    reasonText = reasonMap[lowerR];
                }
            }

            if (isList) {
                return {
                    html: `
                        <div class="flex items-center justify-between w-full py-2.5 px-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 rounded-xl transition-all border-l-4" style="border-left-color: ${color};">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[18px] text-zinc-400">pets</span>
                                <div>
                                    <span class="font-extrabold text-xs text-zinc-900 dark:text-zinc-100">${props.mascota || 'Pet'}</span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium ml-1">(${props.cliente || '-'})</span>
                                </div>
                                ${reasonText ? `<span class="text-xs text-zinc-400 dark:text-zinc-500 italic truncate max-w-xs">&bull; ${reasonText}</span>` : ''}
                            </div>
                            <span class="text-[10px] px-2.5 py-1 font-bold rounded-lg uppercase tracking-wider shadow-2xs" style="background-color: ${color}15; color: ${color}; border: 1px solid ${color}30;">
                                ${statusName}
                            </span>
                        </div>
                    `
                };
            }

            if (isMonth) {
                return {
                    html: `
                        <div class="px-2 py-1 rounded-md text-[11px] font-semibold truncate flex items-center justify-between gap-1.5 w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xs hover:shadow-xs transition-all cursor-pointer" style="border-left: 3.5px solid ${color};">
                            <div class="flex items-center gap-1 min-w-0 truncate">
                                <span class="font-extrabold text-zinc-500 dark:text-zinc-400 text-[10px] shrink-0">${arg.timeText || ''}</span>
                                <span class="font-bold text-zinc-900 dark:text-zinc-100 truncate text-[11px]">${props.mascota || ''}</span>
                            </div>
                            <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded shrink-0" style="background-color: ${color}15; color: ${color};">
                                ${statusName}
                            </span>
                        </div>
                    `
                };
            }

            // Vista Semana y Día (timeGrid) - Diseño Card Premium y Limpio
            return {
                html: `
                    <div class="h-full w-full p-2 rounded-xl flex flex-col justify-between overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs hover:shadow-md transition-all duration-150" style="border-left: 3.5px solid ${color};">
                        <div class="min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="font-black text-[11px] truncate tracking-tight text-zinc-900 dark:text-zinc-100 leading-tight">
                                    ${props.mascota || 'Pet'}
                                </span>
                                <span class="text-[8.5px] font-bold uppercase px-1.5 py-0.5 rounded-md tracking-wider shrink-0" style="background-color: ${color}15; color: ${color}; border: 1px solid ${color}30;">
                                    ${statusName}
                                </span>
                            </div>
                            <p class="text-[10px] text-zinc-600 dark:text-zinc-400 truncate font-semibold">
                                ${props.cliente || ''}
                            </p>
                            ${props.reason ? `<p class="text-[9.5px] text-zinc-400 dark:text-zinc-500 truncate italic mt-0.5">${props.reason}</p>` : ''}
                        </div>
                        <div class="flex items-center justify-between text-[9.5px] font-bold text-zinc-500 dark:text-zinc-400 mt-1 pt-1 border-t border-zinc-100 dark:border-zinc-800/60">
                            <span>${arg.timeText || ''}</span>
                            <span class="text-[9px] font-medium text-zinc-400 truncate max-w-[80px]">${props.veterinario ? props.veterinario.split(' ')[0] : ''}</span>
                        </div>
                    </div>
                `
            };
        },

        eventDrop: function (info) {
            const citaId = parseInt(info.event.id);
            const dateObj = info.event.start;
            const newStart = dateObj.toLocaleDateString('en-CA') + 'T' + dateObj.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            
            wire.moverCita(citaId, newStart).catch(() => {
                info.revert();
            });
        },
    });

    calendar.render();

    el._vcCalendar = calendar;
    window._vcActiveCalendar = calendar;

    // Disparar evento inicial con el título y la vista actual
    if (calendar.view) {
        window.dispatchEvent(new CustomEvent('calendar-view-updated', {
            detail: {
                title: calendar.view.title,
                type: calendar.view.type
            }
        }));
    }

    window.addEventListener('language-changed', (e) => {
        const lang = e.detail?.locale || localStorage.getItem('vc_locale') || 'en';
        calendar.setOption('locale', lang);
        if (calendar.view) {
            window.dispatchEvent(new CustomEvent('calendar-view-updated', {
                detail: {
                    title: calendar.view.title,
                    type: calendar.view.type
                }
            }));
        }
    });

    window.addEventListener('calendar-set-view', (e) => {
        if (calendar && e.detail?.view) {
            calendar.changeView(e.detail.view);
            if (calendar.view) {
                window.dispatchEvent(new CustomEvent('calendar-view-updated', {
                    detail: {
                        title: calendar.view.title,
                        type: calendar.view.type
                    }
                }));
            }
        }
    });

    window.addEventListener('calendar-prev', () => {
        if (calendar) calendar.prev();
    });

    window.addEventListener('calendar-next', () => {
        if (calendar) calendar.next();
    });

    window.addEventListener('calendar-today', () => {
        if (calendar) calendar.today();
    });

    window.addEventListener('calendar-resize', () => {
        if (calendar) calendar.updateSize();
    });

    document.addEventListener('livewire:navigating', function cleanup() {
        try { calendar.destroy(); } catch (e) {}
        if (window._vcActiveCalendar === calendar) window._vcActiveCalendar = null;
        document.removeEventListener('livewire:navigating', cleanup);
    }, { once: true });

    return calendar;
};
