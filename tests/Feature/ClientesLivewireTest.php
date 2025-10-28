<?php

namespace Tests\Feature;

use App\Livewire\ClientesIndex;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Test de Feature para el componente Livewire ClientesIndex
 *
 * PROPÓSITO: Verificar que el CRUD de clientes funcione correctamente
 * desde la interfaz de usuario (componente Livewire).
 */
class ClientesLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    /**
     * Setup: Se ejecuta antes de cada test
     * Crea un usuario para las pruebas con autenticación
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario para autenticación
        $this->user = User::factory()->create();
    }

    /**
     * Test 1: El componente se puede renderizar
     *
     * QUÉ HACE: Carga el componente Livewire ClientesIndex
     * POR QUÉ ES IMPORTANTE: Verifica que el componente está bien configurado
     */
    public function testChargeVerification(): void
    {
        Livewire::actingAs($this->user)
            ->test(ClientesIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.clientes-index');
    }

    /**
     * Test 2: Se pueden listar clientes
     *
     * QUÉ HACE: Crea clientes y verifica que aparecen en la lista
     * POR QUÉ ES IMPORTANTE: Asegura que la funcionalidad de listado funciona
     */
    public function testClientsList(): void
    {
        // Crear 3 clientes de prueba
        $cliente1 = Cliente::factory()->create(['nombre' => 'Juan', 'apellido' => 'Pérez']);
        $cliente2 = Cliente::factory()->create(['nombre' => 'María', 'apellido' => 'García']);
        $cliente3 = Cliente::factory()->create(['nombre' => 'Pedro', 'apellido' => 'López']);

        Livewire::actingAs($this->user)
            ->test(ClientesIndex::class)
            ->assertSee('Juan')
            ->assertSee('María')
            ->assertSee('Pedro')
            ->assertSee('Pérez')
            ->assertSee('García')
            ->assertSee('López');
    }

    /**
     * Test 3: La búsqueda funciona correctamente
     *
     * QUÉ HACE: Busca un cliente por nombre y verifica los resultados
     * POR QUÉ ES IMPORTANTE: Asegura que el filtro de búsqueda funciona
     */
    public function testSearchClient(): void
    {
        Cliente::factory()->create(['nombre' => 'Juan', 'apellido' => 'Pérez']);
        Cliente::factory()->create(['nombre' => 'María', 'apellido' => 'García']);

        Livewire::actingAs($this->user)
            ->test(ClientesIndex::class)
            ->set('search', 'Juan')
            ->assertSee('Juan')
            ->assertDontSee('María');
    }

    /**
     * Test 4: Se puede crear un nuevo cliente
     *
     * QUÉ HACE: Llena el formulario y crea un nuevo cliente
     * POR QUÉ ES IMPORTANTE: Verifica que la creación funcione correctamente
     */
    public function testCreateClient(): void
    {
        Livewire::actingAs($this->user)
            ->test(ClientesIndex::class)
            ->set('nombre', 'Carlos')
            ->set('apellido', 'Ramírez')
            ->set('dni', '12345678')
            ->set('telefono', '1234567890')
            ->set('email', 'carlos@example.com')
            ->set('fecha_nacimiento', '1988-03-20')
            ->call('guardar')
            ->assertHasNoErrors();

        // Verificar que el cliente se guardó en la base de datos
        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Carlos',
            'apellido' => 'Ramírez',
            'dni' => '12345678',
            'email' => 'carlos@example.com',
        ]);
    }

    /**
     * Test 5: Validación de campos requeridos
     *
     * QUÉ HACE: Intenta guardar sin llenar campos obligatorios
     * POR QUÉ ES IMPORTANTE: Asegura que las validaciones funcionan
     */
    public function testValidationFields(): void
    {
        Livewire::actingAs($this->user)
            ->test(ClientesIndex::class)
            ->set('nombre', '')
            ->set('apellido', '')
            ->set('dni', '')
            ->call('guardar')
            ->assertHasErrors(['nombre', 'apellido', 'dni']);
    }

    /**
     * Test 6: Validación de email válido
     *
     * QUÉ HACE: Intenta guardar con un email inválido
     * POR QUÉ ES IMPORTANTE: Asegura que solo se aceptan emails válidos
     */
    public function testValidationEmail(): void
    {
        Livewire::actingAs($this->user)
            ->test(ClientesIndex::class)
            ->set('nombre', 'Ana')
            ->set('apellido', 'Martínez')
            ->set('dni', '87654321')
            ->set('email', 'email-invalido')
            ->call('guardar')
            ->assertHasErrors(['email']);
    }

    /**
     * Test 7: Se puede editar un cliente existente
     *
     * QUÉ HACE: Edita los datos de un cliente ya creado
     * POR QUÉ ES IMPORTANTE: Verifica que la actualización funcione
     */
    public function testEditsClient(): void
    {
        $cliente = Cliente::factory()->create([
            'nombre' => 'Luis',
            'apellido' => 'González',
            'dni' => '11111111',
        ]);

        Livewire::actingAs($this->user)
            ->test(ClientesIndex::class)
            ->call('editar', $cliente->id)
            ->assertSet('clienteId', $cliente->id)
            ->assertSet('nombre', 'Luis')
            ->assertSet('editando', true)
            ->set('nombre', 'Luis Actualizado')
            ->call('guardar');

        // Verificar que se actualizó en la base de datos
        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nombre' => 'Luis Actualizado',
            'apellido' => 'González',
        ]);
    }

    /**
     * Test 8: Se puede eliminar un cliente
     *
     * QUÉ HACE: Elimina un cliente de la base de datos
     * POR QUÉ ES IMPORTANTE: Verifica que la eliminación funcione
     */
    public function testDeleteClient(): void
    {
        $cliente = Cliente::factory()->create([
            'nombre' => 'Elena',
            'apellido' => 'Rodríguez',
            'dni' => '99999999',
        ]);

        Livewire::actingAs($this->user)
            ->test(ClientesIndex::class)
            ->call('eliminar', $cliente->id);

        // Verificar que el cliente fue eliminado
        $this->assertDatabaseMissing('clientes', [
            'id' => $cliente->id,
        ]);
    }

    /**
     * Test 9: El modal se abre y cierra correctamente
     *
     * QUÉ HACE: Prueba la funcionalidad del modal
     * POR QUÉ ES IMPORTANTE: Asegura la experiencia de usuario
     */
    public function testCloseOpenModal(): void
    {
        Livewire::actingAs($this->user)
            ->test(ClientesIndex::class)
            ->assertSet('modalAbierto', false)
            ->call('abrirModal')
            ->assertSet('modalAbierto', true)
            ->call('cerrarModal')
            ->assertSet('modalAbierto', false);
    }

    /**
     * Test 10: Validación de fecha de nacimiento no puede ser futura
     *
     * QUÉ HACE: Intenta guardar con una fecha futura
     * POR QUÉ ES IMPORTANTE: Valida lógica de negocio
     */
    public function testDateValidation(): void
    {
        Livewire::actingAs($this->user)
            ->test(ClientesIndex::class)
            ->set('nombre', 'Futuro')
            ->set('apellido', 'Cliente')
            ->set('dni', '55555555')
            ->set('fecha_nacimiento', now()->addYear()->format('Y-m-d'))
            ->call('guardar')
            ->assertHasErrors(['fecha_nacimiento']);
    }
}