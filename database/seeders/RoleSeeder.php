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
            'ver empleados',  // NUEVO: para consultar disponibilidad de esteticistas
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

        // Esteticista - Solo ve su agenda personal y edita sus turnos asignados
        $esteticistaPole = Role::create(['name' => 'Esteticista']);
        $esteticistaPole->givePermissionTo([
            'ver clientes',    // Para ver datos del cliente en su turno
            'ver servicios',   // Para consultar información del servicio
            'ver turnos',      // Solo verá SUS propios turnos (filtrado en el componente)
            'editar turnos',   // Solo puede cambiar estado de SUS turnos
        ]);

        // Cliente - Ver servicios y productos, crear ventas (carrito) y pagos (Mercado Pago)
        $clienteRole = Role::create(['name' => 'Cliente']);
        $clienteRole->givePermissionTo([
            'ver servicios',
            'ver productos',
            'ver ventas',
            'crear ventas',
            'ver pagos',
            'crear pagos',
        ]);
    }
}
