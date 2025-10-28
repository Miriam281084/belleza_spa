<?php

namespace Database\Factories;

use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Servicio>
 */
class ServicioFactory extends Factory
{
    protected $model = Servicio::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->randomElement([
                'Limpieza Facial',
                'Masaje Relajante',
                'Depilación Facial',
                'Tratamiento Capilar',
                'Manicura',
                'Pedicura',
                'Lifting de Pestañas',
                'Microblading',
            ]),
            'descripcion' => fake()->optional()->sentence(10),
            'duracion' => fake()->randomElement([30, 45, 60, 90, 120]),
            'precio' => fake()->randomFloat(2, 500, 5000),
        ];
    }
}
