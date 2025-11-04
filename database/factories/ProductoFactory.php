<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categorias = ['Cosméticos', 'Cuidado del Cabello', 'Cuidado de la Piel', 'Maquillaje', 'Fragancias'];

        return [
            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->randomFloat(2, 100, 5000),
            'stock' => fake()->numberBetween(0, 100),
            'categoria' => fake()->randomElement($categorias),
        ];
    }

    /**
     * Estado sin stock
     */
    public function sinStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    /**
     * Estado con stock bajo
     */
    public function stockBajo(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(1, 5),
        ]);
    }
}
