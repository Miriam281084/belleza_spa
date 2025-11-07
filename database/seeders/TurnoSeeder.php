<?php

namespace Database\Seeders;

use App\Models\Turno;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TurnoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usar fechas dinámicas (próximos días)
        $hoy = now();

        $turnos = [
            [
                'cliente_id' => 1,
                'empleado_id' => 2,
                'servicio_id' => 1,
                'fecha' => $hoy->copy()->addDays(1)->format('Y-m-d'),
                'hora' => '10:00:00',
                'estado' => 'confirmado',
                'observaciones' => 'Cliente prefiere productos sin fragancia',
            ],
            [
                'cliente_id' => 2,
                'empleado_id' => 3,
                'servicio_id' => 2,
                'fecha' => $hoy->copy()->addDays(1)->format('Y-m-d'),
                'hora' => '14:00:00',
                'estado' => 'confirmado',
                'observaciones' => null,
            ],
            [
                'cliente_id' => 3,
                'empleado_id' => 2,
                'servicio_id' => 4,
                'fecha' => $hoy->copy()->addDays(2)->format('Y-m-d'),
                'hora' => '11:00:00',
                'estado' => 'pendiente',
                'observaciones' => null,
            ],
            [
                'cliente_id' => 4,
                'empleado_id' => 3,
                'servicio_id' => 5,
                'fecha' => $hoy->copy()->addDays(2)->format('Y-m-d'),
                'hora' => '15:30:00',
                'estado' => 'confirmado',
                'observaciones' => 'Primera vez en el spa',
            ],
            [
                'cliente_id' => 5,
                'empleado_id' => 2,
                'servicio_id' => 6,
                'fecha' => $hoy->copy()->addDays(3)->format('Y-m-d'),
                'hora' => '09:00:00',
                'estado' => 'confirmado',
                'observaciones' => null,
            ],
            [
                'cliente_id' => 1,
                'empleado_id' => 3,
                'servicio_id' => 8,
                'fecha' => $hoy->copy()->addDays(3)->format('Y-m-d'),
                'hora' => '16:00:00',
                'estado' => 'pendiente',
                'observaciones' => 'Dolor en zona cervical',
            ],
            [
                'cliente_id' => 2,
                'empleado_id' => 2,
                'servicio_id' => 3,
                'fecha' => $hoy->copy()->addDays(4)->format('Y-m-d'),
                'hora' => '10:30:00',
                'estado' => 'confirmado',
                'observaciones' => null,
            ],
            [
                'cliente_id' => 3,
                'empleado_id' => 3,
                'servicio_id' => 7,
                'fecha' => $hoy->copy()->subDays(5)->format('Y-m-d'),
                'hora' => '13:00:00',
                'estado' => 'completado',
                'observaciones' => 'Excelente resultado',
            ],
            // Agregar un turno para el usuario cliente de prueba (cliente_id 6)
            [
                'cliente_id' => 6, // Ana López (cliente@belleza.com)
                'empleado_id' => 2,
                'servicio_id' => 1,
                'fecha' => $hoy->copy()->addDays(5)->format('Y-m-d'),
                'hora' => '11:00:00',
                'estado' => 'pendiente',
                'observaciones' => 'Primer turno desde el sistema',
            ],
        ];

        foreach ($turnos as $turno) {
            Turno::create($turno);
        }
    }
}
