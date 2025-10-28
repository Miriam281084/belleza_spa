<?php

namespace Database\Seeders;

use App\Models\Empleado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empleados = [
            [
                'nombre' => 'Carlos',
                'apellido' => 'Pérez',
                'telefono' => '1178901234',
                'email' => 'carlos.perez@bellezaspa.com',
            ],
            [
                'nombre' => 'Lucía',
                'apellido' => 'Sánchez',
                'telefono' => '1189012345',
                'email' => 'lucia.sanchez@bellezaspa.com',
            ],
            [
                'nombre' => 'Valentina',
                'apellido' => 'Romero',
                'telefono' => '1190123456',
                'email' => 'valentina.romero@bellezaspa.com',
            ],
            [
                'nombre' => 'Martín',
                'apellido' => 'Torres',
                'telefono' => '1101234567',
                'email' => 'martin.torres@bellezaspa.com',
            ],
        ];

        foreach ($empleados as $empleado) {
            Empleado::create($empleado);
        }
    }
}
