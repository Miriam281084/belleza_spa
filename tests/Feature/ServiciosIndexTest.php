<?php

namespace Tests\Feature;

use App\Livewire\ServiciosIndex;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Test de Feature para el componente Livewire ServiciosIndex
 *
 * PROPÓSITO: Verificar que el CRUD de servicios funcione correctamente
 * desde la interfaz de usuario (componente Livewire).
 */
class ServiciosIndexTest extends TestCase
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
     * QUÉ HACE: Carga el componente Livewire ServiciosIndex
     * POR QUÉ ES IMPORTANTE: Verifica que el componente está bien configurado
     */
    public function testChargeVerification(): void
    {
        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.servicios-index');
    }

    /**
     * Test 2: Se pueden listar servicios
     *
     * QUÉ HACE: Crea servicios y verifica que aparecen en la lista
     * POR QUÉ ES IMPORTANTE: Asegura que la funcionalidad de listado funciona
     */
    public function testServicesList(): void
    {
        // Crear 3 servicios de prueba
        $servicio1 = Servicio::factory()->create(['nombre' => 'Limpieza Facial', 'precio' => 1500]);
        $servicio2 = Servicio::factory()->create(['nombre' => 'Masaje Relajante', 'precio' => 2000]);
        $servicio3 = Servicio::factory()->create(['nombre' => 'Manicura', 'precio' => 800]);

        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->assertSee('Limpieza Facial')
            ->assertSee('Masaje Relajante')
            ->assertSee('Manicura');
    }

    /**
     * Test 3: La búsqueda por nombre funciona correctamente
     *
     * QUÉ HACE: Busca un servicio por nombre y verifica los resultados
     * POR QUÉ ES IMPORTANTE: Asegura que el filtro de búsqueda funciona
     */
    public function testSearchServiceByName(): void
    {
        Servicio::factory()->create(['nombre' => 'Limpieza Facial']);
        Servicio::factory()->create(['nombre' => 'Masaje Relajante']);

        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->set('search', 'Limpieza')
            ->assertSee('Limpieza Facial')
            ->assertDontSee('Masaje Relajante');
    }

    /**
     * Test 4: La búsqueda por descripción funciona correctamente
     *
     * QUÉ HACE: Busca un servicio por su descripción
     * POR QUÉ ES IMPORTANTE: Asegura que la búsqueda abarca múltiples campos
     */
    public function testSearchServiceByDescription(): void
    {
        Servicio::factory()->create([
            'nombre' => 'Servicio A',
            'descripcion' => 'Tratamiento especial para la piel',
        ]);
        Servicio::factory()->create([
            'nombre' => 'Servicio B',
            'descripcion' => 'Tratamiento capilar avanzado',
        ]);

        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->set('search', 'especial')
            ->assertSee('Servicio A')
            ->assertDontSee('Servicio B');
    }

    /**
     * Test 5: Se puede crear un nuevo servicio
     *
     * QUÉ HACE: Llena el formulario y crea un nuevo servicio
     * POR QUÉ ES IMPORTANTE: Verifica que la creación funcione correctamente
     */
    public function testCreateService(): void
    {
        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->set('nombre_servicio', 'Depilación Completa')
            ->set('descripcion', 'Depilación de cuerpo completo')
            ->set('duracion', 90)
            ->set('precio', 3000)
            ->call('guardar')
            ->assertHasNoErrors();

        // Verificar que el servicio se guardó en la base de datos
        $this->assertDatabaseHas('servicios', [
            'nombre' => 'Depilación Completa',
            'descripcion' => 'Depilación de cuerpo completo',
            'duracion' => 90,
            'precio' => 3000,
        ]);
    }

    /**
     * Test 6: Validación de campos requeridos
     *
     * QUÉ HACE: Intenta guardar sin llenar campos obligatorios
     * POR QUÉ ES IMPORTANTE: Asegura que las validaciones funcionan
     */
    public function testValidationRequiredFields(): void
    {
        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->set('nombre_servicio', '')
            ->set('duracion', '')
            ->set('precio', '')
            ->call('guardar')
            ->assertHasErrors(['nombre_servicio', 'duracion', 'precio']);
    }

    /**
     * Test 7: Validación de duración mínima (15 minutos)
     *
     * QUÉ HACE: Intenta guardar con una duración menor a 15 minutos
     * POR QUÉ ES IMPORTANTE: Asegura que la lógica de negocio se respeta
     */
    public function testValidationMinimumDuration(): void
    {
        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->set('nombre_servicio', 'Servicio Test')
            ->set('duracion', 10)
            ->set('precio', 1000)
            ->call('guardar')
            ->assertHasErrors(['duracion']);
    }

    /**
     * Test 8: Validación de precio no negativo
     *
     * QUÉ HACE: Intenta guardar con un precio negativo
     * POR QUÉ ES IMPORTANTE: Asegura que solo se aceptan precios válidos
     */
    public function testValidationNonNegativePrice(): void
    {
        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->set('nombre_servicio', 'Servicio Test')
            ->set('duracion', 60)
            ->set('precio', -100)
            ->call('guardar')
            ->assertHasErrors(['precio']);
    }

    /**
     * Test 9: Validación de tipos de datos correctos
     *
     * QUÉ HACE: Intenta guardar con tipos de datos incorrectos
     * POR QUÉ ES IMPORTANTE: Asegura la integridad de los datos
     */
    public function testValidationDataTypes(): void
    {
        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->set('nombre_servicio', 'Servicio Test')
            ->set('duracion', 'no-es-numero')
            ->set('precio', 'no-es-precio')
            ->call('guardar')
            ->assertHasErrors(['duracion', 'precio']);
    }

    /**
     * Test 10: Se puede editar un servicio existente
     *
     * QUÉ HACE: Edita los datos de un servicio ya creado
     * POR QUÉ ES IMPORTANTE: Verifica que la actualización funcione
     */
    public function testEditsService(): void
    {
        $servicio = Servicio::factory()->create([
            'nombre' => 'Servicio Original',
            'descripcion' => 'Descripción original',
            'duracion' => 60,
            'precio' => 1500,
        ]);

        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->call('editar', $servicio->id)
            ->assertSet('servicioId', $servicio->id)
            ->assertSet('nombre_servicio', 'Servicio Original')
            ->assertSet('editando', true)
            ->set('nombre_servicio', 'Servicio Actualizado')
            ->set('precio', 2000)
            ->call('guardar');

        // Verificar que se actualizó en la base de datos
        $this->assertDatabaseHas('servicios', [
            'id' => $servicio->id,
            'nombre' => 'Servicio Actualizado',
            'precio' => 2000,
        ]);
    }

    /**
     * Test 11: Se puede eliminar un servicio
     *
     * QUÉ HACE: Elimina un servicio de la base de datos
     * POR QUÉ ES IMPORTANTE: Verifica que la eliminación funcione
     */
    public function testDeleteService(): void
    {
        $servicio = Servicio::factory()->create([
            'nombre' => 'Servicio a Eliminar',
            'duracion' => 45,
            'precio' => 1200,
        ]);

        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->call('eliminar', $servicio->id);

        // Verificar que el servicio fue eliminado
        $this->assertDatabaseMissing('servicios', [
            'id' => $servicio->id,
        ]);
    }

    /**
     * Test 12: El modal se abre y cierra correctamente
     *
     * QUÉ HACE: Prueba la funcionalidad del modal
     * POR QUÉ ES IMPORTANTE: Asegura la experiencia de usuario
     */
    public function testCloseOpenModal(): void
    {
        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->assertSet('modalAbierto', false)
            ->call('abrirModal')
            ->assertSet('modalAbierto', true)
            ->call('cerrarModal')
            ->assertSet('modalAbierto', false);
    }

    /**
     * Test 13: El formulario se resetea al cerrar el modal
     *
     * QUÉ HACE: Verifica que los campos se limpien al cerrar
     * POR QUÉ ES IMPORTANTE: Evita que datos antiguos se mezclen con nuevos registros
     */
    public function testFormResetOnModalClose(): void
    {
        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->set('nombre_servicio', 'Prueba')
            ->set('descripcion', 'Test')
            ->set('duracion', 60)
            ->set('precio', 1000)
            ->call('abrirModal')
            ->call('cerrarModal')
            ->assertSet('nombre_servicio', '')
            ->assertSet('descripcion', '')
            ->assertSet('duracion', '')
            ->assertSet('precio', '');
    }

    /**
     * Test 14: Se pueden crear servicios con duraciones variadas
     *
     * QUÉ HACE: Crea servicios con diferentes duraciones válidas
     * POR QUÉ ES IMPORTANTE: Verifica que el sistema acepta diferentes duraciones
     */
    public function testCreateServicesWithVariedDurations(): void
    {
        $duraciones = [15, 30, 45, 60, 90, 120];

        foreach ($duraciones as $duracion) {
            Livewire::actingAs($this->user)
                ->test(ServiciosIndex::class)
                ->set('nombre_servicio', "Servicio {$duracion} min")
                ->set('duracion', $duracion)
                ->set('precio', 1000)
                ->call('guardar')
                ->assertHasNoErrors();

            $this->assertDatabaseHas('servicios', [
                'nombre' => "Servicio {$duracion} min",
                'duracion' => $duracion,
            ]);
        }
    }

    /**
     * Test 15: La descripción es opcional
     *
     * QUÉ HACE: Crea un servicio sin descripción
     * POR QUÉ ES IMPORTANTE: Verifica que campos opcionales funcionen correctamente
     */
    public function testDescriptionIsOptional(): void
    {
        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->set('nombre_servicio', 'Servicio Sin Descripción')
            ->set('descripcion', '')
            ->set('duracion', 30)
            ->set('precio', 1500)
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('servicios', [
            'nombre' => 'Servicio Sin Descripción',
            'descripcion' => '',
        ]);
    }

    /**
     * Test 16: Los precios decimales se almacenan correctamente
     *
     * QUÉ HACE: Crea servicios con precios decimales
     * POR QUÉ ES IMPORTANTE: Asegura que los centavos se manejan correctamente
     */
    public function testDecimalPricesAreStoredCorrectly(): void
    {
        Livewire::actingAs($this->user)
            ->test(ServiciosIndex::class)
            ->set('nombre_servicio', 'Servicio Decimal')
            ->set('duracion', 45)
            ->set('precio', 1234.56)
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('servicios', [
            'nombre' => 'Servicio Decimal',
            'precio' => 1234.56,
        ]);
    }
}
