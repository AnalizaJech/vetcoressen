/**
 * VETCORESSEN - Módulo de calendario para citas con FullCalendar
 * 
 * Se inicializa desde la vista cita-index.blade.php cuando la vista activa es 'calendario'.
 * Carga las citas via Livewire wire y las muestra con colores por estado.
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
        el._vcCalendar.destroy();
    }
    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
        
        // Vista inicial: semana con horarios (mejor UX para clínica)
        initialView: 'timeGridWeek',
        
        // Barra de herramientas superior
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridDay,timeGridWeek,dayGridMonth,listWeek',
        },

        // Botones en español
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Lista',
        },

        // Configuración regional
        locale: document.documentElement.lang || 'es',
        firstDay: 1, // Lunes
        
        // Horarios visibles (rango de atención de la clínica)
        slotMinTime: '07:00:00',
        slotMaxTime: '21:00:00',
        slotDuration: '00:30:00',
        slotLabelFormat: {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        },
        eventTimeFormat: {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
            meridiem: 'short'
        },
        
        // Configuración visual
        allDaySlot: false,
        nowIndicator: true,
        navLinks: true, // Mantener funcionalidad para ir al día
        height: 'auto',
        contentHeight: 600,
        dayMaxEvents: 3,
        
        // Drag & drop habilitado
        editable: true,
        droppable: false,
        eventDurationEditable: false,
        
        // Estirar eventos y evitar solapamiento
        slotEventOverlap: false,
        eventMinHeight: 90,
        
        selectable: true,
        select: function(info) {
            const dateObj = info.start;
            const dateStr = dateObj.toLocaleDateString('en-CA'); // YYYY-MM-DD
            const timeStr = dateObj.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
            window.location.href = `/appointments/create?fecha=${dateStr}&hora=${timeStr}`;
        },

        // Cargar eventos desde Livewire filtrados por rango visible
        events: function (fetchInfo, successCallback, failureCallback) {
            wire.getCitasCalendario(
                fetchInfo.startStr,
                fetchInfo.endStr
            ).then(events => {
                successCallback(events);
            }).catch(err => {
                console.error('Error cargando citas:', err);
                failureCallback(err);
            });
        },

        // Click en evento → abrir modal "Ver" de Livewire
        eventClick: function (info) {
            const id = parseInt(info.event.id);
            if (id) {
                wire.ver(id).then(() => {
                    if (typeof Flux !== 'undefined') {
                        Flux.modal('ver-cita').show();
                    } else {
                        // Fallback por si no encuentra Flux (ej: versiones anteriores)
                        const editUrl = info.event.extendedProps.editUrl;
                        if (editUrl) window.location.href = editUrl;
                    }
                });
            }
        },

        // Hook para cuando cambia el rango de fechas o la vista (ideal para limpiar tooltips nativos feos)
        datesSet: function() {
            setTimeout(() => {
                document.querySelectorAll('.fc-list-day-text, .fc-list-day-side-text').forEach(el => {
                    if (el.tagName === 'A') {
                        el.removeAttribute('title');
                    }
                });
            }, 100);
        },

        // Renderizado personalizado del contenido de la cita
        eventContent: function(arg) {
            const props = arg.event.extendedProps;
            const isList = arg.view.type.includes('list');
            const isMonth = arg.view.type === 'dayGridMonth';
            
            if (isList) {
                const isEmergencia = props.status === 'EMERGENCIA';
                let formattedTime = 'Todo el día';
                if (!arg.event.allDay && arg.event.start) {
                    formattedTime = arg.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: true});
                }

                return {
                    html: `
                        <div class="flex items-center justify-between w-full p-3 py-4">
                            <div class="flex flex-col gap-1.5">
                                <span class="font-bold text-sm text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                    ${props.mascota} 
                                    <span class="text-[11px] font-medium text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-full border border-zinc-200 dark:border-zinc-700 shadow-sm flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">person</span>
                                        ${props.cliente}
                                    </span>
                                </span>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-[11.5px] text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 px-2.5 py-0.5 rounded-full flex items-center gap-1 border border-transparent dark:border-zinc-700/50">
                                        <span class="material-symbols-outlined text-[14px] opacity-80">schedule</span> 
                                        ${formattedTime}
                                    </span>
                                    <span class="text-xs text-zinc-600 dark:text-zinc-400 flex items-center gap-1 font-medium">
                                        <span class="material-symbols-outlined text-[14px] ${isEmergencia ? 'text-red-500' : 'text-emerald-500'}">${isEmergencia ? 'emergency' : 'medical_information'}</span> 
                                        ${props.reason || 'Consulta General'}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] px-2.5 py-1.5 font-bold rounded-xl uppercase border shadow-sm flex items-center gap-1" style="background-color:${arg.event.backgroundColor}15; color:${arg.event.backgroundColor}; border-color:${arg.event.backgroundColor}30">
                                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background-color:${arg.event.backgroundColor}"></span>
                                    ${props.status}
                                </span>
                            </div>
                        </div>
                    `
                };
            }

            if (isMonth) {
                return {
                    html: `
                        <div class="p-1 px-1.5 flex flex-col leading-tight rounded w-full border" style="background-color:${arg.event.backgroundColor}15; color:${arg.event.backgroundColor}; border-color:${arg.event.borderColor}40;">
                            <div class="font-extrabold text-[10px] truncate flex items-center justify-between">
                                <span>${arg.timeText} ${props.mascota}</span>
                            </div>
                        </div>
                    `
                };
            }

            return {
                html: `
                    <div class="p-2 flex flex-col h-auto min-h-full leading-tight">
                        <div class="flex justify-between items-start mb-2 gap-2 border-b border-black/10 pb-2">
                            <div class="flex items-center gap-1.5 font-bold text-[10px] uppercase tracking-wide opacity-90 truncate">
                                <span class="material-symbols-outlined text-[14px]">${props.status === 'EMERGENCIA' ? 'emergency' : (props.status === 'PENDIENTE' ? 'schedule' : (props.status === 'CONFIRMADA' ? 'check_circle' : 'pets'))}</span>
                                <span class="truncate">${props.status}</span>
                            </div>
                        </div>
                        <div class="font-extrabold text-xs mb-1.5 whitespace-normal break-words">${props.mascota}</div>
                        <div class="text-[10px] opacity-90 font-medium whitespace-normal break-words">${props.cliente}</div>
                        ${arg.view.type.includes('timeGrid') ? `<div class="text-[10px] mt-1 pt-1 opacity-75 border-t border-black/10 whitespace-normal break-words line-clamp-2">${props.reason}</div>` : ''}
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
                // Si falla, revertir posición
                info.revert();
            });
        },

        // Tooltip customizado y mejorado al pasar el mouse
        eventDidMount: function (info) {
            const props = info.event.extendedProps;
            const dateStr = info.event.start.toLocaleDateString('es-ES');
            const timeStr = info.event.start.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit', hour12: true});
            
            // Crear el tooltip de forma dinámica
            let tooltip = document.createElement('div');
            tooltip.className = 'vc-calendar-tooltip hidden fixed z-[9999] bg-zinc-900 dark:bg-zinc-800 text-white border border-zinc-700 p-4 rounded-xl shadow-2xl text-xs w-72 opacity-0 transition-opacity duration-200 pointer-events-none';
            tooltip.innerHTML = `
                <div class="font-bold mb-3 border-b border-zinc-700 pb-2 text-emerald-400 uppercase tracking-wider flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">info</span> Detalles de Cita
                </div>
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between"><span class="text-zinc-400">Fecha/Hora:</span> <span class="text-zinc-200 font-medium">${dateStr} ${timeStr}</span></div>
                    <div class="flex justify-between"><span class="text-zinc-400">Estado:</span> <span class="font-semibold text-white px-2 py-0.5 rounded-full bg-zinc-700/50">${props.status}</span></div>
                    <div class="flex justify-between"><span class="text-zinc-400">Mascota:</span> <span class="font-bold text-vc-primary-light">${props.mascota}</span></div>
                    <div class="flex justify-between"><span class="text-zinc-400">Cliente:</span> <span class="text-zinc-200 truncate ml-2 font-medium">${props.cliente}</span></div>
                    <div class="flex justify-between"><span class="text-zinc-400">Doctor:</span> <span class="text-zinc-200 truncate ml-2">${props.veterinario || '-'}</span></div>
                    <div class="mt-2 pt-2 border-t border-zinc-700/50">
                        <span class="text-zinc-400 block mb-1">Motivo:</span>
                        <span class="text-zinc-300 italic line-clamp-3 leading-snug">${props.reason}</span>
                    </div>
                </div>
            `;
            document.body.appendChild(tooltip);

            // Mostrar y ocultar
            info.el.addEventListener('mouseenter', () => {
                tooltip.classList.remove('hidden');
                setTimeout(() => tooltip.classList.remove('opacity-0'), 10);
            });
            info.el.addEventListener('mousemove', (e) => {
                let x = e.clientX + 15;
                let y = e.clientY + 15;
                // Evitar que el tooltip se salga de la pantalla
                if (x + 300 > window.innerWidth) x = e.clientX - 300;
                if (y + 200 > window.innerHeight) y = e.clientY - 210;
                
                tooltip.style.left = x + 'px';
                tooltip.style.top = y + 'px';
            });
            info.el.addEventListener('mouseleave', () => {
                tooltip.classList.add('opacity-0');
                setTimeout(() => tooltip.classList.add('hidden'), 200);
            });
            
            // Guardar referencia
            info.event.setProp('extendedProps', { ...props, _tooltip: tooltip });
        },
        eventWillUnmount: function (info) {
            // Limpiar tooltip del DOM al destruir el evento
            const tooltip = info.event.extendedProps._tooltip;
            if (tooltip && tooltip.parentNode) {
                tooltip.parentNode.removeChild(tooltip);
            }
        },
    });

    calendar.render();

    // Guardar referencia para cleanup
    el._vcCalendar = calendar;
    window._vcActiveCalendar = calendar;
    document.addEventListener('livewire:navigating', function cleanup() {
        try { calendar.destroy(); } catch (e) {}
        if (window._vcActiveCalendar === calendar) window._vcActiveCalendar = null;
        document.removeEventListener('livewire:navigating', cleanup);
    }, { once: true });

    return calendar;
};
