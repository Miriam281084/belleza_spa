<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Admin',
                'descripcion' => 'Administrador del sistema con acceso completo',
            ],
            [
                'nombre' => 'Recepcionista',
                'descripcion' => 'Encargado de gestionar turnos y atención al cliente',
            ],
            [
                'nombre' => 'Esteticista',
                'descripcion' => 'Profesional que realiza los tratamientos de belleza',
            ],
            [
                'nombre' => 'Cliente',
                'descripcion' => 'Usuario cliente del spa',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
