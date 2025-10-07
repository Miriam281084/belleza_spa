<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Crema Hidratante Facial',
                'descripcion' => 'Crema hidratante de uso diario para todo tipo de piel',
                'precio' => 3500.00,
                'stock' => 25,
                'categoria' => 'Facial',
            ],
            [
                'nombre' => 'Serum Vitamina C',
                'descripcion' => 'Serum antioxidante para luminosidad',
                'precio' => 5500.00,
                'stock' => 15,
                'categoria' => 'Facial',
            ],
            [
                'nombre' => 'Exfoliante Corporal',
                'descripcion' => 'Exfoliante con sales marinas',
                'precio' => 2800.00,
                'stock' => 30,
                'categoria' => 'Corporal',
            ],
            [
                'nombre' => 'Aceite de Masaje Relajante',
                'descripcion' => 'Aceite esencial de lavanda',
                'precio' => 3200.00,
                'stock' => 20,
                'categoria' => 'Masajes',
            ],
            [
                'nombre' => 'Mascarilla Purificante',
                'descripcion' => 'Mascarilla de arcilla para pieles grasas',
                'precio' => 4000.00,
                'stock' => 18,
                'categoria' => 'Facial',
            ],
            [
                'nombre' => 'Esmalte Semipermanente',
                'descripcion' => 'Esmalte de larga duración - Color Rojo',
                'precio' => 1500.00,
                'stock' => 40,
                'categoria' => 'Uñas',
            ],
            [
                'nombre' => 'Kit de Depilación',
                'descripcion' => 'Kit completo con cera y espátulas',
                'precio' => 2500.00,
                'stock' => 12,
                'categoria' => 'Depilación',
            ],
            [
                'nombre' => 'Loción Corporal',
                'descripcion' => 'Loción hidratante post tratamiento',
                'precio' => 2900.00,
                'stock' => 22,
                'categoria' => 'Corporal',
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
