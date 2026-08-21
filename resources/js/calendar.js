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
            const statusName = props.status || 'APPOINTMENT';

            if (isList) {
                return {
                    html: `
                        <div class="flex items-center justify-between w-full py-1.5 px-2">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: ${color};"></span>
                                <span class="font-extrabold text-sm text-zinc-950 dark:text-white">${props.mascota || 'Pet'}</span>
                                <span class="text-xs text-zinc-600 dark:text-zinc-300 font-medium">(${props.cliente || '-'})</span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400 italic truncate max-w-xs">${props.reason || ''}</span>
                            </div>
                            <span class="text-[10px] px-2.5 py-1 font-extrabold rounded-full uppercase tracking-wider" style="background-color: ${color}20; color: ${color}; border: 1px solid ${color}40;">
                                ${statusName}
                            </span>
                        </div>
                    `
                };
            }

            if (isMonth) {
                return {
                    html: `
                        <div class="px-2 py-1 rounded-md text-[11px] font-semibold truncate flex items-center gap-1.5 w-full bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 shadow-2xs hover:shadow-md transition-all cursor-pointer" style="border-left: 3.5px solid ${color};">
                            <span class="font-extrabold text-zinc-500 dark:text-zinc-400 text-[10px]">${arg.timeText || ''}</span>
                            <span class="font-black text-zinc-950 dark:text-white truncate">${props.mascota || ''}</span>
                            <span class="text-[9px] uppercase font-bold ml-auto px-1 rounded" style="background-color: ${color}18; color: ${color};">
                                ${statusName.substring(0, 3)}
                            </span>
                        </div>
                    `
                };
            }

            // Vista Semana y Día (timeGrid) - Diseño Card Premium de Alto Contraste
            return {
                html: `
                    <div class="h-full w-full p-2 rounded-lg flex flex-col justify-between overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/90 shadow-xs hover:shadow-md transition-all duration-150" style="border-left: 4px solid ${color};">
                        <div class="min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="font-black text-[12px] truncate tracking-tight text-zinc-950 dark:text-white leading-tight">
                                    ${props.mascota || 'Pet'}
                                </span>
                                <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded tracking-wide shrink-0" style="background-color: ${color}20; color: ${color}; border: 0.5px solid ${color}35;">
                                    ${statusName.substring(0, 4)}
                                </span>
                            </div>
                            <p class="text-[11px] text-zinc-700 dark:text-zinc-200 truncate font-semibold">
                                ${props.cliente || ''}
                            </p>
                            ${props.reason ? `<p class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate italic mt-0.5">${props.reason}</p>` : ''}
                        </div>
                        <div class="flex items-center justify-between text-[10px] font-bold text-zinc-500 dark:text-zinc-400 mt-1 pt-1 border-t border-zinc-100 dark:border-zinc-800/60">
                            <span>${arg.timeText || ''}</span>
                            <span class="text-[9px] font-medium text-zinc-400">${props.veterinario ? props.veterinario.split(' ')[0] : ''}</span>
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
