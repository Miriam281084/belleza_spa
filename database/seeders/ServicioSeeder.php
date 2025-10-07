<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            [
                'nombre' => 'Facial Hidratante',
                'descripcion' => 'Tratamiento facial profundo con hidratación intensa',
                'precio' => 5000.00,
                'duracion' => 60,
            ],
            [
                'nombre' => 'Masaje Relajante',
                'descripcion' => 'Masaje corporal completo para aliviar tensiones',
                'precio' => 7000.00,
                'duracion' => 90,
            ],
            [
                'nombre' => 'Depilación Facial',
                'descripcion' => 'Depilación de cejas y labio superior',
                'precio' => 2500.00,
                'duracion' => 30,
            ],
            [
                'nombre' => 'Manicura Completa',
                'descripcion' => 'Manicura con esmaltado semipermanente',
                'precio' => 3500.00,
                'duracion' => 45,
            ],
            [
                'nombre' => 'Pedicura Spa',
                'descripcion' => 'Pedicura con exfoliación e hidratación',
                'precio' => 4000.00,
                'duracion' => 60,
            ],
            [
                'nombre' => 'Lifting de Pestañas',
                'descripcion' => 'Tratamiento para alargar y curvar pestañas',
                'precio' => 6000.00,
                'duracion' => 45,
            ],
            [
                'nombre' => 'Peeling Químico',
                'descripcion' => 'Exfoliación química para renovación celular',
                'precio' => 8000.00,
                'duracion' => 75,
            ],
            [
                'nombre' => 'Masaje Descontracturante',
                'descripcion' => 'Masaje terapéutico para zonas con tensión',
                'precio' => 6500.00,
                'duracion' => 60,
            ],
        ];

        foreach ($servicios as $servicio) {
            Servicio::create($servicio);
        }
    }
}
