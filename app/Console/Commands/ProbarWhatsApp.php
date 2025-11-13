<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;

class ProbarWhatsApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:probar {telefono? : Número de teléfono con código de país (ej: 5493416123456)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar envío de mensajes de WhatsApp usando Twilio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $whatsappService = new WhatsAppService();

        // Verificar si WhatsApp está habilitado
        if (!$whatsappService->estaHabilitado()) {
            $this->error('❌ WhatsApp no está habilitado en el archivo .env');
            $this->info('Asegúrate de tener: WHATSAPP_ENABLED=true');
            return 1;
        }

        $this->info('✓ WhatsApp está habilitado');
        $this->info('✓ Proveedor: ' . config('services.whatsapp.provider'));
        $this->info('✓ Número Twilio: ' . config('services.twilio.whatsapp_from'));

        // Solicitar número de teléfono si no se pasó como argumento
        $telefono = $this->argument('telefono');

        if (!$telefono) {
            $this->newLine();
            $this->warn('IMPORTANTE: Para usar el Sandbox de Twilio:');
            $this->info('1. Ve a: https://console.twilio.com/us1/develop/sms/try-it-out/whatsapp-learn');
            $this->info('2. Desde tu WhatsApp, envía el código "join <tu-codigo>" al número +1 415 523 8886');
            $this->info('3. Espera la confirmación');
            $this->newLine();

            $telefono = $this->ask('Ingresa el número de teléfono (formato: 5493416123456)');
        }

        if (!$telefono) {
            $this->error('Debes proporcionar un número de teléfono');
            return 1;
        }

        // Formatear número si es necesario
        if (!str_starts_with($telefono, '+')) {
            $telefono = '+' . $telefono;
        }

        $this->info("📱 Enviando mensaje de prueba a: $telefono");

        $mensaje = "🧪 *Mensaje de Prueba - Belleza Spa Victoria*\n\n" .
                   "Este es un mensaje de prueba desde tu sistema de gestión.\n\n" .
                   "Si recibiste este mensaje, ¡Twilio está funcionando correctamente! ✅\n\n" .
                   "Fecha: " . now()->format('d/m/Y H:i:s');

        try {
            $resultado = $whatsappService->enviarMensaje($telefono, $mensaje);

            if ($resultado) {
                $this->newLine();
                $this->info('✅ ¡Mensaje enviado exitosamente!');
                $this->info('Revisa tu WhatsApp en el número: ' . $telefono);
            } else {
                $this->newLine();
                $this->error('❌ No se pudo enviar el mensaje');
                $this->warn('Verifica que:');
                $this->info('  1. Te hayas unido al Sandbox de Twilio (join <codigo>)');
                $this->info('  2. El número esté en formato correcto (+5493416123456)');
                $this->info('  3. Las credenciales de Twilio sean correctas en .env');
                $this->newLine();
                $this->info('Revisa los logs en: storage/logs/laravel.log');
            }

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Error al enviar mensaje: ' . $e->getMessage());
            $this->newLine();
            $this->info('Revisa los logs en: storage/logs/laravel.log para más detalles');
            return 1;
        }

        return 0;
    }
}
