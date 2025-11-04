<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Notificacion;
use App\Models\Servicio;
use App\Models\Turno;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Test de Feature para el módulo de Notificaciones WhatsApp
 *
 * PROPÓSITO: Verificar que el sistema de notificaciones WhatsApp funcione correctamente
 * incluyendo envío de mensajes, confirmaciones, recordatorios y gestión de notificaciones.
 */
class NotificacionesWhatsAppTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Cliente $cliente;
    protected WhatsAppService $whatsappService;

    /**
     * Setup: Se ejecuta antes de cada test
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->cliente = Cliente::factory()->create([
            'telefono' => '+5491123456789',
        ]);

        // Configurar WhatsApp para tests
        Config::set('services.whatsapp.enabled', true);
        Config::set('services.whatsapp.provider', 'twilio');
        Config::set('services.twilio.sid', 'test_sid');
        Config::set('services.twilio.token', 'test_token');
        Config::set('services.twilio.whatsapp_from', 'whatsapp:+14155238886');

        $this->whatsappService = new WhatsAppService();
    }

    /**
     * Test 1: Se puede crear una notificación en la base de datos
     */
    public function test_can_create_notification(): void
    {
        $notificacion = Notificacion::factory()->create([
            'cliente_id' => $this->cliente->id,
            'mensaje' => 'Test de notificación',
            'tipo' => 'general',
            'estado' => 'pendiente',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'cliente_id' => $this->cliente->id,
            'mensaje' => 'Test de notificación',
            'tipo' => 'general',
            'estado' => 'pendiente',
        ]);
    }

    /**
     * Test 2: Una notificación pertenece a un cliente
     */
    public function test_notification_belongs_to_client(): void
    {
        $notificacion = Notificacion::factory()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $this->assertEquals($this->cliente->id, $notificacion->cliente->id);
        $this->assertEquals($this->cliente->nombre, $notificacion->cliente->nombre);
    }

    /**
     * Test 3: Se puede listar notificaciones de un cliente
     */
    public function test_can_list_client_notifications(): void
    {
        Notificacion::factory()->count(5)->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $notificaciones = Notificacion::where('cliente_id', $this->cliente->id)->get();

        $this->assertCount(5, $notificaciones);
    }

    /**
     * Test 4: El servicio de WhatsApp está correctamente configurado
     */
    public function test_whatsapp_service_is_configured(): void
    {
        $this->assertTrue($this->whatsappService->estaHabilitado());
    }

    /**
     * Test 5: El servicio formatea correctamente números de teléfono argentinos
     */
    public function test_formats_argentine_phone_numbers_correctly(): void
    {
        // Usar reflection para acceder al método protegido
        $reflection = new \ReflectionClass(WhatsAppService::class);
        $method = $reflection->getMethod('formatearNumero');
        $method->setAccessible(true);

        $service = new WhatsAppService();

        // Casos de prueba
        $this->assertEquals('+5491123456789', $method->invoke($service, '1123456789'));
        $this->assertEquals('+5491123456789', $method->invoke($service, '+5491123456789'));
        $this->assertEquals('+5491123456789', $method->invoke($service, '5491123456789'));
        $this->assertEquals('+5491123456789', $method->invoke($service, '11 2345 6789'));
    }

    /**
     * Test 6: Se genera correctamente el mensaje de confirmación de turno
     */
    public function test_generates_confirmation_message_correctly(): void
    {
        $turno = Turno::factory()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $turno->load('cliente', 'servicio', 'empleado');

        // Usar reflection para acceder al método protegido
        $reflection = new \ReflectionClass(WhatsAppService::class);
        $method = $reflection->getMethod('generarMensajeConfirmacionTurno');
        $method->setAccessible(true);

        $mensaje = $method->invoke($this->whatsappService, $turno);

        $this->assertStringContainsString($turno->cliente->nombre, $mensaje);
        $this->assertStringContainsString($turno->servicio->nombre, $mensaje);
        $this->assertStringContainsString($turno->empleado->nombre, $mensaje);
        $this->assertStringContainsString('confirmado', $mensaje);
        $this->assertStringContainsString('Belleza Spa Victoria', $mensaje);
    }

    /**
     * Test 7: Se genera correctamente el mensaje de recordatorio de turno
     */
    public function test_generates_reminder_message_correctly(): void
    {
        $turno = Turno::factory()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $turno->load('cliente', 'servicio', 'empleado');

        // Usar reflection para acceder al método protegido
        $reflection = new \ReflectionClass(WhatsAppService::class);
        $method = $reflection->getMethod('generarMensajeRecordatorioTurno');
        $method->setAccessible(true);

        $mensaje = $method->invoke($this->whatsappService, $turno);

        $this->assertStringContainsString($turno->cliente->nombre, $mensaje);
        $this->assertStringContainsString($turno->servicio->nombre, $mensaje);
        $this->assertStringContainsString($turno->empleado->nombre, $mensaje);
        $this->assertStringContainsString('recordamos', $mensaje);
        $this->assertStringContainsString('Belleza Spa Victoria', $mensaje);
    }

    /**
     * Test 8: No se envía WhatsApp si el cliente no tiene teléfono
     */
    public function test_does_not_send_whatsapp_if_client_has_no_phone(): void
    {
        $clienteSinTelefono = Cliente::factory()->create([
            'telefono' => null,
        ]);

        $turno = Turno::factory()->create([
            'cliente_id' => $clienteSinTelefono->id,
        ]);

        $turno->load('cliente', 'servicio', 'empleado');

        Log::shouldReceive('warning')
            ->once()
            ->with('Cliente sin teléfono, no se puede enviar WhatsApp', \Mockery::any());

        $resultado = $this->whatsappService->enviarConfirmacionTurno($turno);

        $this->assertFalse($resultado);
    }

    /**
     * Test 9: El servicio registra en logs cuando WhatsApp está deshabilitado
     */
    public function test_logs_when_whatsapp_is_disabled(): void
    {
        Config::set('services.whatsapp.enabled', false);
        $service = new WhatsAppService();

        Log::shouldReceive('info')
            ->once()
            ->with('WhatsApp está deshabilitado. Mensaje no enviado.', \Mockery::any());

        $resultado = $service->enviarMensaje('+5491123456789', 'Test mensaje');

        $this->assertFalse($resultado);
    }

    /**
     * Test 10: Se pueden filtrar notificaciones por tipo
     */
    public function test_can_filter_notifications_by_type(): void
    {
        Notificacion::factory()->confirmacionTurno()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        Notificacion::factory()->recordatorioTurno()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        Notificacion::factory()->create([
            'cliente_id' => $this->cliente->id,
            'tipo' => 'promocion',
        ]);

        $confirmaciones = Notificacion::where('tipo', 'confirmacion_turno')->get();
        $recordatorios = Notificacion::where('tipo', 'recordatorio_turno')->get();
        $promociones = Notificacion::where('tipo', 'promocion')->get();

        $this->assertCount(1, $confirmaciones);
        $this->assertCount(1, $recordatorios);
        $this->assertCount(1, $promociones);
    }

    /**
     * Test 11: Se pueden filtrar notificaciones por estado
     */
    public function test_can_filter_notifications_by_status(): void
    {
        Notificacion::factory()->enviada()->create(['cliente_id' => $this->cliente->id]);
        Notificacion::factory()->pendiente()->create(['cliente_id' => $this->cliente->id]);
        Notificacion::factory()->fallida()->create(['cliente_id' => $this->cliente->id]);

        $enviadas = Notificacion::where('estado', 'enviado')->count();
        $pendientes = Notificacion::where('estado', 'pendiente')->count();
        $fallidas = Notificacion::where('estado', 'fallido')->count();

        $this->assertEquals(1, $enviadas);
        $this->assertEquals(1, $pendientes);
        $this->assertEquals(1, $fallidas);
    }

    /**
     * Test 12: El servicio retorna false si falla el envío con Twilio
     */
    public function test_returns_false_when_twilio_fails(): void
    {
        Http::fake([
            'api.twilio.com/*' => Http::response([
                'message' => 'Authentication Error',
                'code' => 20003,
            ], 401),
        ]);

        $resultado = $this->whatsappService->enviarMensaje('+5491123456789', 'Test mensaje');

        $this->assertFalse($resultado);
    }

    /**
     * Test 13: El servicio maneja excepciones correctamente
     */
    public function test_handles_exceptions_correctly(): void
    {
        Http::fake([
            'api.twilio.com/*' => function () {
                throw new \Exception('Network error');
            },
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('❌ Error al enviar WhatsApp via Twilio', \Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('📱 MENSAJE GUARDADO EN LOG (EXCEPCIÓN)', \Mockery::any());

        $resultado = $this->whatsappService->enviarMensaje('+5491123456789', 'Test mensaje');

        $this->assertFalse($resultado);
    }

    /**
     * Test 14: Se puede registrar el historial de notificaciones enviadas
     */
    public function test_can_track_notification_history(): void
    {
        $notificacion1 = Notificacion::factory()->enviada()->create([
            'cliente_id' => $this->cliente->id,
            'fecha_envio' => now()->subDays(5),
        ]);

        $notificacion2 = Notificacion::factory()->enviada()->create([
            'cliente_id' => $this->cliente->id,
            'fecha_envio' => now()->subDays(3),
        ]);

        $notificacion3 = Notificacion::factory()->enviada()->create([
            'cliente_id' => $this->cliente->id,
            'fecha_envio' => now()->subDay(),
        ]);

        $historial = Notificacion::where('cliente_id', $this->cliente->id)
            ->where('estado', 'enviado')
            ->orderBy('fecha_envio', 'desc')
            ->get();

        $this->assertCount(3, $historial);
        $this->assertEquals($notificacion3->id, $historial[0]->id);
        $this->assertEquals($notificacion1->id, $historial[2]->id);
    }

    /**
     * Test 15: El servicio valida credenciales de Twilio
     */
    public function test_validates_twilio_credentials(): void
    {
        Config::set('services.twilio.sid', null);
        Config::set('services.twilio.token', null);

        $service = new WhatsAppService();

        Log::shouldReceive('error')
            ->once()
            ->with('Credenciales de Twilio no configuradas. Lee las instrucciones en WhatsAppService.php');

        $resultado = $service->enviarMensaje('+5491123456789', 'Test mensaje');

        $this->assertFalse($resultado);
    }

    /**
     * Test 16: Se puede actualizar el estado de una notificación
     */
    public function test_can_update_notification_status(): void
    {
        $notificacion = Notificacion::factory()->pendiente()->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $this->assertEquals('pendiente', $notificacion->estado);

        $notificacion->update(['estado' => 'enviado']);

        $this->assertEquals('enviado', $notificacion->estado);
        $this->assertDatabaseHas('notificaciones', [
            'id' => $notificacion->id,
            'estado' => 'enviado',
        ]);
    }

    /**
     * Test 17: Se pueden obtener notificaciones pendientes de envío
     */
    public function test_can_get_pending_notifications(): void
    {
        Notificacion::factory()->pendiente()->count(3)->create([
            'cliente_id' => $this->cliente->id,
        ]);

        Notificacion::factory()->enviada()->count(2)->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $pendientes = Notificacion::where('estado', 'pendiente')->get();

        $this->assertCount(3, $pendientes);
    }

    /**
     * Test 18: El método estaHabilitado verifica la configuración correctamente
     */
    public function test_esta_habilitado_verifies_configuration(): void
    {
        // Configuración completa
        $service1 = new WhatsAppService();
        $this->assertTrue($service1->estaHabilitado());

        // Sin SID
        Config::set('services.twilio.sid', null);
        $service2 = new WhatsAppService();
        $this->assertFalse($service2->estaHabilitado());

        // Sin token
        Config::set('services.twilio.sid', 'test_sid');
        Config::set('services.twilio.token', null);
        $service3 = new WhatsAppService();
        $this->assertFalse($service3->estaHabilitado());

        // Deshabilitado
        Config::set('services.twilio.token', 'test_token');
        Config::set('services.whatsapp.enabled', false);
        $service4 = new WhatsAppService();
        $this->assertFalse($service4->estaHabilitado());
    }

    /**
     * Test 19: Se puede obtener el conteo de notificaciones por tipo
     */
    public function test_can_get_notification_count_by_type(): void
    {
        Notificacion::factory()->confirmacionTurno()->count(5)->create([
            'cliente_id' => $this->cliente->id,
        ]);

        Notificacion::factory()->recordatorioTurno()->count(3)->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $confirmaciones = Notificacion::where('tipo', 'confirmacion_turno')->count();
        $recordatorios = Notificacion::where('tipo', 'recordatorio_turno')->count();

        $this->assertEquals(5, $confirmaciones);
        $this->assertEquals(3, $recordatorios);
    }

    /**
     * Test 20: Un cliente puede tener múltiples notificaciones
     */
    public function test_client_can_have_multiple_notifications(): void
    {
        Notificacion::factory()->count(10)->create([
            'cliente_id' => $this->cliente->id,
        ]);

        $cliente = Cliente::with('notificaciones')->find($this->cliente->id);

        $this->assertCount(10, $cliente->notificaciones);
    }
}
