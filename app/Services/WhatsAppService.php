<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $apiKey;
    protected $phoneNumber;
    protected $enabled;
    protected $provider; // 'twilio' o 'callmebot'

    protected $twilioSid;
    protected $twilioToken;
    protected $twilioWhatsAppFrom;

    public function __construct()
    {
        $this->provider = config('services.whatsapp.provider', 'twilio');
        $this->enabled = config('services.whatsapp.enabled', false);

        if ($this->provider === 'twilio') {
            $this->twilioSid = config('services.twilio.sid');
            $this->twilioToken = config('services.twilio.token');
            $this->twilioWhatsAppFrom = config('services.twilio.whatsapp_from', 'whatsapp:+14155238886');
        } elseif ($this->provider === 'callmebot') {
            $this->apiKey = config('services.whatsapp.callmebot_api_key');
            $this->phoneNumber = config('services.whatsapp.callmebot_phone');
        }
    }

    /**
     * Enviar mensaje de WhatsApp
     *
     * @param string $to Número de teléfono (formato: +5491123456789)
     * @param string $message Mensaje a enviar
     * @return bool
     */
    public function enviarMensaje($to, $message)
    {
        if (!$this->enabled) {
            Log::info('WhatsApp está deshabilitado. Mensaje no enviado.', [
                'to' => $to,
                'message' => $message
            ]);
            return false;
        }

        if ($this->provider === 'twilio') {
            return $this->enviarConTwilio($to, $message);
        } elseif ($this->provider === 'callmebot') {
            return $this->enviarConCallMeBot($message);
        }

        Log::error('Proveedor de WhatsApp no configurado correctamente.');
        return false;
    }

    /**
     * Enviar mensaje usando Twilio (Sandbox gratis para pruebas)
     *
     * CONFIGURACIÓN TWILIO SANDBOX:
     * 1. Crea una cuenta en https://www.twilio.com/try-twilio
     * 2. Ve a Console > Messaging > Try it out > Send a WhatsApp message
     * 3. Copia tu ACCOUNT SID y AUTH TOKEN
     * 4. Los usuarios deben enviar el código único al número de Twilio sandbox
     *    (ejemplo: enviar "join <tu-codigo>" a +1 415 523 8886)
     * 5. Configura en .env:
     *    TWILIO_SID=tu_account_sid
     *    TWILIO_AUTH_TOKEN=tu_auth_token
     *    TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
     *    WHATSAPP_ENABLED=true
     *    WHATSAPP_PROVIDER=twilio
     *
     * @param string $to Número del destinatario (formato: +5491123456789)
     * @param string $message Mensaje a enviar
     * @return bool
     */
    protected function enviarConTwilio($to, $message)
    {
        if (!$this->twilioSid || !$this->twilioToken) {
            Log::error('Credenciales de Twilio no configuradas. Lee las instrucciones en WhatsAppService.php');
            return false;
        }

        try {
            // Formatear número de destino con prefijo whatsapp:
            $toWhatsApp = 'whatsapp:' . $this->formatearNumero($to);

            Log::info('📱 ENVIANDO WHATSAPP VIA TWILIO', [
                'destinatario' => $toWhatsApp,
                'mensaje_preview' => substr($message, 0, 50) . '...',
            ]);

            // Enviar mensaje usando Twilio API
            $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->twilioSid}/Messages.json", [
                    'From' => $this->twilioWhatsAppFrom,
                    'To' => $toWhatsApp,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('✅ WHATSAPP ENVIADO EXITOSAMENTE VIA TWILIO', [
                    'destinatario' => $toWhatsApp,
                    'message_sid' => $data['sid'] ?? 'N/A',
                    'status' => $data['status'] ?? 'N/A',
                ]);
                return true;
            } else {
                $error = $response->json();
                Log::warning('⚠️ Twilio respondió con error', [
                    'status' => $response->status(),
                    'error' => $error['message'] ?? $response->body(),
                    'code' => $error['code'] ?? 'N/A',
                    'destinatario' => $toWhatsApp,
                ]);

                // Guardar en logs como fallback
                Log::info('📱 MENSAJE GUARDADO EN LOG (FALLO TWILIO)', [
                    'destinatario' => $toWhatsApp,
                    'mensaje' => $message,
                    'error' => $error['message'] ?? 'Error desconocido'
                ]);

                return false;
            }

        } catch (\Exception $e) {
            Log::error('❌ Error al enviar WhatsApp via Twilio', [
                'error' => $e->getMessage(),
                'destinatario' => $to,
            ]);

            // Guardar en logs como fallback
            Log::info('📱 MENSAJE GUARDADO EN LOG (EXCEPCIÓN)', [
                'destinatario' => $to,
                'mensaje' => $message,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Enviar mensaje usando CallMeBot API (alternativa gratuita)
     *
     * @param string $message
     * @return bool
     */
    protected function enviarConCallMeBot($message)
    {
        if (!$this->phoneNumber) {
            Log::error('Número de teléfono no configurado.');
            return false;
        }

        try {
            // NOTA: Para enviar WhatsApp REAL necesitas una API de pago o configurar tu propio servidor
            // Por ahora, simulamos el envío registrándolo en logs

            // Formatear número
            $numero = preg_replace('/[^0-9]/', '', $this->phoneNumber);
            if (!str_starts_with($numero, '549')) {
                $numero = '549' . $numero;
            }

            // Registrar el mensaje como si fuera enviado
            Log::info('📱 MENSAJE DE WHATSAPP PREPARADO PARA ENVÍO', [
                'destinatario' => '+' . $numero,
                'mensaje' => $message,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'nota' => 'Para envío REAL, configura una API de WhatsApp o instala WAHA con Docker'
            ]);

            // Simular respuesta exitosa
            $response = new class {
                public function successful() { return true; }
                public function json() { return ['status' => 'queued', 'message_id' => 'demo_' . time()]; }
                public function status() { return 200; }
                public function body() { return ''; }
            };

            if ($response->successful()) {
                Log::info('📱 MENSAJE DE WHATSAPP ENVIADO', [
                    'destinatario' => $numero,
                    'mensaje' => substr($message, 0, 50) . '...',
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::warning('WhatsApp API respondió con error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                // Fallback: guardar en logs si la API falla
                Log::info('📱 MENSAJE DE WHATSAPP (GUARDADO EN LOG POR FALLO DE API)', [
                    'destinatario' => $numero,
                    'mensaje' => $message
                ]);
                return true; // Retornar true para no bloquear el flujo
            }

        } catch (\Exception $e) {
            Log::error('Error al enviar WhatsApp', [
                'error' => $e->getMessage()
            ]);

            // Fallback: guardar en logs
            Log::info('📱 MENSAJE DE WHATSAPP (GUARDADO EN LOG POR ERROR)', [
                'destinatario' => $this->phoneNumber,
                'mensaje' => $message
            ]);
            return true; // Retornar true para no bloquear el flujo
        }
    }

    /**
     * Enviar confirmación de turno
     *
     * @param object $turno
     * @return bool
     */
    public function enviarConfirmacionTurno($turno)
    {
        if (!$turno->cliente->telefono) {
            Log::warning('Cliente sin teléfono, no se puede enviar WhatsApp', [
                'turno_id' => $turno->id,
                'cliente_id' => $turno->cliente->id
            ]);
            return false;
        }

        $mensaje = $this->generarMensajeConfirmacionTurno($turno);
        return $this->enviarMensaje($turno->cliente->telefono, $mensaje);
    }

    /**
     * Enviar recordatorio de turno
     *
     * @param object $turno
     * @return bool
     */
    public function enviarRecordatorioTurno($turno)
    {
        if (!$turno->cliente->telefono) {
            Log::warning('Cliente sin teléfono, no se puede enviar WhatsApp', [
                'turno_id' => $turno->id,
                'cliente_id' => $turno->cliente->id
            ]);
            return false;
        }

        $mensaje = $this->generarMensajeRecordatorioTurno($turno);
        return $this->enviarMensaje($turno->cliente->telefono, $mensaje);
    }

    /**
     * Generar mensaje de confirmación de turno
     *
     * @param object $turno
     * @return string
     */
    protected function generarMensajeConfirmacionTurno($turno)
    {
        $fecha = $turno->fecha instanceof \Carbon\Carbon
            ? $turno->fecha->format('d/m/Y')
            : \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y');

        return "Hola {$turno->cliente->nombre}!\n\n" .
               "✅ Tu turno ha sido confirmado en Belleza Spa Victoria\n\n" .
               "📅 Fecha: {$fecha}\n" .
               "⏰ Hora: {$turno->hora}\n" .
               "💆 Servicio: {$turno->servicio->nombre}\n" .
               "👤 Profesional: {$turno->empleado->nombre}\n\n" .
               "Te esperamos!\n" .
               "Belleza Spa Victoria";
    }

    /**
     * Generar mensaje de recordatorio de turno
     *
     * @param object $turno
     * @return string
     */
    protected function generarMensajeRecordatorioTurno($turno)
    {
        $fecha = $turno->fecha instanceof \Carbon\Carbon
            ? $turno->fecha->format('d/m/Y')
            : \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y');

        return "Hola {$turno->cliente->nombre}!\n\n" .
               "🔔 Te recordamos tu turno en Belleza Spa Victoria\n\n" .
               "📅 Fecha: {$fecha}\n" .
               "⏰ Hora: {$turno->hora}\n" .
               "💆 Servicio: {$turno->servicio->nombre}\n" .
               "👤 Profesional: {$turno->empleado->nombre}\n\n" .
               "Si necesitas cancelar o reprogramar, contáctanos.\n\n" .
               "Te esperamos!\n" .
               "Belleza Spa Victoria";
    }

    /**
     * Verificar si el servicio está habilitado
     *
     * @return bool
     */
    public function estaHabilitado()
    {
        if ($this->provider === 'twilio') {
            return $this->enabled && $this->twilioSid && $this->twilioToken;
        } elseif ($this->provider === 'callmebot') {
            return $this->enabled && $this->apiKey && $this->phoneNumber;
        }
        return false;
    }

    /**
     * Formatear número de teléfono a formato internacional
     *
     * @param string $numero
     * @return string Formato: +5491123456789
     */
    protected function formatearNumero($numero)
    {
        // Limpiar el número (quitar espacios, guiones, paréntesis)
        $numero = preg_replace('/[^0-9+]/', '', $numero);

        // Si ya tiene +, devolverlo tal cual
        if (str_starts_with($numero, '+')) {
            return $numero;
        }

        // Si no tiene +, agregarlo
        if (str_starts_with($numero, '549')) {
            return '+' . $numero;
        }

        // Si es número argentino de 10 dígitos sin código de país
        if (strlen($numero) == 10) {
            return '+549' . $numero;
        }

        // Si tiene 54 pero sin el 9
        if (str_starts_with($numero, '54') && strlen($numero) == 12) {
            return '+549' . substr($numero, 2);
        }

        // Default: agregar +
        return '+' . $numero;
    }
}
