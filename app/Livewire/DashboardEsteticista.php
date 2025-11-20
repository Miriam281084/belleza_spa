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
