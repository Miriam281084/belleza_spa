<?php

namespace Tests\Feature;

use App\Livewire\EmpleadosIndex;
use App\Models\Empleado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Test de Feature para el componente Livewire EmpleadosIndex
 *
 * PROPÓSITO: Verificar que el CRUD de empleados funcione correctamente
 * desde la interfaz de usuario (componente Livewire).
 */
class EmpleadosIndexTest extends TestCase
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
     * QUÉ HACE: Carga el componente Livewire EmpleadosIndex
     * POR QUÉ ES IMPORTANTE: Verifica que el componente está bien configurado
     */
    public function testChargeVerification(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.empleados-index');
    }

    /**
     * Test 2: Se pueden listar empleados
     *
     * QUÉ HACE: Crea empleados y verifica que aparecen en la lista
     * POR QUÉ ES IMPORTANTE: Asegura que la funcionalidad de listado funciona
     */
    public function testEmployeesList(): void
    {
        // Crear 3 empleados de prueba con diferentes roles
        $empleado1 = Empleado::factory()->create(['nombre' => 'Ana', 'apellido' => 'Martínez', 'rol' => 'admin']);
        $empleado2 = Empleado::factory()->create(['nombre' => 'Carlos', 'apellido' => 'López', 'rol' => 'recepcionista']);
        $empleado3 = Empleado::factory()->create(['nombre' => 'Sofía', 'apellido' => 'García', 'rol' => 'esteticista']);

        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->assertSee('Ana')
            ->assertSee('Carlos')
            ->assertSee('Sofía')
            ->assertSee('Martínez')
            ->assertSee('López')
            ->assertSee('García');
    }

    /**
     * Test 3: La búsqueda funciona correctamente
     *
     * QUÉ HACE: Busca un empleado por nombre y verifica los resultados
     * POR QUÉ ES IMPORTANTE: Asegura que el filtro de búsqueda funciona
     */
    public function testSearchEmployee(): void
    {
        Empleado::factory()->create(['nombre' => 'Pedro', 'apellido' => 'Ramírez', 'rol' => 'admin']);
        Empleado::factory()->create(['nombre' => 'Laura', 'apellido' => 'Sánchez', 'rol' => 'esteticista']);

        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->set('search', 'Pedro')
            ->assertSee('Pedro')
            ->assertDontSee('Laura');
    }

    /**
     * Test 4: Se puede crear un nuevo empleado
     *
     * QUÉ HACE: Llena el formulario y crea un nuevo empleado
     * POR QUÉ ES IMPORTANTE: Verifica que la creación funcione correctamente
     */
    public function testCreateEmployee(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->set('nombre', 'Roberto')
            ->set('apellido', 'Fernández')
            ->set('email', 'roberto@example.com')
            ->set('telefono', '1234567890')
            ->set('rol', 'recepcionista')
            ->call('guardar')
            ->assertHasNoErrors();

        // Verificar que el empleado se guardó en la base de datos
        $this->assertDatabaseHas('empleados', [
            'nombre' => 'Roberto',
            'apellido' => 'Fernández',
            'email' => 'roberto@example.com',
            'rol' => 'recepcionista',
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
            ->test(EmpleadosIndex::class)
            ->set('nombre', '')
            ->set('apellido', '')
            ->set('email', '')
            ->call('guardar')
            ->assertHasErrors(['nombre', 'apellido', 'email']);
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
            ->test(EmpleadosIndex::class)
            ->set('nombre', 'Isabel')
            ->set('apellido', 'Torres')
            ->set('email', 'email-invalido')
            ->set('rol', 'esteticista')
            ->call('guardar')
            ->assertHasErrors(['email']);
    }

    /**
     * Test 7: Validación del campo rol (solo valores permitidos)
     *
     * QUÉ HACE: Intenta guardar con un rol no válido
     * POR QUÉ ES IMPORTANTE: Asegura que solo se aceptan roles permitidos
     */
    public function testValidationRole(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->set('nombre', 'Miguel')
            ->set('apellido', 'Vargas')
            ->set('email', 'miguel@example.com')
            ->set('rol', 'rol-invalido')
            ->call('guardar')
            ->assertHasErrors(['rol']);
    }

    /**
     * Test 8: Se puede editar un empleado existente
     *
     * QUÉ HACE: Edita los datos de un empleado ya creado
     * POR QUÉ ES IMPORTANTE: Verifica que la actualización funcione
     */
    public function testEditsEmployee(): void
    {
        $empleado = Empleado::factory()->create([
            'nombre' => 'Diana',
            'apellido' => 'Morales',
            'email' => 'diana@example.com',
            'rol' => 'esteticista',
        ]);

        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->call('editar', $empleado->id)
            ->assertSet('empleadoId', $empleado->id)
            ->assertSet('nombre', 'Diana')
            ->assertSet('editando', true)
            ->set('nombre', 'Diana Actualizada')
            ->set('rol', 'admin')
            ->call('guardar');

        // Verificar que se actualizó en la base de datos
        $this->assertDatabaseHas('empleados', [
            'id' => $empleado->id,
            'nombre' => 'Diana Actualizada',
            'apellido' => 'Morales',
            'rol' => 'admin',
        ]);
    }

    /**
     * Test 9: Se puede eliminar un empleado
     *
     * QUÉ HACE: Elimina un empleado de la base de datos
     * POR QUÉ ES IMPORTANTE: Verifica que la eliminación funcione
     */
    public function testDeleteEmployee(): void
    {
        $empleado = Empleado::factory()->create([
            'nombre' => 'Fernando',
            'apellido' => 'Castro',
            'email' => 'fernando@example.com',
            'rol' => 'recepcionista',
        ]);

        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->call('eliminar', $empleado->id);

        // Verificar que el empleado fue eliminado
        $this->assertDatabaseMissing('empleados', [
            'id' => $empleado->id,
        ]);
    }

    /**
     * Test 10: El modal se abre y cierra correctamente
     *
     * QUÉ HACE: Prueba la funcionalidad del modal
     * POR QUÉ ES IMPORTANTE: Asegura la experiencia de usuario
     */
    public function testCloseOpenModal(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->assertSet('modalAbierto', false)
            ->call('abrirModal')
            ->assertSet('modalAbierto', true)
            ->call('cerrarModal')
            ->assertSet('modalAbierto', false);
    }

    /**
     * Test 11: El rol por defecto es 'esteticista'
     *
     * QUÉ HACE: Verifica que al abrir el modal el rol sea esteticista
     * POR QUÉ ES IMPORTANTE: Valida el comportamiento por defecto
     */
    public function testDefaultRole(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->call('abrirModal')
            ->assertSet('rol', 'esteticista');
    }

    /**
     * Test 12: Se pueden crear empleados con cada uno de los tres roles
     *
     * QUÉ HACE: Crea empleados con admin, recepcionista y esteticista
     * POR QUÉ ES IMPORTANTE: Verifica que todos los roles permitidos funcionan
     */
    public function testAllRolesCreation(): void
    {
        // Admin
        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->set('nombre', 'Admin')
            ->set('apellido', 'User')
            ->set('email', 'admin@example.com')
            ->set('rol', 'admin')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('empleados', ['email' => 'admin@example.com', 'rol' => 'admin']);

        // Recepcionista
        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->set('nombre', 'Recep')
            ->set('apellido', 'User')
            ->set('email', 'recep@example.com')
            ->set('rol', 'recepcionista')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('empleados', ['email' => 'recep@example.com', 'rol' => 'recepcionista']);

        // Esteticista
        Livewire::actingAs($this->user)
            ->test(EmpleadosIndex::class)
            ->set('nombre', 'Estetic')
            ->set('apellido', 'User')
            ->set('email', 'estetic@example.com')
            ->set('rol', 'esteticista')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('empleados', ['email' => 'estetic@example.com', 'rol' => 'esteticista']);
    }
}
