<?php

namespace Tests\Feature;

use App\Livewire\TurnosCalendario;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use App\Models\Turno;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Test de Feature para el componente Livewire TurnosCalendario
 *
 * PROPÓSITO: Verificar que el CRUD de turnos funcione correctamente
 * incluyendo validaciones de disponibilidad y reglas de negocio.
 */
class TurnosCalendarioTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Cliente $cliente;
    protected Empleado $empleado;
    protected Servicio $servicio;

    /**
     * Setup: Se ejecuta antes de cada test
     * Crea los datos necesarios para las pruebas
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->cliente = Cliente::factory()->create();
        $this->empleado = Empleado::factory()->create(['rol' => 'esteticista']);
        $this->servicio = Servicio::factory()->create([
            'nombre' => 'Limpieza Facial',
            'duracion' => 60,
            'precio' => 1500,
        ]);
    }

    /**
     * Test 1: El componente se puede renderizar
     *
     * QUÉ HACE: Carga el componente Livewire TurnosCalendario
     * POR QUÉ ES IMPORTANTE: Verifica que el componente está bien configurado
     */
    public function testChargeVerification(): void
    {
        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.turnos-calendario');
    }

    /**
     * Test 2: El componente carga los datos necesarios en mount
     *
     * QUÉ HACE: Verifica que se cargan clientes, empleados y servicios
     * POR QUÉ ES IMPORTANTE: Asegura que los datos están disponibles para el formulario
     */
    public function testLoadsRequiredData(): void
    {
        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->assertSet('clientes', function ($clientes) {
                return $clientes->isNotEmpty();
            })
            ->assertSet('empleados', function ($empleados) {
                return $empleados->isNotEmpty();
            })
            ->assertSet('servicios', function ($servicios) {
                return $servicios->isNotEmpty();
            });
    }

    /**
     * Test 3: Solo carga empleados con rol esteticista
     *
     * QUÉ HACE: Verifica que solo se cargan esteticistas en la lista de empleados
     * POR QUÉ ES IMPORTANTE: Asegura que solo personal autorizado puede atender turnos
     */
    public function testOnlyLoadsEsteticistas(): void
    {
        $admin = Empleado::factory()->create(['rol' => 'admin', 'nombre' => 'Admin']);
        $recepcionista = Empleado::factory()->create(['rol' => 'recepcionista', 'nombre' => 'Recep']);
        $esteticista = Empleado::factory()->create(['rol' => 'esteticista', 'nombre' => 'Estetic']);

        $component = Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class);

        $empleados = $component->get('empleados');
        $nombres = $empleados->pluck('nombre')->toArray();

        $this->assertContains('Estetic', $nombres);
        $this->assertNotContains('Admin', $nombres);
        $this->assertNotContains('Recep', $nombres);
    }

    /**
     * Test 4: Se puede crear un turno con datos válidos
     *
     * QUÉ HACE: Crea un turno nuevo con todos los datos requeridos
     * POR QUÉ ES IMPORTANTE: Verifica la funcionalidad básica de creación
     */
    public function testCreateTurno(): void
    {
        $fechaFutura = Carbon::tomorrow()->format('Y-m-d');

        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->set('fecha', $fechaFutura)
            ->set('hora', '10:00')
            ->set('cliente_id', $this->cliente->id)
            ->set('empleado_id', $this->empleado->id)
            ->set('servicio_id', $this->servicio->id)
            ->set('estado', 'pendiente')
            ->call('guardar')
            ->assertHasNoErrors();

        $turno = Turno::where('cliente_id', $this->cliente->id)
            ->where('hora', '10:00')
            ->first();

        $this->assertNotNull($turno);
        $this->assertEquals($this->empleado->id, $turno->empleado_id);
        $this->assertEquals($this->servicio->id, $turno->servicio_id);
        $this->assertEquals('pendiente', $turno->estado);
    }

    /**
     * Test 5: Validación de campos requeridos
     *
     * QUÉ HACE: Intenta crear un turno sin campos obligatorios
     * POR QUÉ ES IMPORTANTE: Asegura que las validaciones funcionan
     */
    public function testValidationRequiredFields(): void
    {
        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->set('fecha', '')
            ->set('hora', '')
            ->set('cliente_id', '')
            ->set('empleado_id', '')
            ->set('servicio_id', '')
            ->call('guardar')
            ->assertHasErrors(['fecha', 'hora', 'cliente_id', 'empleado_id', 'servicio_id']);
    }

    /**
     * Test 6: No se pueden crear turnos en fechas pasadas
     *
     * QUÉ HACE: Intenta crear un turno con una fecha del pasado
     * POR QUÉ ES IMPORTANTE: Valida reglas de negocio importantes
     */
    public function testCannotCreateTurnoInPast(): void
    {
        $fechaPasada = Carbon::yesterday()->format('Y-m-d');

        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->set('fecha', $fechaPasada)
            ->set('hora', '10:00')
            ->set('cliente_id', $this->cliente->id)
            ->set('empleado_id', $this->empleado->id)
            ->set('servicio_id', $this->servicio->id)
            ->set('estado', 'pendiente')
            ->call('guardar')
            ->assertHasErrors(['fecha']);
    }

    /**
     * Test 7: Se actualizan validaciones de estado según modo
     *
     * QUÉ HACE: Verifica que en creación solo se permite pendiente/confirmado
     * POR QUÉ ES IMPORTANTE: Asegura que la lógica de estados es correcta
     */
    public function testStateValidationOnCreation(): void
    {
        $fechaFutura = Carbon::tomorrow()->format('Y-m-d');

        // Intentar crear con estado 'realizado' (no permitido en creación)
        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->set('fecha', $fechaFutura)
            ->set('hora', '10:00')
            ->set('cliente_id', $this->cliente->id)
            ->set('empleado_id', $this->empleado->id)
            ->set('servicio_id', $this->servicio->id)
            ->set('estado', 'realizado')
            ->call('guardar')
            ->assertHasErrors(['estado']);
    }

    /**
     * Test 8: Se puede editar un turno existente
     *
     * QUÉ HACE: Carga y edita un turno ya creado
     * POR QUÉ ES IMPORTANTE: Verifica la funcionalidad de actualización
     */
    public function testEditsTurno(): void
    {
        $turno = Turno::factory()->create([
            'cliente_id' => $this->cliente->id,
            'empleado_id' => $this->empleado->id,
            'servicio_id' => $this->servicio->id,
            'fecha' => Carbon::tomorrow()->format('Y-m-d'),
            'hora' => '10:00',
            'estado' => 'pendiente',
        ]);

        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->call('abrirModalEditar', $turno->id)
            ->assertSet('turnoId', $turno->id)
            ->assertSet('estado', 'pendiente')
            ->set('estado', 'confirmado')
            ->set('observaciones', 'Turno confirmado por teléfono')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('turnos', [
            'id' => $turno->id,
            'estado' => 'confirmado',
            'observaciones' => 'Turno confirmado por teléfono',
        ]);
    }

    /**
     * Test 9: En edición se permiten todos los estados
     *
     * QUÉ HACE: Verifica que al editar se pueden usar todos los estados
     * POR QUÉ ES IMPORTANTE: Asegura flexibilidad en la gestión de turnos
     */
    public function testAllStatesAllowedOnEdit(): void
    {
        $turno = Turno::factory()->create([
            'cliente_id' => $this->cliente->id,
            'empleado_id' => $this->empleado->id,
            'servicio_id' => $this->servicio->id,
            'fecha' => Carbon::tomorrow()->format('Y-m-d'),
            'hora' => '10:00',
            'estado' => 'pendiente',
        ]);

        $estados = ['pendiente', 'confirmado', 'realizado', 'cancelado'];

        foreach ($estados as $estado) {
            Livewire::actingAs($this->user)
                ->test(TurnosCalendario::class)
                ->call('abrirModalEditar', $turno->id)
                ->set('estado', $estado)
                ->call('guardar')
                ->assertHasNoErrors();

            $this->assertDatabaseHas('turnos', [
                'id' => $turno->id,
                'estado' => $estado,
            ]);
        }
    }

    /**
     * Test 10: Se puede eliminar un turno
     *
     * QUÉ HACE: Elimina un turno existente
     * POR QUÉ ES IMPORTANTE: Verifica la funcionalidad de eliminación
     */
    public function testDeleteTurno(): void
    {
        $turno = Turno::factory()->create([
            'cliente_id' => $this->cliente->id,
            'empleado_id' => $this->empleado->id,
            'servicio_id' => $this->servicio->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->call('abrirModalEditar', $turno->id)
            ->call('eliminar');

        $this->assertDatabaseMissing('turnos', [
            'id' => $turno->id,
        ]);
    }

    /**
     * Test 11: El modal se abre y cierra correctamente
     *
     * QUÉ HACE: Prueba la funcionalidad del modal
     * POR QUÉ ES IMPORTANTE: Asegura la experiencia de usuario
     */
    public function testCloseOpenModal(): void
    {
        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->assertSet('mostrarModal', false)
            ->call('abrirModalCrear', Carbon::tomorrow()->format('Y-m-d'), '10:00')
            ->assertSet('mostrarModal', true)
            ->call('cerrarModal')
            ->assertSet('mostrarModal', false);
    }

    /**
     * Test 12: Al seleccionar un servicio se actualiza la duración
     *
     * QUÉ HACE: Verifica que la duración se actualice automáticamente
     * POR QUÉ ES IMPORTANTE: Asegura que el sistema calcule correctamente los tiempos
     */
    public function testServiceSelectionUpdatesDuration(): void
    {
        $servicio = Servicio::factory()->create(['duracion' => 90]);

        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->set('servicio_id', $servicio->id)
            ->assertSet('duracionServicio', 90);
    }

    /**
     * Test 13: Permite turnos consecutivos sin superposición
     *
     * QUÉ HACE: Crea turnos que terminan justo cuando empieza el siguiente
     * POR QUÉ ES IMPORTANTE: Maximiza la utilización del tiempo
     */
    public function testAllowsConsecutiveTurnos(): void
    {
        $fecha = Carbon::tomorrow()->format('Y-m-d');

        // Crear un servicio con 60 minutos de duración
        $servicio60min = Servicio::factory()->create(['duracion' => 60]);

        // Crear primer turno de 10:00 a 11:00
        Turno::factory()->create([
            'empleado_id' => $this->empleado->id,
            'servicio_id' => $servicio60min->id,
            'fecha' => $fecha,
            'hora' => '10:00',
            'estado' => 'confirmado',
        ]);

        // Crear segundo turno de 11:00 a 12:00 (debe permitirse)
        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->set('fecha', $fecha)
            ->set('hora', '11:00')
            ->set('cliente_id', $this->cliente->id)
            ->set('empleado_id', $this->empleado->id)
            ->set('servicio_id', $servicio60min->id)
            ->set('estado', 'pendiente')
            ->call('guardar')
            ->assertHasNoErrors();

        $turno = Turno::where('empleado_id', $this->empleado->id)
            ->where('hora', '11:00')
            ->first();

        $this->assertNotNull($turno);
    }

    /**
     * Test 14: Ignora turnos cancelados al verificar disponibilidad
     *
     * QUÉ HACE: Verifica que turnos cancelados no bloqueen horarios
     * POR QUÉ ES IMPORTANTE: Permite reutilizar horarios de turnos cancelados
     */
    public function testIgnoresCancelledTurnosInAvailability(): void
    {
        $fecha = Carbon::tomorrow()->format('Y-m-d');

        // Crear un turno cancelado
        Turno::factory()->create([
            'empleado_id' => $this->empleado->id,
            'servicio_id' => $this->servicio->id,
            'fecha' => $fecha,
            'hora' => '10:00',
            'estado' => 'cancelado',
        ]);

        // Debe permitir crear un turno en el mismo horario
        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->set('fecha', $fecha)
            ->set('hora', '10:00')
            ->set('cliente_id', $this->cliente->id)
            ->set('empleado_id', $this->empleado->id)
            ->set('servicio_id', $this->servicio->id)
            ->set('estado', 'pendiente')
            ->call('guardar')
            ->assertHasNoErrors();
    }

    /**
     * Test 15: Al editar, no se cuenta a sí mismo como conflicto
     *
     * QUÉ HACE: Verifica que al editar no se marque como conflicto consigo mismo
     * POR QUÉ ES IMPORTANTE: Permite editar turnos sin errores falsos
     */
    public function testEditDoesNotConflictWithItself(): void
    {
        $fecha = Carbon::tomorrow()->format('Y-m-d');

        $turno = Turno::factory()->create([
            'empleado_id' => $this->empleado->id,
            'servicio_id' => $this->servicio->id,
            'fecha' => $fecha,
            'hora' => '10:00',
            'estado' => 'pendiente',
        ]);

        // Editar el mismo turno en el mismo horario debe funcionar
        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->call('abrirModalEditar', $turno->id)
            ->set('observaciones', 'Turno actualizado')
            ->call('guardar')
            ->assertHasNoErrors();
    }

    /**
     * Test 16: Diferentes empleados pueden tener turnos al mismo tiempo
     *
     * QUÉ HACE: Verifica que diferentes empleados puedan trabajar simultáneamente
     * POR QUÉ ES IMPORTANTE: Permite operación paralela del spa
     */
    public function testDifferentEmpleadosCanHaveSimultaneousTurnos(): void
    {
        $fecha = Carbon::tomorrow()->format('Y-m-d');
        $empleado2 = Empleado::factory()->create(['rol' => 'esteticista']);

        // Crear turno para empleado 1
        Turno::factory()->create([
            'empleado_id' => $this->empleado->id,
            'servicio_id' => $this->servicio->id,
            'fecha' => $fecha,
            'hora' => '10:00',
            'estado' => 'confirmado',
        ]);

        // Crear turno para empleado 2 en el mismo horario (debe permitirse)
        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->set('fecha', $fecha)
            ->set('hora', '10:00')
            ->set('cliente_id', $this->cliente->id)
            ->set('empleado_id', $empleado2->id)
            ->set('servicio_id', $this->servicio->id)
            ->set('estado', 'pendiente')
            ->call('guardar')
            ->assertHasNoErrors();
    }

    /**
     * Test 17: Las observaciones son opcionales
     *
     * QUÉ HACE: Crea un turno sin observaciones
     * POR QUÉ ES IMPORTANTE: Verifica que campos opcionales funcionan correctamente
     */
    public function testObservationsAreOptional(): void
    {
        $fecha = Carbon::tomorrow()->format('Y-m-d');

        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->set('fecha', $fecha)
            ->set('hora', '10:00')
            ->set('cliente_id', $this->cliente->id)
            ->set('empleado_id', $this->empleado->id)
            ->set('servicio_id', $this->servicio->id)
            ->set('estado', 'pendiente')
            ->set('observaciones', '')
            ->call('guardar')
            ->assertHasNoErrors();
    }

    /**
     * Test 18: Validación de relaciones existentes (foreign keys)
     *
     * QUÉ HACE: Intenta crear turno con IDs que no existen
     * POR QUÉ ES IMPORTANTE: Asegura integridad referencial
     */
    public function testValidatesForeignKeys(): void
    {
        $fecha = Carbon::tomorrow()->format('Y-m-d');

        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->set('fecha', $fecha)
            ->set('hora', '10:00')
            ->set('cliente_id', 99999)
            ->set('empleado_id', 99999)
            ->set('servicio_id', 99999)
            ->set('estado', 'pendiente')
            ->call('guardar')
            ->assertHasErrors(['cliente_id', 'empleado_id', 'servicio_id']);
    }

    /**
     * Test 19: El estado por defecto es 'pendiente'
     *
     * QUÉ HACE: Verifica que al abrir el modal el estado sea pendiente
     * POR QUÉ ES IMPORTANTE: Asegura un flujo de trabajo consistente
     */
    public function testDefaultStateIsPending(): void
    {
        Livewire::actingAs($this->user)
            ->test(TurnosCalendario::class)
            ->call('abrirModalCrear', Carbon::tomorrow()->format('Y-m-d'), '10:00')
            ->assertSet('estado', 'pendiente');
    }
}
