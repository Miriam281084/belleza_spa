<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Turno;
use App\Models\Venta;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $hoy = now();

        // Métricas del día
        $turnosHoy = Turno::whereDate('fecha', $hoy->toDateString())->count();
        $turnosPendientes = Turno::whereDate('fecha', $hoy->toDateString())
            ->where('estado', 'pendiente')
            ->count();
        $turnosConfirmados = Turno::whereDate('fecha', $hoy->toDateString())
            ->where('estado', 'confirmado')
            ->count();
        $turnosRealizados = Turno::whereDate('fecha', $hoy->toDateString())
            ->where('estado', 'realizado')
            ->count();
        $turnosCancelados = Turno::whereDate('fecha', $hoy->toDateString())
            ->where('estado', 'cancelado')
            ->count();

        // Ingresos
        $ingresosHoy = Pago::whereDate('fecha_pago', $hoy->toDateString())->sum('monto');
        $ingresosSemana = Pago::whereBetween('fecha_pago', [
            $hoy->copy()->startOfWeek(),
            $hoy->copy()->endOfWeek()
        ])->sum('monto');
        $ingresosMes = Pago::whereMonth('fecha_pago', $hoy->month)
            ->whereYear('fecha_pago', $hoy->year)
            ->sum('monto');

        // Productos con stock bajo
        $productosStockBajo = Producto::where('stock', '<', 5)->get();

        // Próximos turnos de hoy (todos los estados, ordenados por hora)
        $proximosTurnos = Turno::with(['cliente', 'servicio', 'empleado'])
            ->whereDate('fecha', $hoy->toDateString())
            ->whereIn('estado', ['pendiente', 'confirmado', 'realizado', 'cancelado'])
            ->orderBy('hora', 'asc')
            ->limit(10)
            ->get();

        // Ventas de hoy
        $ventasHoy = Venta::whereDate('fecha', $hoy->toDateString())->count();
        $montoVentasHoy = Venta::whereDate('fecha', $hoy->toDateString())->sum('monto_total');

        // Clientes nuevos esta semana
        $clientesNuevos = Cliente::whereBetween('created_at', [
            $hoy->copy()->startOfWeek(),
            $hoy->copy()->endOfWeek()
        ])->count();

        // Datos para gráfico de turnos por estado (últimos 7 días)
        $turnosPorDia = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = $hoy->copy()->subDays($i);
            $turnosPorDia[] = [
                'fecha' => $fecha->format('d/m'),
                'cantidad' => Turno::whereDate('fecha', $fecha->toDateString())->count(),
            ];
        }

        // Ingresos por semana (últimos 7 días)
        $ingresosPorDia = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = $hoy->copy()->subDays($i);
            $ingresosPorDia[] = [
                'fecha' => $fecha->format('d/m'),
                'monto' => Pago::whereDate('fecha_pago', $fecha->toDateString())->sum('monto'),
            ];
        }

        return view('livewire.dashboard', [
            'turnosHoy' => $turnosHoy,
            'turnosPendientes' => $turnosPendientes,
            'turnosConfirmados' => $turnosConfirmados,
            'turnosRealizados' => $turnosRealizados,
            'turnosCancelados' => $turnosCancelados,
            'ingresosHoy' => $ingresosHoy,
            'ingresosSemana' => $ingresosSemana,
            'ingresosMes' => $ingresosMes,
            'productosStockBajo' => $productosStockBajo,
            'proximosTurnos' => $proximosTurnos,
            'ventasHoy' => $ventasHoy,
            'montoVentasHoy' => $montoVentasHoy,
            'clientesNuevos' => $clientesNuevos,
            'turnosPorDia' => $turnosPorDia,
            'ingresosPorDia' => $ingresosPorDia,
        ]);
    }
}
