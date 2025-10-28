<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Servicio;

class ServiciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            [
                'nombre' => 'Masaje Relajante',
                'descripcion' => 'Masaje de cuerpo completo que ayuda a relajar los músculos y reducir el estrés. Incluye aromaterapia.',
                'duracion' => 60,
                'precio' => 80.00,
            ],
            [
                'nombre' => 'Limpieza Facial',
                'descripcion' => 'Tratamiento facial profundo que incluye limpieza, exfoliación, extracción y mascarilla.',
                'duracion' => 45,
                'precio' => 50.00,
            ],
            [
                'nombre' => 'Depilación Láser',
                'descripcion' => 'Depilación permanente con tecnología láser de última generación. Segura y efectiva.',
                'duracion' => 30,
                'precio' => 120.00,
            ],
            [
                'nombre' => 'Tratamiento Corporal',
                'descripcion' => 'Tratamiento completo que incluye exfoliación corporal, envolturas y masaje hidratante.',
                'duracion' => 90,
                'precio' => 150.00,
            ],
            [
                'nombre' => 'Manicura',
                'descripcion' => 'Servicio de cuidado de manos y uñas que incluye limado, cutícula, masaje y esmaltado.',
                'duracion' => 45,
                'precio' => 30.00,
            ],
        ];

        foreach ($servicios as $servicio) {
            Servicio::create($servicio);
        }
    }
}
