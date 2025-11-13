<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;

class VerProductos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:ver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ver todos los productos y sus imágenes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productos = Producto::all();

        $this->info('=== PRODUCTOS EN LA BASE DE DATOS ===');
        $this->newLine();

        foreach ($productos as $producto) {
            $this->line("ID: {$producto->id}");
            $this->line("Nombre: {$producto->nombre}");
            $this->line("Categoría: {$producto->categoria}");
            $this->line("Imagen: " . ($producto->imagen ?? '❌ SIN IMAGEN'));
            $this->newLine();
        }

        $this->info("Total de productos: {$productos->count()}");

        return 0;
    }
}
