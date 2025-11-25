<?php

namespace App\Livewire;

use App\Models\Empleado;
use App\Models\Turno;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.app')]
class DashboardEsteticista extends Component
{
    public $empleadoActual = null;

    public function mount()
    {
        $user = Auth::user();

        // Solo esteticistas pueden acceder
        if (!$user->hasRole('Esteticista')) {
            $this->redirect(route('dashboard'), navigate: true);
            return;
        }

        // Buscar su perfil de empleado por email
        $this->empleadoActual = Empleado::where('email', $user->email)->first();
    }

    public function cambiarEstado($turnoId, $nuevoEstado)
    {
        try {
            $turno = Turno::findOrFail($turnoId);

            // Verificar que el turno pertenece a esta esteticista
            if ($this->empleadoActual && $turno->empleado_id !== $this->empleadoActual->id) {
                session()->flash('error', 'No puedes modificar turnos de otras esteticistas.');
                return;
            }

            // Validar que el estado sea válido
            $estadosPermitidos = ['pendiente', 'confirmado', 'completado', 'realizado', 'cancelado'];
            if (!in_array($nuevoEstado, $estadosPermitidos)) {
                session()->flash('error', 'Estado no válido.');
                return;
            }

            $turno->estado = $nuevoEstado;
            $turno->save();

            session()->flash('success', 'Estado del turno actualizado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $hoy = now();
        $turnosHoy = collect();
        $estadisticas = [
            'total' => 0,
            'pendientes' => 0,
            'confirmados' => 0,
            'realizados' => 0,
            'cancelados' => 0,
        ];

        if ($this->empleadoActual) {
            // Obtener turnos del día actual para este empleado
            $turnosHoy = Turno::with(['cliente', 'servicio'])
                ->where('empleado_id', $this->empleadoActual->id)
                ->whereDate('fecha', $hoy->toDateString())
                ->orderBy('hora', 'asc')
                ->get();

            // Calcular estadísticas
            $estadisticas['total'] = $turnosHoy->count();
            $estadisticas['pendientes'] = $turnosHoy->where('estado', 'pendiente')->count();
            $estadisticas['confirmados'] = $turnosHoy->where('estado', 'confirmado')->count();
            $estadisticas['completado'] = $turnosHoy->where('estado', 'completado')->count();
            $estadisticas['realizados'] = $turnosHoy->where('estado', 'realizado')->count();
            $estadisticas['cancelados'] = $turnosHoy->where('estado', 'cancelado')->count();
        }

        return view('livewire.dashboard-esteticista', [
            'turnosHoy' => $turnosHoy,
            'estadisticas' => $estadisticas,
            'fechaHoy' => $hoy->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'),
        ]);
    }
}
