<?php

namespace Tests\Feature;

use App\Livewire\PagosHistorial;
use App\Livewire\PagosRegistrar;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Turno;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Test de Feature para el módulo de Pagos
 *
 * PROPÓSITO: Verificar que el sistema de pagos funcione correctamente
 * incluyendo registro de pagos para turnos y ventas, y visualización del historial.
 */
class PagosTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Cliente $cliente;

    /**
     * Setup: Se ejecuta antes de cada test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->cliente = Cliente::factory()->create([
            'telefono' => '1123456789',
        ]);
    }

    /**
     * Test 1: El componente PagosRegistrar se puede renderizar
     */
    public function test_pagos_registrar_component_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(PagosRegistrar::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.pagos-registrar');
    }

    /**
     * Test 2: Se puede registrar un pago para un turno
     */
    public function test_can_register_payment_for_turno(): void
    {
        $turno = Turno::factory()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $turno->refresh();
        $turno->load('servicio', 'pagos');
        $saldoPendiente = $turno->saldoPendiente();

        $component = Livewire::actingAs($this->user)
            ->test(PagosRegistrar::class)
            ->set('id_referencia', "turno-{$turno->id}")
            ->call('cargarDatos')
            ->set('metodo', 'efectivo')
            ->call('registrarPago')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pagos', [
            'cliente_id' => $this->cliente->id,
            'turno_id' => $turno->id,
            'venta_id' => null,
            'metodo_pago' => 'efectivo',
            'estado' => 'completado',
        ]);

        // Verificar que el monto pagado es correcto
        $pago = Pago::where('turno_id', $turno->id)->first();
        $this->assertEquals(round($saldoPendiente, 2), round($pago->monto, 2));
    }

    /**
     * Test 3: Se puede registrar un pago para una venta
     */
    public function test_can_register_payment_for_venta(): void
    {
        $venta = Venta::factory()->create([
            'cliente_id' => $this->cliente->id,
            'monto_total' => 5000,
        ]);

        $venta->refresh();
        $venta->load('pagos');
        $saldoPendiente = $venta->saldoPendiente();

        Livewire::actingAs($this->user)
            ->test(PagosRegistrar::class)
            ->set('id_referencia', "venta-{$venta->id}")
            ->call('cargarDatos')
            ->set('monto', 2000)
            ->set('metodo', 'tarjeta')
            ->call('registrarPago')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pagos', [
            'cliente_id' => $this->cliente->id,
            'turno_id' => null,
            'venta_id' => $venta->id,
            'metodo_pago' => 'tarjeta',
            'estado' => 'completado',
        ]);

        // Verificar que el pago fue registrado
        $pago = Pago::where('venta_id', $venta->id)->first();
        $this->assertNotNull($pago);
        $this->assertEquals(2000, $pago->monto);
    }

    /**
     * Test 4: No se puede registrar un pago que exceda el saldo pendiente
     */
    public function test_cannot_register_payment_exceeding_pending_balance(): void
    {
        $venta = Venta::factory()->create([
            'cliente_id' => $this->cliente->id,
            'monto_total' => 1000,
        ]);

        Livewire::actingAs($this->user)
            ->test(PagosRegistrar::class)
            ->set('id_referencia', "venta-{$venta->id}")
            ->set('id_cliente', $this->cliente->id)
            ->set('monto', 1500)
            ->set('metodo', 'efectivo')
            ->call('cargarDatos')
            ->call('registrarPago');

        // El pago no debe crearse
        $this->assertDatabaseMissing('pagos', [
            'venta_id' => $venta->id,
            'monto' => 1500,
        ]);
    }

    /**
     * Test 5: Validación de campos requeridos
     */
    public function test_payment_validation_required_fields(): void
    {
        Livewire::actingAs($this->user)
            ->test(PagosRegistrar::class)
            ->set('id_referencia', '')
            ->set('id_cliente', '')
            ->set('monto', '')
            ->set('metodo', '')
            ->call('registrarPago')
            ->assertHasErrors(['id_referencia', 'id_cliente', 'monto', 'metodo']);
    }

    /**
     * Test 6: El modelo Pago calcula correctamente el saldo pendiente de una venta
     */
    public function test_venta_calculates_pending_balance_correctly(): void
    {
        $venta = Venta::factory()->create([
            'cliente_id' => $this->cliente->id,
            'monto_total' => 5000,
        ]);

        // Crear pagos parciales
        Pago::factory()->paraVenta($venta->id)->conMonto(2000)->completado()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        Pago::factory()->paraVenta($venta->id)->conMonto(1500)->completado()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $venta->refresh();

        $this->assertEquals(3500, $venta->totalPagado());
        $this->assertEquals(1500, $venta->saldoPendiente());
        $this->assertFalse($venta->estaPagada());
    }

    /**
     * Test 7: Una venta está marcada como pagada cuando no tiene saldo pendiente
     */
    public function test_venta_is_marked_as_paid_when_no_balance(): void
    {
        $venta = Venta::factory()->create([
            'cliente_id' => $this->cliente->id,
            'monto_total' => 3000,
        ]);

        Pago::factory()->paraVenta($venta->id)->conMonto(3000)->completado()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $venta->refresh();

        $this->assertEquals(3000, $venta->totalPagado());
        $this->assertEquals(0, $venta->saldoPendiente());
        $this->assertTrue($venta->estaPagada());
    }

    /**
     * Test 8: El componente PagosHistorial se puede renderizar
     */
    public function test_pagos_historial_component_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(PagosHistorial::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.pagos-historial');
    }

    /**
     * Test 9: El historial muestra los pagos correctamente
     */
    public function test_historial_displays_payments(): void
    {
        $turno = Turno::factory()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        Pago::factory()->paraTurno($turno->id)->create([
            'cliente_id' => $this->cliente->id,
            'monto' => 1500,
            'metodo_pago' => 'efectivo',
        ]);

        Livewire::actingAs($this->user)
            ->test(PagosHistorial::class)
            ->assertSee('1,500')  // El componente formatea el número con coma
            ->assertSee('efectivo');
    }

    /**
     * Test 10: Se puede filtrar pagos por cliente
     */
    public function test_can_filter_payments_by_client(): void
    {
        $cliente2 = Cliente::factory()->create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
        ]);

        $turno1 = Turno::factory()->create(['cliente_id' => $this->cliente->id]);
        $turno2 = Turno::factory()->create(['cliente_id' => $cliente2->id]);

        Pago::factory()->paraTurno($turno1->id)->create([
            'cliente_id' => $this->cliente->id,
        ]);

        Pago::factory()->paraTurno($turno2->id)->create([
            'cliente_id' => $cliente2->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(PagosHistorial::class)
            ->set('search', 'Juan')
            ->assertSee('Juan')
            ->assertDontSee($this->cliente->nombre);
    }

    /**
     * Test 11: Se pueden filtrar pagos por método de pago
     */
    public function test_can_filter_payments_by_method(): void
    {
        $turno = Turno::factory()->create(['cliente_id' => $this->cliente->id]);

        Pago::factory()->paraTurno($turno->id)->create([
            'cliente_id' => $this->cliente->id,
            'metodo_pago' => 'efectivo',
        ]);

        Pago::factory()->paraTurno($turno->id)->create([
            'cliente_id' => $this->cliente->id,
            'metodo_pago' => 'tarjeta',
        ]);

        Livewire::actingAs($this->user)
            ->test(PagosHistorial::class)
            ->set('metodoFiltro', 'efectivo')
            ->assertSee('efectivo');
    }

    /**
     * Test 12: Pagos pendientes mantienen su estado hasta completarse
     */
    public function test_pending_payments_maintain_state(): void
    {
        $venta = Venta::factory()->create([
            'cliente_id' => $this->cliente->id,
            'monto_total' => 5000,
        ]);

        $pago = Pago::factory()->paraVenta($venta->id)->pendiente()->create([
            'cliente_id' => $this->cliente->id,
            'monto' => 2000,
        ]);

        $this->assertEquals('pendiente', $pago->estado);

        // Verificar que no cuenta para el total pagado
        $this->assertEquals(0, $venta->totalPagado());
        $this->assertEquals(5000, $venta->saldoPendiente());
    }

    /**
     * Test 13: Se pueden registrar múltiples pagos parciales para una venta
     */
    public function test_can_register_multiple_partial_payments_for_venta(): void
    {
        $venta = Venta::factory()->create([
            'cliente_id' => $this->cliente->id,
            'monto_total' => 10000,
        ]);

        // Primer pago
        Pago::factory()->paraVenta($venta->id)->conMonto(3000)->completado()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        // Segundo pago
        Pago::factory()->paraVenta($venta->id)->conMonto(4000)->completado()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        // Tercer pago
        Pago::factory()->paraVenta($venta->id)->conMonto(3000)->completado()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $venta->refresh();

        $this->assertEquals(10000, $venta->totalPagado());
        $this->assertEquals(0, $venta->saldoPendiente());
        $this->assertTrue($venta->estaPagada());
        $this->assertEquals(3, $venta->pagos()->count());
    }

    /**
     * Test 14: El modelo Turno calcula correctamente el saldo pendiente
     */
    public function test_turno_calculates_pending_balance_correctly(): void
    {
        $turno = Turno::factory()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        // El servicio tiene un precio que debería estar en el turno
        $precioServicio = $turno->servicio->precio;

        // Crear un pago parcial
        Pago::factory()->paraTurno($turno->id)->conMonto($precioServicio / 2)->completado()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $turno->refresh();

        $this->assertEquals($precioServicio / 2, $turno->totalPagado());
        $this->assertEquals($precioServicio / 2, $turno->saldoPendiente());
        $this->assertFalse($turno->estaPagado());
    }

    /**
     * Test 15: Un turno está pagado cuando el total de pagos cubre el servicio
     */
    public function test_turno_is_paid_when_payments_cover_service(): void
    {
        $turno = Turno::factory()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $precioServicio = $turno->servicio->precio;

        Pago::factory()->paraTurno($turno->id)->conMonto($precioServicio)->completado()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $turno->refresh();

        $this->assertEquals($precioServicio, $turno->totalPagado());
        $this->assertEquals(0, $turno->saldoPendiente());
        $this->assertTrue($turno->estaPagado());
    }
}
