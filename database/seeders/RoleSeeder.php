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

        // -------------------------
        // PERMISOS DEL SISTEMA
        // -------------------------
        $permissions = [
            // Clientes
            'ver clientes',
            'crear clientes',
            'editar clientes',


            // Empleados
            'ver empleados',
            'crear empleados',
            'editar empleados',


            // Servicios
            'ver servicios',
            'crear servicios',
            'editar servicios',


            // Productos
            'ver productos',
            'crear productos',
            'editar productos',


            // Turnos
            'ver turnos',
            'crear turnos',
            'editar turnos',


            // Ventas
            'ver ventas',
            'crear ventas',
            'editar ventas',


            // Pagos
            'ver pagos',
            'crear pagos',
            'editar pagos',


            // Reportes
            'ver reportes',
            'generar reportes',

            // Configuración
            'acceder configuracion',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // -------------------------
        // ROLES DEL SISTEMA
        // -------------------------

        // ADMIN – Acceso total
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // RECEPCIONISTA – Gestión operativa
        $recepcionistaRole = Role::firstOrCreate(['name' => 'recepcionista']);
        $recepcionistaRole->givePermissionTo([
            'ver clientes',
            'crear clientes',
            'editar clientes',

            'ver empleados',

            'ver servicios',
            'editar servicios',
            'ver productos',
            'editar productos',

            'ver turnos',
            'crear turnos',
            'editar turnos',

            'ver ventas',
            'crear ventas',

            'ver pagos',
            'crear pagos',
        ]);

        // ESTETICISTA – Consulta + uso de agenda propia
        $esteticistaRole = Role::firstOrCreate(['name' => 'esteticista']);
        $esteticistaRole->givePermissionTo([
            'ver clientes',
            'ver servicios',
            'ver productos',
            'ver turnos', // Filtrado por empleado en Livewire
        ]);

        // MASAJISTA – Similar a esteticista pero orientado a terapias corporales
        $masajistaRole = Role::firstOrCreate(['name' => 'masajista']);
        $masajistaRole->givePermissionTo([
            'ver clientes',
            'ver servicios',
            'ver productos',
            'ver turnos', // Filtrado
        ]);

        // MANICURISTA – Servicios de manos/pies
        $manicuristaRole = Role::firstOrCreate(['name' => 'manicurista']);
        $manicuristaRole->givePermissionTo([
            'ver clientes',
            'ver servicios',
            'ver productos',
            'ver turnos', // Filtrado
        ]);

        // CLIENTE – Carrito y pagos
        $clienteRole = Role::firstOrCreate(['name' => 'cliente']);
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
