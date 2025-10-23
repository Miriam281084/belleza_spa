<?php

namespace Database\Seeders;

use App\Models\User;
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

        // Crear usuario Cliente
        $cliente = User::create([
            'name' => 'Ana López',
            'email' => 'cliente@belleza.com',
            'password' => Hash::make('password'),
        ]);
        $cliente->assignRole('Cliente');
    }
}
