<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Clientes
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'eliminar clientes',

            // Empleados
            'ver empleados',
            'crear empleados',
            'editar empleados',
            'eliminar empleados',

            // Servicios
            'ver servicios',
            'crear servicios',
            'editar servicios',
            'eliminar servicios',

            // Productos
            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',

            // Turnos
            'ver turnos',
            'crear turnos',
            'editar turnos',
            'eliminar turnos',

            // Ventas
            'ver ventas',
            'crear ventas',
            'editar ventas',
            'eliminar ventas',

            // Reportes
            'ver reportes',
            'generar reportes',

            // Configuración
            'acceder configuracion',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos

        // Admin - Acceso total
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Recepcionista - Gestión de clientes, turnos y ventas
        $recepcionistaRole = Role::create(['name' => 'Recepcionista']);
        $recepcionistaRole->givePermissionTo([
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'ver servicios',
            'ver productos',
            'ver turnos',
            'crear turnos',
            'editar turnos',
            'ver ventas',
            'crear ventas',
        ]);

        // Esteticista - Ver turnos y servicios
        $esteticistaPole = Role::create(['name' => 'Esteticista']);
        $esteticistaPole->givePermissionTo([
            'ver clientes',
            'ver servicios',
            'ver productos',
            'ver turnos',
            'editar turnos',
        ]);

        // Cliente - Solo ver sus propios datos
        $clienteRole = Role::create(['name' => 'Cliente']);
        $clienteRole->givePermissionTo([
            'ver servicios',
            'ver productos',
            'ver turnos',
            'crear turnos',
        ]);
    }
}
