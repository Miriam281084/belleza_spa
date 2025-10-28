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
        $turnos = [
            [
                'cliente_id' => 1,
                'empleado_id' => 2,
                'servicio_id' => 1,
                'fecha' => '2025-10-08',
                'hora' => '10:00:00',
                'estado' => 'confirmado',
                'observaciones' => 'Cliente prefiere productos sin fragancia',
            ],
            [
                'cliente_id' => 2,
                'empleado_id' => 3,
                'servicio_id' => 2,
                'fecha' => '2025-10-08',
                'hora' => '14:00:00',
                'estado' => 'confirmado',
                'observaciones' => null,
            ],
            [
                'cliente_id' => 3,
                'empleado_id' => 2,
                'servicio_id' => 4,
                'fecha' => '2025-10-09',
                'hora' => '11:00:00',
                'estado' => 'pendiente',
                'observaciones' => null,
            ],
            [
                'cliente_id' => 4,
                'empleado_id' => 3,
                'servicio_id' => 5,
                'fecha' => '2025-10-09',
                'hora' => '15:30:00',
                'estado' => 'confirmado',
                'observaciones' => 'Primera vez en el spa',
            ],
            [
                'cliente_id' => 5,
                'empleado_id' => 2,
                'servicio_id' => 6,
                'fecha' => '2025-10-10',
                'hora' => '09:00:00',
                'estado' => 'confirmado',
                'observaciones' => null,
            ],
            [
                'cliente_id' => 1,
                'empleado_id' => 3,
                'servicio_id' => 8,
                'fecha' => '2025-10-10',
                'hora' => '16:00:00',
                'estado' => 'pendiente',
                'observaciones' => 'Dolor en zona cervical',
            ],
            [
                'cliente_id' => 2,
                'empleado_id' => 2,
                'servicio_id' => 3,
                'fecha' => '2025-10-11',
                'hora' => '10:30:00',
                'estado' => 'confirmado',
                'observaciones' => null,
            ],
            [
                'cliente_id' => 3,
                'empleado_id' => 3,
                'servicio_id' => 7,
                'fecha' => '2025-10-11',
                'hora' => '13:00:00',
                'estado' => 'completado',
                'observaciones' => 'Excelente resultado',
            ],
        ];

        foreach ($turnos as $turno) {
            Turno::create($turno);
        }
    }
}
