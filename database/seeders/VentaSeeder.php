<?php

namespace Database\Seeders;

use App\Models\Venta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ventas = [
            [
                'cliente_id' => 1,
                'fecha' => '2025-10-05 11:30:00',
                'monto_total' => 9000.00,
                'metodo_pago' => 'tarjeta',
            ],
            [
                'cliente_id' => 2,
                'fecha' => '2025-10-06 15:45:00',
                'monto_total' => 5500.00,
                'metodo_pago' => 'efectivo',
            ],
            [
                'cliente_id' => 3,
                'fecha' => '2025-10-06 17:20:00',
                'monto_total' => 7300.00,
                'metodo_pago' => 'tarjeta',
            ],
            [
                'cliente_id' => 4,
                'fecha' => '2025-10-07 10:15:00',
                'monto_total' => 4500.00,
                'metodo_pago' => 'transferencia',
            ],
        ];

        foreach ($ventas as $ventaData) {
            $venta = Venta::create($ventaData);

            // Agregar productos a la venta usando la tabla pivote
            if ($venta->id == 1) {
                $venta->productos()->attach([
                    1 => ['cantidad' => 2, 'precio_unitario' => 3500.00],
                    4 => ['cantidad' => 1, 'precio_unitario' => 3200.00],
                ]);
            } elseif ($venta->id == 2) {
                $venta->productos()->attach([
                    2 => ['cantidad' => 1, 'precio_unitario' => 5500.00],
                ]);
            } elseif ($venta->id == 3) {
                $venta->productos()->attach([
                    5 => ['cantidad' => 1, 'precio_unitario' => 4000.00],
                    3 => ['cantidad' => 1, 'precio_unitario' => 2800.00],
                    6 => ['cantidad' => 1, 'precio_unitario' => 1500.00],
                ]);
            } elseif ($venta->id == 4) {
                $venta->productos()->attach([
                    7 => ['cantidad' => 1, 'precio_unitario' => 2500.00],
                    8 => ['cantidad' => 1, 'precio_unitario' => 2900.00],
                ]);
            }
        }
    }
}
