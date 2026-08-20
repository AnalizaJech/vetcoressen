/**
 * VETCORESSEN - Módulo de calendario para citas con FullCalendar
 * 
 * Se inicializa desde la vista cita-index.blade.php cuando la vista activa es 'calendario'.
 * Carga las citas via Livewire wire y las muestra con diseño limpio y profesional.
 */

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

// Registrar globalmente para inicialización desde Alpine
window.initVetCalendar = function (el, wire) {
    if (window._vcActiveCalendar) {
        try { window._vcActiveCalendar.destroy(); } catch (e) {}
        window._vcActiveCalendar = null;
    }
    if (el._vcCalendar) {
        try { el._vcCalendar.destroy(); } catch (e) {}
    }

    const currentLocale = localStorage.getItem('vc_locale') || document.documentElement.lang || 'es';

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
        
        initialView: 'timeGridWeek',
        
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridDay,timeGridWeek,dayGridMonth,listWeek',
        },

        locale: currentLocale,
        firstDay: 1, // Lunes
        
        // Rango de atención de la clínica
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
        contentHeight: 650,
        dayMaxEvents: 3,
        dayMaxEventRows: 3,
        moreLinkClick: 'popover',
        
        editable: true,
        droppable: false,
        eventDurationEditable: false,
        slotEventOverlap: true, // Google Calendar overlap
        
        selectable: true,
        select: function(info) {
            const dateObj = info.start;
            const dateStr = dateObj.toLocaleDateString('en-CA');
            const timeStr = dateObj.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
            window.location.href = `/appointments/create?fecha=${dateStr}&hora=${timeStr}`;
        },

        // Carga de eventos asíncrona via Livewire
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
                    console.error('Error al cargar citas de FullCalendar:', err);
                    failureCallback(err);
                });
        },

        // Click en evento → abrir modal de ver/gestionar cita
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            const citaId = parseInt(info.event.id);
            if (wire && wire.abrirModalVer) {
                wire.abrirModalVer(citaId);
            }
        },

        // Renderizado visual elegante y no invasivo
        eventContent: function (arg) {
            const props = arg.event.extendedProps || {};
            const isMonth = arg.view.type === 'dayGridMonth';
            const isList = arg.view.type.includes('list');

            if (isList) {
                return {
                    html: `
                        <div class="flex items-center justify-between w-full py-1">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-sm text-zinc-900 dark:text-white">${props.mascota || 'Mascota'}</span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">(${props.cliente || '-'})</span>
                                <span class="text-xs text-zinc-600 dark:text-zinc-300 italic truncate max-w-xs">${props.reason || ''}</span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 font-bold rounded-full uppercase" style="background-color:${arg.event.backgroundColor}20; color:${arg.event.backgroundColor};">
                                ${props.status || 'CITA'}
                            </span>
                        </div>
                    `
                };
            }

            if (isMonth) {
                return {
                    html: `
                        <div class="px-1.5 py-0.5 rounded text-[11px] font-semibold truncate flex items-center gap-1 w-full" style="background-color:${arg.event.backgroundColor}25; color:${arg.event.backgroundColor}; border-left: 3px solid ${arg.event.backgroundColor};">
                            <span class="opacity-75">${arg.timeText || ''}</span>
                            <span class="font-bold truncate">${props.mascota || ''}</span>
                        </div>
                    `
                };
            }

            // Vista Semana y Día (timeGrid)
            return {
                html: `
                    <div class="h-full w-full p-1.5 rounded-lg flex flex-col justify-between overflow-hidden text-xs shadow-xs" style="background-color:${arg.event.backgroundColor}18; color: var(--vc-text, #18181b); border-left: 3.5px solid ${arg.event.backgroundColor};">
                        <div class="min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="font-black text-[11px] truncate tracking-tight text-zinc-900 dark:text-white">${props.mascota || ''}</span>
                                <span class="text-[9px] font-bold uppercase px-1 rounded" style="background-color:${arg.event.backgroundColor}30; color:${arg.event.backgroundColor};">
                                    ${props.status ? props.status.substring(0, 4) : ''}
                                </span>
                            </div>
                            <p class="text-[10px] text-zinc-600 dark:text-zinc-300 truncate font-medium">${props.cliente || ''}</p>
                        </div>
                        <div class="text-[9px] text-zinc-500 dark:text-zinc-400 truncate mt-0.5">
                            ${arg.timeText || ''}
                        </div>
                    </div>
                `
            };
        },

        // Drag & drop → reprogramar cita
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

    // Actualizar locale al cambiar idioma
    window.addEventListener('language-changed', (e) => {
        const lang = e.detail?.locale || localStorage.getItem('vc_locale') || 'es';
        calendar.setOption('locale', lang);
    });

    document.addEventListener('livewire:navigating', function cleanup() {
        try { calendar.destroy(); } catch (e) {}
        if (window._vcActiveCalendar === calendar) window._vcActiveCalendar = null;
        document.removeEventListener('livewire:navigating', cleanup);
    }, { once: true });

    return calendar;
};
