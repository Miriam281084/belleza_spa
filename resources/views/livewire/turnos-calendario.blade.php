<div>
    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4 md:p-6">

                <!-- Mensaje de éxito -->
                <div x-data="{ mensaje: '', mostrar: false }"
                     @mostrar-mensaje.window="mensaje = $event.detail.mensaje; mostrar = true; setTimeout(() => mostrar = false, 5000)"
                     x-show="mostrar"
                     x-transition
                     x-cloak
                     class="mb-3 md:mb-4 bg-green-100 border border-green-400 text-green-700 px-3 md:px-4 py-2 md:py-3 rounded relative text-sm md:text-base"
                     role="alert">
                    <span x-text="mensaje"></span>
                    <button @click="mostrar = false" class="absolute top-0 right-0 px-3 md:px-4 py-2 md:py-3">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Calendario de Turnos</h2>
                    <button
                        wire:click="abrirModalCrear('{{ date('Y-m-d') }}', '09:00')"
                        type="button"
                        class="w-full sm:w-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition shadow-md hover:shadow-lg text-sm md:text-base"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nueva Cita
                    </button>
                </div>

                <!-- Leyenda de colores -->
                <div class="mb-3 md:mb-4 flex flex-wrap gap-2 md:gap-4 text-xs md:text-sm">
                    <div class="flex items-center">
                        <div class="w-4 h-4 md:w-6 md:h-6 rounded border-2 flex-shrink-0" style="background-color: #FDE047; border-color: #FACC15;"></div>
                        <span class="ml-1 md:ml-2 font-medium">Pendiente</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 md:w-6 md:h-6 rounded border-2 flex-shrink-0" style="background-color: #4ADE80; border-color: #22C55E;"></div>
                        <span class="ml-1 md:ml-2 font-medium">Confirmado</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 md:w-6 md:h-6 rounded border-2 flex-shrink-0" style="background-color: #60A5FA; border-color: #3B82F6;"></div>
                        <span class="ml-1 md:ml-2 font-medium">Realizado</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 md:w-6 md:h-6 rounded border-2 flex-shrink-0" style="background-color: #FB7185; border-color: #F43F5E;"></div>
                        <span class="ml-1 md:ml-2 font-medium">Cancelado</span>
                    </div>
                </div>

                <!-- Calendario -->
                <div id="calendar" wire:ignore></div>

            </div>
        </div>
    </div>

    <!-- Incluir formulario de turno directamente (sin componente anidado) -->
    @include('livewire.turno-form-partial')

    <!-- Scripts de FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Lista'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        slotDuration: '00:15:00',
        slotLabelInterval: '01:00',
        allDaySlot: false,
        editable: false,
        selectable: true,
        selectMirror: true,
        selectMinDistance: 0,
        unselectAuto: true,
        dayMaxEvents: true,
        weekends: true,
        nowIndicator: true,
        navLinks: true,
        eventStartEditable: false,
        eventDurationEditable: false,

        // Cargar eventos
        events: function(info, successCallback, failureCallback) {
            console.log('Cargando eventos del calendario...');
            fetch('{{ route("turnos.eventos") }}')
                .then(response => response.json())
                .then(data => {
                    console.log('Eventos cargados:', data);
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error al cargar eventos:', error);
                    failureCallback(error);
                });
        },

        // Evento al seleccionar un rango de tiempo (principalmente para vista de semana/día)
        select: function(info) {
            console.log('Select event:', info);
            var fecha = info.startStr.split('T')[0];
            var hora = info.startStr.split('T')[1] ? info.startStr.split('T')[1].substring(0, 5) : '09:00';
            console.log('Abriendo modal con fecha:', fecha, 'hora:', hora);
            @this.call('abrirModalCrear', fecha, hora);
            calendar.unselect(); // Limpiar la selección
        },

        // Evento al hacer clic en una fecha/hora (para vista de mes)
        dateClick: function(info) {
            console.log('DateClick event:', info);
            var fecha = info.dateStr.split('T')[0];
            var hora = info.dateStr.split('T')[1] ? info.dateStr.split('T')[1].substring(0, 5) : '09:00';
            console.log('Abriendo modal con fecha:', fecha, 'hora:', hora);
            @this.call('abrirModalCrear', fecha, hora);
        },

        // Evento al hacer clic en un turno existente
        eventClick: function(info) {
            console.log('EventClick:', info);
            var turnoId = parseInt(info.event.id);
            console.log('Abriendo modal para editar turno ID:', turnoId);
            @this.call('abrirModalEditar', turnoId);
        }
    });

    calendar.render();

    // Escuchar evento para recargar el calendario
    window.addEventListener('recargarCalendario', function() {
        console.log('Recargando calendario...');
        calendar.refetchEvents();
    });

    // Recargar eventos cuando Livewire actualiza
    Livewire.hook('commit', ({ component, respond }) => {
        respond(() => {
            if (calendar) {
                console.log('Livewire commit - recargando eventos');
                calendar.refetchEvents();
            }
        });
    });
});
</script>

