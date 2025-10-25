<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            <!-- Mensaje de éxito -->
            @if (session()->has('mensaje'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('mensaje') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Calendario de Turnos</h2>
            </div>

            <!-- Leyenda de colores -->
            <div class="mb-4 flex flex-wrap gap-4 text-sm">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-300 mr-2"></div>
                    <span>Pendiente</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-400 mr-2"></div>
                    <span>Confirmado</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-blue-400 mr-2"></div>
                    <span>Realizado</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-400 mr-2"></div>
                    <span>Cancelado</span>
                </div>
            </div>

            <!-- Calendario -->
            <div id="calendar" wire:ignore></div>

            <!-- Componente del formulario -->
            @livewire('turno-form')

        </div>
    </div>
</div>

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
        allDaySlot: false,
        editable: false,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,

        // Cargar eventos
        events: function(info, successCallback, failureCallback) {
            fetch('{{ route("turnos.eventos") }}')
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error al cargar eventos:', error);
                    failureCallback(error);
                });
        },

        // Evento al hacer clic en una fecha/hora
        dateClick: function(info) {
            var fecha = info.dateStr.split('T')[0];
            var hora = info.dateStr.split('T')[1] ? info.dateStr.split('T')[1].substring(0, 5) : '09:00';
            @this.call('abrirModalCrear', fecha, hora);
        },

        // Evento al hacer clic en un turno existente
        eventClick: function(info) {
            var turnoId = parseInt(info.event.id);
            @this.call('abrirModalEditar', turnoId);
        }
    });

    calendar.render();

    // Escuchar evento para recargar el calendario
    window.addEventListener('recargarCalendario', function() {
        calendar.refetchEvents();
    });

    // Recargar eventos cuando Livewire actualiza
    Livewire.hook('commit', ({ component, respond }) => {
        respond(() => {
            if (calendar) {
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
}

.fc-event {
    cursor: pointer;
}

.fc-daygrid-event {
    white-space: normal !important;
    align-items: normal !important;
}

.fc-event-title {
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
