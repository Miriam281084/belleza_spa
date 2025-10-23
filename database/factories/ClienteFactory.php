<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    /**

     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'dni' => fake()->unique()->numerify('########'), // 8 dígitos
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
        ];
    }
}