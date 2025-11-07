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

            // Turnos (calendario administrativo)
            'ver turnos',
            'crear turnos',
            'editar turnos',
            'eliminar turnos',

            // Ventas
            'ver ventas',
            'crear ventas',
            'editar ventas',
            'eliminar ventas',

            // Pagos
            'ver pagos',
            'crear pagos',
            'editar pagos',
            'eliminar pagos',

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

        // Recepcionista - Gestión de clientes, turnos, ventas y pagos
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
            'ver pagos',
            'crear pagos',
        ]);

        // Esteticista - Ver clientes, servicios, productos y turnos (solo editar turnos asignados)
        $esteticistaPole = Role::create(['name' => 'Esteticista']);
        $esteticistaPole->givePermissionTo([
            'ver clientes',
            'ver servicios',
            'ver productos',
            'ver turnos',
            'editar turnos',
        ]);

        // Cliente - Solo ver servicios y productos (gestiona sus propios turnos sin permisos administrativos)
        $clienteRole = Role::create(['name' => 'Cliente']);
        $clienteRole->givePermissionTo([
            'ver servicios',
            'ver productos',
        ]);
    }
}
