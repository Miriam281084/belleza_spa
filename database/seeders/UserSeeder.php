<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario Admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@belleza.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Admin');

        // Crear usuario Recepcionista
        $recepcionista = User::create([
            'name' => 'María García',
            'email' => 'recepcionista@belleza.com',
            'password' => Hash::make('password'),
        ]);
        $recepcionista->assignRole('Recepcionista');

        // Crear usuario Esteticista
        $esteticista = User::create([
            'name' => 'Laura Martínez',
            'email' => 'esteticista@belleza.com',
            'password' => Hash::make('password'),
        ]);
        $esteticista->assignRole('Esteticista');

        // Crear usuario Cliente y relacionarlo con un registro de cliente
        $clienteUser = User::create([
            'name' => 'Ana López',
            'email' => 'cliente@belleza.com',
            'password' => Hash::make('password'),
        ]);
        $clienteUser->assignRole('Cliente');

        // Crear perfil de cliente asociado al usuario
        Cliente::create([
            'user_id' => $clienteUser->id,
            'nombre' => 'Ana',
            'apellido' => 'López',
            'dni' => '99999999',
            'telefono' => '+54 11 1234-5678',
            'email' => 'cliente@belleza.com',
            'fecha_nacimiento' => '1995-05-15',
        ]);
    }
}
