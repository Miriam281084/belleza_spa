<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Crema Hidratante Facial',
                'descripcion' => 'Crema hidratante con ácido hialurónico para todo tipo de piel',
                'precio' => 2500.00,
                'stock' => 15,
                'categoria' => 'Cremas',
            ],
            [
                'nombre' => 'Crema Antiedad Premium',
                'descripcion' => 'Crema antiarrugas con retinol y colágeno',
                'precio' => 3800.00,
                'stock' => 8,
                'categoria' => 'Cremas',
            ],
            [
                'nombre' => 'Aceite de Argán Puro',
                'descripcion' => 'Aceite 100% natural de argán para cabello y piel',
                'precio' => 1200.00,
                'stock' => 20,
                'categoria' => 'Aceites',
            ],
            [
                'nombre' => 'Aceite Esencial de Lavanda',
                'descripcion' => 'Aceite esencial para aromaterapia y relajación',
                'precio' => 800.00,
                'stock' => 25,
                'categoria' => 'Aceites',
            ],
            [
                'nombre' => 'Sérum Vitamina C',
                'descripcion' => 'Sérum concentrado con vitamina C para luminosidad',
                'precio' => 3200.00,
                'stock' => 12,
                'categoria' => 'Cosméticos',
            ],
            [
                'nombre' => 'Mascarilla de Arcilla Verde',
                'descripcion' => 'Mascarilla purificante para piel grasa',
                'precio' => 1500.00,
                'stock' => 3,
                'categoria' => 'Mascarillas',
            ],
            [
                'nombre' => 'Exfoliante Corporal Natural',
                'descripcion' => 'Exfoliante con sales del mar muerto y aceites naturales',
                'precio' => 1800.00,
                'stock' => 10,
                'categoria' => 'Corporales',
            ],
            [
                'nombre' => 'Loción Corporal Hidratante',
                'descripcion' => 'Loción con manteca de karité y aloe vera',
                'precio' => 2000.00,
                'stock' => 18,
                'categoria' => 'Corporales',
            ],
            [
                'nombre' => 'Contorno de Ojos',
                'descripcion' => 'Tratamiento específico para ojeras y bolsas',
                'precio' => 2800.00,
                'stock' => 6,
                'categoria' => 'Cosméticos',
            ],
            [
                'nombre' => 'Protector Solar SPF 50',
                'descripcion' => 'Protección solar facial de amplio espectro',
                'precio' => 2200.00,
                'stock' => 22,
                'categoria' => 'Cosméticos',
            ],
            [
                'nombre' => 'Tónico Facial Refrescante',
                'descripcion' => 'Tónico con agua de rosas y hamamelis',
                'precio' => 1400.00,
                'stock' => 14,
                'categoria' => 'Cosméticos',
            ],
            [
                'nombre' => 'Bálsamo Labial Nutritivo',
                'descripcion' => 'Bálsamo con cera de abejas y aceite de jojoba',
                'precio' => 600.00,
                'stock' => 30,
                'categoria' => 'Cosméticos',
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
