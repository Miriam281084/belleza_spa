<?php

namespace Database\Factories;

use App\Models\Turno;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Turno>
 */
class TurnoFactory extends Factory
{
    protected $model = Turno::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'empleado_id' => Empleado::factory(),
            'servicio_id' => Servicio::factory(),
            'fecha' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'hora' => fake()->time('H:i'),
            'estado' => fake()->randomElement(['pendiente', 'confirmado', 'realizado', 'cancelado']),
            'observaciones' => fake()->optional()->sentence(),
        ];
    }
}
