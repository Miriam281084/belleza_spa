<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Notificacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notificacion>
 */
class NotificacionFactory extends Factory
{
    protected $model = Notificacion::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'mensaje' => fake()->sentence(),
            'tipo' => fake()->randomElement(['confirmacion_turno', 'recordatorio_turno', 'promocion', 'general']),
            'fecha_envio' => fake()->dateTimeBetween('-1 week', 'now'),
            'estado' => fake()->randomElement(['enviado', 'pendiente', 'fallido']),
        ];
    }

    /**
     * Notificación de confirmación de turno
     */
    public function confirmacionTurno(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'confirmacion_turno',
            'mensaje' => 'Tu turno ha sido confirmado',
        ]);
    }

    /**
     * Notificación de recordatorio de turno
     */
    public function recordatorioTurno(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'recordatorio_turno',
            'mensaje' => 'Recordatorio: Tienes un turno mañana',
        ]);
    }

    /**
     * Notificación pendiente
     */
    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pendiente',
        ]);
    }

    /**
     * Notificación enviada
     */
    public function enviada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'enviado',
        ]);
    }

    /**
     * Notificación fallida
     */
    public function fallida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'fallido',
        ]);
    }
}
