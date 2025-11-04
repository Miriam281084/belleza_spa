<?php

namespace Tests\Feature;

use App\Livewire\VentasCrear;
use App\Livewire\VentasHistorial;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Test de Feature para el módulo de Ventas
 *
 * PROPÓSITO: Verificar que el sistema de ventas funcione correctamente
 * incluyendo creación de ventas, gestión del carrito, actualización de stock,
 * y visualización del historial.
 */
class VentasTest extends TestCase
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
        $this->cliente = Cliente::factory()->create();
    }

    /**
     * Test 1: El componente VentasCrear se puede renderizar
     */
    public function test_ventas_crear_component_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.ventas-crear');
    }

    /**
     * Test 2: Se puede agregar un producto al carrito
     */
    public function test_can_add_product_to_cart(): void
    {
        $producto = Producto::factory()->create([
            'nombre' => 'Shampoo',
            'precio' => 500,
            'stock' => 10,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->call('agregarProducto', $producto->id);

        $carrito = $component->get('productosEnCarrito');

        $this->assertArrayHasKey($producto->id, $carrito);
        $this->assertEquals($producto->id, $carrito[$producto->id]['id']);
        $this->assertEquals(1, $carrito[$producto->id]['cantidad']);
        $this->assertEquals(500, $component->get('montoTotal'));
    }

    /**
     * Test 3: Se puede remover un producto del carrito
     */
    public function test_can_remove_product_from_cart(): void
    {
        $producto = Producto::factory()->create(['stock' => 10]);

        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->call('agregarProducto', $producto->id)
            ->assertCount('productosEnCarrito', 1)
            ->call('removerProducto', $producto->id)
            ->assertCount('productosEnCarrito', 0)
            ->assertSet('montoTotal', 0);
    }

    /**
     * Test 4: Se puede vaciar el carrito
     */
    public function test_can_clear_cart(): void
    {
        $producto1 = Producto::factory()->create(['stock' => 10]);
        $producto2 = Producto::factory()->create(['stock' => 10]);

        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->call('agregarProducto', $producto1->id)
            ->call('agregarProducto', $producto2->id)
            ->assertCount('productosEnCarrito', 2)
            ->call('vaciarCarrito')
            ->assertCount('productosEnCarrito', 0);
    }

    /**
     * Test 5: Se puede actualizar la cantidad de un producto en el carrito
     */
    public function test_can_update_product_quantity_in_cart(): void
    {
        $producto = Producto::factory()->create([
            'precio' => 1000,
            'stock' => 10,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->call('agregarProducto', $producto->id)
            ->call('actualizarCantidad', $producto->id, 3);

        $carrito = $component->get('productosEnCarrito');
        $this->assertEquals(3, $carrito[$producto->id]['cantidad']);
        $this->assertEquals(3000, $component->get('montoTotal'));
    }

    /**
     * Test 6: No se puede agregar más productos que el stock disponible
     */
    public function test_cannot_add_more_products_than_stock(): void
    {
        $producto = Producto::factory()->create([
            'precio' => 500,
            'stock' => 2,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->call('agregarProducto', $producto->id)
            ->call('actualizarCantidad', $producto->id, 5);

        // Debería mantener la cantidad en 2 (stock máximo)
        $carrito = $component->get('productosEnCarrito');
        $this->assertLessThanOrEqual(2, $carrito[$producto->id]['cantidad']);
    }

    /**
     * Test 7: Se puede procesar una venta correctamente
     */
    public function test_can_process_sale(): void
    {
        $producto = Producto::factory()->create([
            'precio' => 1500,
            'stock' => 10,
        ]);

        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->set('id_cliente', $this->cliente->id)
            ->call('agregarProducto', $producto->id)
            ->call('actualizarCantidad', $producto->id, 2)
            ->call('procesarVenta')
            ->assertHasNoErrors();

        // Verificar que la venta se creó
        $this->assertDatabaseHas('ventas', [
            'cliente_id' => $this->cliente->id,
            'monto_total' => 3000,
        ]);

        // Verificar que el detalle de venta se creó
        $venta = Venta::where('cliente_id', $this->cliente->id)->first();
        $this->assertDatabaseHas('detalle_venta', [
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio_unitario' => 1500,
        ]);

        // Verificar que el stock se actualizó
        $producto->refresh();
        $this->assertEquals(8, $producto->stock);
    }

    /**
     * Test 8: No se puede procesar una venta sin cliente
     */
    public function test_cannot_process_sale_without_client(): void
    {
        $producto = Producto::factory()->create(['stock' => 10]);

        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->set('id_cliente', null)
            ->call('agregarProducto', $producto->id)
            ->call('procesarVenta')
            ->assertHasErrors(['id_cliente']);
    }

    /**
     * Test 9: No se puede procesar una venta sin productos
     */
    public function test_cannot_process_sale_without_products(): void
    {
        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->set('id_cliente', $this->cliente->id)
            ->call('procesarVenta');

        // No debe crear ninguna venta
        $this->assertEquals(0, Venta::count());
    }

    /**
     * Test 10: El componente VentasHistorial se puede renderizar
     */
    public function test_ventas_historial_component_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(VentasHistorial::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.ventas-historial');
    }

    /**
     * Test 11: El historial muestra las ventas correctamente
     */
    public function test_historial_displays_sales(): void
    {
        $venta = Venta::factory()->create([
            'cliente_id' => $this->cliente->id,
            'monto_total' => 5000,
            'fecha' => now(), // Usar fecha actual para que esté en el rango del filtro
        ]);

        Livewire::actingAs($this->user)
            ->test(VentasHistorial::class)
            ->assertSee($this->cliente->nombre)
            ->assertSee('5,000'); // El componente formatea el número con coma
    }

    /**
     * Test 12: Se puede filtrar ventas por cliente
     */
    public function test_can_filter_sales_by_client(): void
    {
        $cliente2 = Cliente::factory()->create([
            'nombre' => 'María',
            'apellido' => 'González',
        ]);

        Venta::factory()->create([
            'cliente_id' => $this->cliente->id,
            'fecha' => now()
        ]);
        Venta::factory()->create([
            'cliente_id' => $cliente2->id,
            'fecha' => now()
        ]);

        Livewire::actingAs($this->user)
            ->test(VentasHistorial::class)
            ->set('search', 'María')
            ->assertSee('María')
            ->assertDontSee($this->cliente->nombre);
    }

    /**
     * Test 13: Se puede ver el detalle de una venta
     */
    public function test_can_view_sale_detail(): void
    {
        $producto = Producto::factory()->create([
            'nombre' => 'Crema Facial',
            'precio' => 2000,
        ]);

        $venta = Venta::factory()->create([
            'cliente_id' => $this->cliente->id,
            'monto_total' => 4000,
            'fecha' => now()
        ]);

        $venta->productos()->attach($producto->id, [
            'cantidad' => 2,
            'precio_unitario' => 2000,
        ]);

        Livewire::actingAs($this->user)
            ->test(VentasHistorial::class)
            ->call('verDetalle', $venta->id)
            ->assertSet('mostrarDetalle', true)
            ->assertSee('Crema Facial')
            ->assertSee('2,000'); // El componente formatea el número con coma
    }

    /**
     * Test 14: El monto total se calcula correctamente con múltiples productos
     */
    public function test_total_amount_calculated_correctly_with_multiple_products(): void
    {
        $producto1 = Producto::factory()->create(['precio' => 500, 'stock' => 10]);
        $producto2 = Producto::factory()->create(['precio' => 1000, 'stock' => 10]);
        $producto3 = Producto::factory()->create(['precio' => 1500, 'stock' => 10]);

        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->call('agregarProducto', $producto1->id)
            ->call('actualizarCantidad', $producto1->id, 2)
            ->call('agregarProducto', $producto2->id)
            ->call('actualizarCantidad', $producto2->id, 1)
            ->call('agregarProducto', $producto3->id)
            ->call('actualizarCantidad', $producto3->id, 3)
            ->assertSet('montoTotal', 6500); // (500*2) + (1000*1) + (1500*3)
    }

    /**
     * Test 15: La venta mantiene relación correcta con productos
     */
    public function test_sale_maintains_correct_relationship_with_products(): void
    {
        $producto1 = Producto::factory()->create(['precio' => 800, 'stock' => 10]);
        $producto2 = Producto::factory()->create(['precio' => 1200, 'stock' => 10]);

        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->set('id_cliente', $this->cliente->id)
            ->call('agregarProducto', $producto1->id)
            ->call('agregarProducto', $producto2->id)
            ->call('procesarVenta');

        $venta = Venta::where('cliente_id', $this->cliente->id)->first();

        $this->assertCount(2, $venta->productos);
        $this->assertEquals($producto1->id, $venta->productos[0]->id);
        $this->assertEquals($producto2->id, $venta->productos[1]->id);
    }

    /**
     * Test 16: El stock se reduce correctamente después de múltiples ventas
     */
    public function test_stock_reduces_correctly_after_multiple_sales(): void
    {
        $producto = Producto::factory()->create([
            'precio' => 1000,
            'stock' => 20,
        ]);

        // Primera venta
        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->set('id_cliente', $this->cliente->id)
            ->call('agregarProducto', $producto->id)
            ->call('actualizarCantidad', $producto->id, 5)
            ->call('procesarVenta');

        $producto->refresh();
        $this->assertEquals(15, $producto->stock);

        // Segunda venta
        $cliente2 = Cliente::factory()->create();
        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->set('id_cliente', $cliente2->id)
            ->call('agregarProducto', $producto->id)
            ->call('actualizarCantidad', $producto->id, 7)
            ->call('procesarVenta');

        $producto->refresh();
        $this->assertEquals(8, $producto->stock);
    }

    /**
     * Test 17: Se puede buscar productos en el componente de crear venta
     */
    public function test_can_search_products_in_create_sale(): void
    {
        $producto1 = Producto::factory()->create(['nombre' => 'Shampoo Especial']);
        $producto2 = Producto::factory()->create(['nombre' => 'Crema Hidratante']);

        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->set('buscarProducto', 'Shampoo')
            ->assertSee('Shampoo Especial')
            ->assertDontSee('Crema Hidratante');
    }

    /**
     * Test 18: Una venta mantiene el precio del producto al momento de la venta
     */
    public function test_sale_maintains_product_price_at_sale_time(): void
    {
        $producto = Producto::factory()->create([
            'precio' => 1000,
            'stock' => 10,
        ]);

        Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->set('id_cliente', $this->cliente->id)
            ->call('agregarProducto', $producto->id)
            ->call('procesarVenta');

        // Cambiar el precio del producto después de la venta
        $producto->update(['precio' => 1500]);

        // Verificar que la venta mantiene el precio original
        $venta = Venta::where('cliente_id', $this->cliente->id)->first();
        $productoEnVenta = $venta->productos->first();

        $this->assertEquals(1000, $productoEnVenta->pivot->precio_unitario);
    }

    /**
     * Test 19: Se puede incrementar y decrementar la cantidad desde el carrito
     */
    public function test_can_increment_and_decrement_quantity_from_cart(): void
    {
        $producto = Producto::factory()->create([
            'precio' => 500,
            'stock' => 10,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->call('agregarProducto', $producto->id);

        $carrito = $component->get('productosEnCarrito');
        $this->assertEquals(1, $carrito[$producto->id]['cantidad']);

        // Incrementar
        $component->call('incrementarCantidad', $producto->id);
        $carrito = $component->get('productosEnCarrito');
        $this->assertEquals(2, $carrito[$producto->id]['cantidad']);
        $this->assertEquals(1000, $component->get('montoTotal'));

        // Decrementar
        $component->call('decrementarCantidad', $producto->id);
        $carrito = $component->get('productosEnCarrito');
        $this->assertEquals(1, $carrito[$producto->id]['cantidad']);
        $this->assertEquals(500, $component->get('montoTotal'));
    }

    /**
     * Test 20: Al decrementar por debajo de 1, el producto se remueve del carrito
     */
    public function test_product_removed_when_decrementing_below_one(): void
    {
        $producto = Producto::factory()->create(['stock' => 10]);

        $component = Livewire::actingAs($this->user)
            ->test(VentasCrear::class)
            ->call('agregarProducto', $producto->id)
            ->call('decrementarCantidad', $producto->id);

        $carrito = $component->get('productosEnCarrito');
        // El producto debe ser removido del carrito
        $this->assertArrayNotHasKey($producto->id, $carrito);
    }
}
