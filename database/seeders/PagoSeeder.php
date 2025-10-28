<?php

namespace Database\Seeders;

use App\Models\Pago;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pagos = [
            [
                'cliente_id' => 1,
                'turno_id' => 1,
                'monto' => 5000.00,
                'metodo_pago' => 'tarjeta',
                'fecha_pago' => '2025-10-08 10:00:00',
                'estado' => 'completado',
            ],
            [
                'cliente_id' => 2,
                'turno_id' => 2,
                'monto' => 7000.00,
                'metodo_pago' => 'efectivo',
                'fecha_pago' => '2025-10-08 14:00:00',
                'estado' => 'completado',
            ],
            [
                'cliente_id' => 4,
                'turno_id' => 4,
                'monto' => 4000.00,
                'metodo_pago' => 'transferencia',
                'fecha_pago' => '2025-10-09 15:30:00',
                'estado' => 'completado',
            ],
            [
                'cliente_id' => 5,
                'turno_id' => 5,
                'monto' => 6000.00,
                'metodo_pago' => 'tarjeta',
                'fecha_pago' => '2025-10-10 09:00:00',
                'estado' => 'completado',
            ],
            [
                'cliente_id' => 2,
                'turno_id' => 7,
                'monto' => 2500.00,
                'metodo_pago' => 'efectivo',
                'fecha_pago' => '2025-10-11 10:30:00',
                'estado' => 'completado',
            ],
            [
                'cliente_id' => 3,
                'turno_id' => 8,
                'monto' => 8000.00,
                'metodo_pago' => 'tarjeta',
                'fecha_pago' => '2025-10-11 13:00:00',
                'estado' => 'completado',
            ],
        ];

        foreach ($pagos as $pago) {
            Pago::create($pago);
        }
    }
}
