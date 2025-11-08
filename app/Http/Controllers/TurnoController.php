<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TurnoController extends Controller
{
    /**
     * Obtiene los eventos del calendario en formato JSON para FullCalendar
     * Filtra por empleado si el usuario es Esteticista
     */
    public function obtenerEventos()
    {
        $user = Auth::user();
        $query = Turno::with(['cliente', 'empleado', 'servicio']);

        // Si es Esteticista, solo mostrar sus propios turnos
        if ($user->hasRole('Esteticista')) {
            $empleado = Empleado::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if ($empleado) {
                $query->where('empleado_id', $empleado->id);
            } else {
                // Si no tiene perfil de empleado, no mostrar nada
                return response()->json([]);
            }
        }

        $turnos = $query->get();

        $eventos = [];
        foreach ($turnos as $turno) {
            // Combinar fecha y hora correctamente
            $fechaStr = $turno->fecha->format('Y-m-d');
            $inicio = Carbon::parse($fechaStr . ' ' . $turno->hora);
            $fin = $inicio->copy()->addMinutes($turno->servicio->duracion);

            $colores = $this->getColoresPorEstado($turno->estado);

            // Formato compatible con FullCalendar: YYYY-MM-DDTHH:mm:ss
            $eventos[] = [
                'id' => $turno->id,
                'title' => ($turno->cliente->nombre ?? 'Sin cliente') . ' - ' . ($turno->servicio->nombre ?? 'Sin servicio'),
                'start' => $inicio->format('Y-m-d\TH:i:s'),
                'end' => $fin->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $colores['fondo'],
                'borderColor' => $colores['borde'],
                'textColor' => $colores['texto'],
                'extendedProps' => [
                    'cliente' => $turno->cliente->nombre ?? 'Sin cliente',
                    'empleado' => ($turno->empleado->nombre ?? 'Sin empleado') . ' ' . ($turno->empleado->apellido ?? ''),
                    'servicio' => $turno->servicio->nombre ?? 'Sin servicio',
                    'estado' => $turno->estado,
                    'observaciones' => $turno->observaciones,
                ],
            ];
        }

        return response()->json($eventos);
    }

    /**
     * Retorna los colores según el estado del turno
     */
    private function getColoresPorEstado($estado)
    {
        return match($estado) {
            'pendiente' => [
                'fondo' => '#FDE047',     // amarillo más intenso
                'borde' => '#FACC15',     // amarillo oscuro
                'texto' => '#713F12',     // texto oscuro para contraste
            ],
            'confirmado' => [
                'fondo' => '#4ADE80',     // verde más intenso
                'borde' => '#22C55E',     // verde oscuro
                'texto' => '#14532D',     // texto oscuro para contraste
            ],
            'realizado' => [
                'fondo' => '#60A5FA',     // azul
                'borde' => '#3B82F6',     // azul oscuro
                'texto' => '#1E3A8A',     // texto oscuro para contraste
            ],
            'cancelado' => [
                'fondo' => '#FB7185',     // rojo/rosa
                'borde' => '#F43F5E',     // rojo oscuro
                'texto' => '#881337',     // texto oscuro para contraste
            ],
            default => [
                'fondo' => '#D1D5DB',     // gris
                'borde' => '#9CA3AF',     // gris oscuro
                'texto' => '#1F2937',     // texto oscuro
            ],
        };
    }
}
