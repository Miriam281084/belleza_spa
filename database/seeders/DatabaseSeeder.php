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
<<<<<<< HEAD
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
=======
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
>>>>>>> f24e2ba2bb9ad6aefdb86e2dad1670bca014d857
        ]);
    }
}