<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
    font-size: 14px;
}

/* Responsive: reducir tamaño de fuente en móviles */
@media (max-width: 640px) {
    #calendar {
        font-size: 11px;
    }

    .fc-toolbar-title {
        font-size: 1.1rem !important;
    }

    .fc-button {
        padding: 0.3rem 0.5rem !important;
        font-size: 0.75rem !important;
    }

    .fc-col-header-cell-cushion {
        padding: 2px !important;
    }
}

/* Estilos generales para todos los eventos */
.fc-event {
    cursor: pointer;
    padding: 3px 5px;
    border-radius: 6px;
    border-width: 2px !important;
    border-style: solid !important;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
}

.fc-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Vista de Mes (DayGrid) */
.fc-daygrid-event {
    white-space: normal !important;
    align-items: normal !important;
    margin-bottom: 2px !important;
    padding: 4px 6px !important;
    min-height: 24px;
}

.fc-daygrid-event .fc-event-title {
    font-weight: 600;
    font-size: 0.85em;
    line-height: 1.3;
}

.fc-daygrid-event .fc-event-time {
    font-weight: bold;
    font-size: 0.8em;
    margin-right: 4px;
}

/* Vista de Semana y Día (TimeGrid) */
.fc-timegrid-event {
    border-radius: 6px !important;
    padding: 4px 6px !important;
    border-width: 2px !important;
}

.fc-timegrid-event .fc-event-main {
    padding: 3px !important;
}

.fc-timegrid-event .fc-event-time {
    font-weight: bold;
    font-size: 0.9em;
    display: block;
    margin-bottom: 2px;
}

.fc-timegrid-event .fc-event-title {
    font-size: 0.85em;
    line-height: 1.3;
    white-space: normal;
    word-wrap: break-word;
    font-weight: 500;
}

/* Ajustar el alto de las celdas de tiempo */
.fc-timegrid-slot {
    height: 3em !important;
}

/* Asegurar que el texto tenga buen contraste */
.fc-event-title,
.fc-event-time,
.fc-event-main {
    font-weight: inherit !important;
}

/* Mejorar la apariencia del dot en vista de mes */
.fc-daygrid-dot-event {
    padding: 3px 6px !important;
}

.fc-daygrid-dot-event .fc-event-title {
    font-weight: 500;
}

/* Vista de lista */
.fc-list-event {
    border-left-width: 4px !important;
}

.fc-list-event:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

/* Mejorar el aspecto de las celdas del calendario */
.fc-daygrid-day {
    background-color: #ffffff;
}

.fc-daygrid-day:hover {
    background-color: #f9fafb;
}

/* Estilo para días de fin de semana */
.fc-day-sat,
.fc-day-sun {
    background-color: #f9fafb;
}

/* Hacer los eventos más visibles con opacidad completa */
.fc-event {
    opacity: 1 !important;
}

/* Estilo para el día actual */
.fc-day-today {
    background-color: #eff6ff !important;
}
</style>
</div>
