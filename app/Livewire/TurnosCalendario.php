<?php

namespace App\Livewire;

use App\Models\Turno;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Carbon\Carbon;

#[Layout('layouts.app')]
class TurnosCalendario extends Component
{
    public $mostrarModal = false;
    public $modoEdicion = false;
    public $turnoIdSeleccionado = null;
    public $fechaSeleccionada = null;
    public $horaSeleccionada = null;

    protected $listeners = ['recargarEventos' => '$refresh'];

    public function render()
    {
        return view('livewire.turnos-calendario');
    }

    public function obtenerEventos()
    {
        $turnos = Turno::with(['cliente', 'empleado', 'servicio'])->get();

        $eventos = [];
        foreach ($turnos as $turno) {
            $inicio = Carbon::parse($turno->fecha->format('Y-m-d') . ' ' . $turno->hora);
            $fin = $inicio->copy()->addMinutes($turno->servicio->duracion);

            $eventos[] = [
                'id' => $turno->id,
                'title' => ($turno->cliente->nombre ?? 'Sin cliente') . ' - ' . ($turno->servicio->nombre ?? 'Sin servicio'),
                'start' => $inicio->toIso8601String(),
                'end' => $fin->toIso8601String(),
                'backgroundColor' => $this->getColorPorEstado($turno->estado),
                'borderColor' => $this->getColorPorEstado($turno->estado),
            ];
        }

        return response()->json($eventos);
    }

    private function getColorPorEstado($estado)
    {
        return match($estado) {
            'pendiente' => '#FCD34D', // amarillo
            'confirmado' => '#34D399', // verde
            'realizado' => '#60A5FA', // azul
            'cancelado' => '#F87171', // rojo
            default => '#9CA3AF', // gris
        };
    }

    public function abrirModalCrear($fecha, $hora)
    {
        $this->fechaSeleccionada = $fecha;
        $this->horaSeleccionada = $hora;
        $this->modoEdicion = false;
        $this->mostrarModal = true;
        $this->dispatch('abrirFormularioTurno', [
            'turnoId' => null,
            'fecha' => $fecha,
            'hora' => $hora
        ]);
    }

    public function abrirModalEditar($turnoId)
    {
        $this->turnoIdSeleccionado = $turnoId;
        $this->modoEdicion = true;
        $this->mostrarModal = true;
        $this->dispatch('abrirFormularioTurno', [
            'turnoId' => $turnoId,
            'fecha' => null,
            'hora' => null
        ]);
    }

    #[On('cerrarModal')]
    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->turnoIdSeleccionado = null;
        $this->fechaSeleccionada = null;
        $this->horaSeleccionada = null;
    }

    #[On('turnoGuardado')]
    public function turnoGuardado()
    {
        $this->dispatch('recargarCalendario');
        $this->cerrarModal();
    }

    public function cambiarEstado($turnoId, $nuevoEstado)
    {
        $turno = Turno::findOrFail($turnoId);
        $turno->estado = $nuevoEstado;
        $turno->save();

        session()->flash('mensaje', 'Estado del turno actualizado.');
        $this->dispatch('recargarCalendario');
    }
}
