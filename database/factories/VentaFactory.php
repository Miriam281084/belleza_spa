<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venta>
 */
class VentaFactory extends Factory
{
    protected $model = Venta::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'fecha' => fake()->dateTimeBetween('-3 months', 'now'),
            'monto_total' => fake()->randomFloat(2, 500, 10000),
            'metodo_pago' => fake()->randomElement(['efectivo', 'tarjeta', 'transferencia', 'mercadopago']),
        ];
    }

    /**
     * Venta con monto específico
     */
    public function conMonto(float $monto): static
    {
        return $this->state(fn (array $attributes) => [
            'monto_total' => $monto,
        ]);
    }

    /**
     * Venta con cliente específico
     */
    public function paraCliente(int $clienteId): static
    {
        return $this->state(fn (array $attributes) => [
            'cliente_id' => $clienteId,
        ]);
    }
}
