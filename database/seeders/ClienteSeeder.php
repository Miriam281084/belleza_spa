<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'María',
                'apellido' => 'González',
                'dni' => '12345678',
                'telefono' => '1123456789',
                'email' => 'maria.gonzalez@email.com',
                'fecha_nacimiento' => '1990-05-15',
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'Martínez',
                'dni' => '23456789',
                'telefono' => '1134567890',
                'email' => 'laura.martinez@email.com',
                'fecha_nacimiento' => '1985-08-22',
            ],
            [
                'nombre' => 'Sofía',
                'apellido' => 'Rodríguez',
                'dni' => '34567890',
                'telefono' => '1145678901',
                'email' => 'sofia.rodriguez@email.com',
                'fecha_nacimiento' => '1992-11-30',
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Fernández',
                'dni' => '45678901',
                'telefono' => '1156789012',
                'email' => 'ana.fernandez@email.com',
                'fecha_nacimiento' => '1988-03-10',
            ],
            [
                'nombre' => 'Carolina',
                'apellido' => 'López',
                'dni' => '56789012',
                'telefono' => '1167890123',
                'email' => 'carolina.lopez@email.com',
                'fecha_nacimiento' => '1995-07-25',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
