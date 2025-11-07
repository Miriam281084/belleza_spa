<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Turno;
use App\Models\Servicio;
use App\Models\Empleado;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MisTurnos extends Component
{
    use WithPagination;

    public $cliente;
    public $showModal = false;
    public $showCancelModal = false;

    // Campos del formulario
    public $servicio_id = '';
    public $empleado_id = '';
    public $fecha = '';
    public $hora = '';
    public $observaciones = '';

    // Para cancelar turno
    public $turnoToCancel = null;

    // Filtros
    public $filtroEstado = 'todos';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        // Buscar o crear el perfil de cliente para el usuario autenticado
        $user = Auth::user();

        // Primero intentar encontrar por user_id
        $this->cliente = Cliente::where('user_id', $user->id)->first();

        // Si no existe, buscar por email coincidente
        if (!$this->cliente) {
            $this->cliente = Cliente::where('email', $user->email)->first();

            // Si existe un cliente con el mismo email, asociarlo
            if ($this->cliente) {
                $this->cliente->update(['user_id' => $user->id]);
            }
        }

        // Si aún no existe, crear perfil de cliente automáticamente
        if (!$this->cliente) {
            $nombreCompleto = explode(' ', $user->name, 2);
            $this->cliente = Cliente::create([
                'user_id' => $user->id,
                'nombre' => $nombreCompleto[0] ?? $user->name,
                'apellido' => $nombreCompleto[1] ?? '',
                'dni' => '', // El usuario puede completarlo después
                'email' => $user->email,
                'telefono' => '',
            ]);
        }

        // Establecer fecha mínima (hoy)
        $this->fecha = now()->format('Y-m-d');
    }

    public function getTurnosProperty()
    {
        $query = Turno::where('cliente_id', $this->cliente->id)
            ->with(['servicio', 'empleado'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc');

        if ($this->filtroEstado !== 'todos') {
            $query->where('estado', $this->filtroEstado);
        }

        return $query->paginate(10);
    }

    public function getEstadisticasProperty()
    {
        return [
            'completados' => Turno::where('cliente_id', $this->cliente->id)
                ->whereIn('estado', ['realizado', 'completado'])
                ->count(),
            'pendientes' => Turno::where('cliente_id', $this->cliente->id)
                ->where('estado', 'pendiente')
                ->where('fecha', '>=', now()->format('Y-m-d'))
                ->count(),
            'confirmados' => Turno::where('cliente_id', $this->cliente->id)
                ->where('estado', 'confirmado')
                ->where('fecha', '>=', now()->format('Y-m-d'))
                ->count(),
        ];
    }

    public function getServiciosProperty()
    {
        return Servicio::orderBy('nombre')->get();
    }

    public function getEmpleadosProperty()
    {
        return Empleado::orderBy('nombre')->get();
    }

    public function abrirModal()
    {
        $this->reset(['servicio_id', 'empleado_id', 'fecha', 'hora', 'observaciones']);
        $this->fecha = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function guardarTurno()
    {
        $this->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'empleado_id' => 'required|exists:empleados,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required',
        ], [
            'servicio_id.required' => 'Debes seleccionar un servicio',
            'empleado_id.required' => 'Debes seleccionar un profesional',
            'fecha.required' => 'La fecha es obligatoria',
            'fecha.after_or_equal' => 'La fecha no puede ser anterior a hoy',
            'hora.required' => 'La hora es obligatoria',
        ]);

        // Verificar disponibilidad del empleado
        $servicio = Servicio::find($this->servicio_id);
        $horaInicio = Carbon::parse($this->fecha . ' ' . $this->hora);
        $horaFin = $horaInicio->copy()->addMinutes($servicio->duracion);

        $turnoSolapado = Turno::where('empleado_id', $this->empleado_id)
            ->where('fecha', $this->fecha)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->where(function($query) use ($horaInicio, $horaFin) {
                $query->whereBetween('hora', [$horaInicio->format('H:i:s'), $horaFin->format('H:i:s')])
                    ->orWhereRaw('? BETWEEN hora AND ADDTIME(hora, SEC_TO_TIME(? * 60))',
                        [$horaInicio->format('H:i:s'), $servicio->duracion]);
            })
            ->exists();

        if ($turnoSolapado) {
            session()->flash('error', 'El profesional no está disponible en ese horario. Por favor selecciona otro horario.');
            return;
        }

        // Crear el turno
        Turno::create([
            'cliente_id' => $this->cliente->id,
            'servicio_id' => $this->servicio_id,
            'empleado_id' => $this->empleado_id,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'estado' => 'pendiente',
            'observaciones' => $this->observaciones,
        ]);

        session()->flash('success', '¡Turno agendado exitosamente! Te enviaremos una confirmación pronto.');
        $this->cerrarModal();
        $this->resetPage();
    }

    public function confirmarCancelar($turnoId)
    {
        $this->turnoToCancel = $turnoId;
        $this->showCancelModal = true;
    }

    public function cancelarTurno()
    {
        if ($this->turnoToCancel) {
            $turno = Turno::find($this->turnoToCancel);

            if ($turno && $turno->cliente_id == $this->cliente->id) {
                // Solo permitir cancelar turnos pendientes o confirmados
                if (in_array($turno->estado, ['pendiente', 'confirmado'])) {
                    // No permitir cancelar turnos dentro de las próximas 24 horas
                    $fechaTurno = Carbon::parse($turno->fecha . ' ' . $turno->hora);
                    if ($fechaTurno->diffInHours(now()) < 24 && $fechaTurno->isFuture()) {
                        session()->flash('error', 'No puedes cancelar un turno con menos de 24 horas de anticipación. Por favor contacta al spa.');
                        $this->showCancelModal = false;
                        return;
                    }

                    $turno->update(['estado' => 'cancelado']);
                    session()->flash('success', 'Turno cancelado exitosamente.');
                } else {
                    session()->flash('error', 'No puedes cancelar este turno.');
                }
            }
        }

        $this->showCancelModal = false;
        $this->turnoToCancel = null;
        $this->resetPage();
    }

    public function cerrarModalCancelar()
    {
        $this->showCancelModal = false;
        $this->turnoToCancel = null;
    }

    public function cambiarFiltro($estado)
    {
        $this->filtroEstado = $estado;
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.mis-turnos', [
            'turnos' => $this->turnos,
            'estadisticas' => $this->estadisticas,
            'servicios' => $this->servicios,
            'empleados' => $this->empleados,
        ])->layout('layouts.app');
    }
}
