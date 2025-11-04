<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Turno;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pago>
 */
class PagoFactory extends Factory
{
    protected $model = Pago::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'turno_id' => null,
            'venta_id' => null,
            'monto' => fake()->randomFloat(2, 100, 5000),
            'metodo_pago' => fake()->randomElement(['efectivo', 'tarjeta', 'transferencia', 'mercadopago']),
            'fecha_pago' => fake()->dateTimeBetween('-1 month', 'now'),
            'estado' => 'completado',
            'mercadopago_preference_id' => null,
            'mercadopago_payment_id' => null,
        ];
    }

    /**
     * Pago asociado a un turno
     */
    public function paraTurno(?int $turnoId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'turno_id' => $turnoId ?? Turno::factory(),
            'venta_id' => null,
        ]);
    }

    /**
     * Pago asociado a una venta
     */
    public function paraVenta(?int $ventaId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'turno_id' => null,
            'venta_id' => $ventaId ?? Venta::factory(),
        ]);
    }

    /**
     * Pago pendiente
     */
    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pendiente',
        ]);
    }

    /**
     * Pago completado
     */
    public function completado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'completado',
        ]);
    }

    /**
     * Pago con MercadoPago
     */
    public function conMercadoPago(): static
    {
        return $this->state(fn (array $attributes) => [
            'metodo_pago' => 'mercadopago',
            'mercadopago_preference_id' => fake()->uuid(),
            'mercadopago_payment_id' => fake()->numerify('##########'),
        ]);
    }

    /**
     * Pago con monto específico
     */
    public function conMonto(float $monto): static
    {
        return $this->state(fn (array $attributes) => [
            'monto' => $monto,
        ]);
    }
}
