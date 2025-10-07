<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ClienteSeeder::class,
            EmpleadoSeeder::class,
            ServicioSeeder::class,
            ProductoSeeder::class,
            TurnoSeeder::class,
            VentaSeeder::class,
            PagoSeeder::class,
            NotificacionSeeder::class,
        ]);
    }
}
