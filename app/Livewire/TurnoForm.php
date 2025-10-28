<?php

namespace App\Livewire;

use App\Models\Turno;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;

class TurnoForm extends Component
{
    public $turnoId;
    public $fecha;
    public $hora;
    public $cliente_id;
    public $empleado_id;
    public $servicio_id;
    public $estado = 'pendiente';
    public $observaciones;
    public $modalAbierto = false;
    public $duracionServicio = 0;

    public $clientes = [];
    public $empleados = [];
    public $servicios = [];

    public function mount()
    {
        $this->cargarDatos();
    }

    private function cargarDatos()
    {
        $this->clientes = Cliente::orderBy('nombre')->get();
        $this->empleados = Empleado::orderBy('nombre')->get();
        $this->servicios = Servicio::orderBy('nombre')->get();
    }

    public function render()
    {
        return view('livewire.turno-form');
    }

    #[On('abrirFormularioTurno')]
    public function abrirFormulario($turnoId = null, $fecha = null, $hora = null)
    {
        $this->resetearFormulario();
        $this->modalAbierto = true;

        if ($turnoId) {
            $this->cargarTurno($turnoId);
        } else {
            $this->fecha = $fecha;
            $this->hora = $hora;
        }
    }

    private function cargarTurno($turnoId)
    {
        $turno = Turno::with(['cliente', 'empleado', 'servicio'])->findOrFail($turnoId);

        $this->turnoId = $turno->id;
        $this->fecha = $turno->fecha->format('Y-m-d');
        $this->hora = $turno->hora;
        $this->cliente_id = $turno->cliente_id;
        $this->empleado_id = $turno->empleado_id;
        $this->servicio_id = $turno->servicio_id;
        $this->estado = $turno->estado;
        $this->observaciones = $turno->observaciones;
        $this->duracionServicio = $turno->servicio->duracion;
    }

    public function updatedServicioId($value)
    {
        if ($value) {
            $servicio = Servicio::find($value);
            $this->duracionServicio = $servicio ? $servicio->duracion : 0;
        } else {
            $this->duracionServicio = 0;
        }
    }

    private function resetearFormulario()
    {
        $this->turnoId = null;
        $this->fecha = null;
        $this->hora = null;
        $this->cliente_id = null;
        $this->empleado_id = null;
        $this->servicio_id = null;
        $this->estado = 'pendiente';
        $this->observaciones = null;
        $this->duracionServicio = 0;
        $this->resetValidation();
    }

    public function guardar()
    {
        $this->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required',
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'servicio_id' => 'required|exists:servicios,id',
            'estado' => 'required|in:pendiente,confirmado,realizado,cancelado',
            'observaciones' => 'nullable|string',
        ]);

        // Verificar disponibilidad
        $disponible = $this->verificarDisponibilidad(
            $this->fecha,
            $this->hora,
            $this->empleado_id,
            $this->servicio_id,
            $this->turnoId
        );

        if (!$disponible['disponible']) {
            $this->addError('hora', $disponible['mensaje']);
            return;
        }

        $datos = [
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'cliente_id' => $this->cliente_id,
            'empleado_id' => $this->empleado_id,
            'servicio_id' => $this->servicio_id,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
        ];

        if ($this->turnoId) {
            $turno = Turno::findOrFail($this->turnoId);
            $turno->update($datos);
            $this->dispatch('mostrarMensaje', mensaje: 'Turno actualizado exitosamente.');
        } else {
            Turno::create($datos);
            $this->dispatch('mostrarMensaje', mensaje: 'Turno creado exitosamente.');
        }

        $this->cerrarModal();
        $this->dispatch('turnoGuardado');
    }

    private function verificarDisponibilidad($fecha, $hora, $empleadoId, $servicioId, $turnoIdExcluir = null)
    {
        // Obtener duración del servicio
        $servicio = Servicio::find($servicioId);
        if (!$servicio) {
            return ['disponible' => false, 'mensaje' => 'Servicio no encontrado.'];
        }

        $duracion = $servicio->duracion;

        // Calcular hora de inicio y fin del turno nuevo
        $inicioNuevo = Carbon::parse($fecha . ' ' . $hora);
        $finNuevo = $inicioNuevo->copy()->addMinutes($duracion);

        // Obtener todos los turnos del mismo empleado en la misma fecha (excepto el turno actual si es edición)
        $query = Turno::where('empleado_id', $empleadoId)
            ->where('fecha', $fecha)
            ->where('estado', '!=', 'cancelado');

        if ($turnoIdExcluir) {
            $query->where('id', '!=', $turnoIdExcluir);
        }

        $turnosExistentes = $query->with('servicio')->get();

        // Verificar sobreposición con cada turno existente
        foreach ($turnosExistentes as $turno) {
            $inicioExistente = Carbon::parse($turno->fecha->format('Y-m-d') . ' ' . $turno->hora);
            $finExistente = $inicioExistente->copy()->addMinutes($turno->servicio->duracion);

            // Verificar si hay sobreposición
            // Hay sobreposición si: inicio nuevo < fin existente Y fin nuevo > inicio existente
            if ($inicioNuevo->lt($finExistente) && $finNuevo->gt($inicioExistente)) {
                $mensaje = "El empleado ya tiene un turno agendado de {$inicioExistente->format('H:i')} a {$finExistente->format('H:i')}.";
                return ['disponible' => false, 'mensaje' => $mensaje];
            }
        }

        return ['disponible' => true, 'mensaje' => ''];
    }

    public function eliminar()
    {
        if ($this->turnoId) {
            $turno = Turno::findOrFail($this->turnoId);
            $turno->delete();

            $this->dispatch('mostrarMensaje', mensaje: 'Turno eliminado exitosamente.');
            $this->cerrarModal();
            $this->dispatch('turnoGuardado');
        }
    }

    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->resetearFormulario();
        $this->dispatch('cerrarModal');
    }
}
