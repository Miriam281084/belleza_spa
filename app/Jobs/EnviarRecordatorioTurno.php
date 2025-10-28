<?php

namespace App\Jobs;

use App\Mail\RecordatorioTurno;
use App\Models\Notificacion;
use App\Models\Turno;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatorioTurno implements ShouldQueue
{
    use Queueable;

    public $turno;

    /**
     * Create a new job instance.
     */
    public function __construct(Turno $turno)
    {
        $this->turno = $turno;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Enviar email
            Mail::to($this->turno->cliente->email)->send(new RecordatorioTurno($this->turno));

            // TODO: Enviar WhatsApp con Twilio cuando se configure
            // if ($this->turno->cliente->telefono) {
            //     $this->enviarWhatsApp();
            // }

            // Registrar notificación exitosa
            Notificacion::create([
                'cliente_id' => $this->turno->cliente_id,
                'tipo' => 'recordatorio_turno',
                'canal' => 'email',
                'estado' => 'enviado',
                'mensaje' => "Recordatorio enviado para turno #{$this->turno->id}",
            ]);

            Log::info("Recordatorio enviado para turno #{$this->turno->id}");

        } catch (\Exception $e) {
            // Registrar error
            Notificacion::create([
                'cliente_id' => $this->turno->cliente_id,
                'tipo' => 'recordatorio_turno',
                'canal' => 'email',
                'estado' => 'fallido',
                'mensaje' => "Error: " . $e->getMessage(),
            ]);

            Log::error("Error al enviar recordatorio turno #{$this->turno->id}: " . $e->getMessage());
        }
    }

    // Método preparado para Twilio WhatsApp
    private function enviarWhatsApp()
    {
        // TODO: Implementar cuando se tengan credenciales de Twilio
        // Requiere: composer require twilio/sdk
        // Y configurar TWILIO_SID, TWILIO_TOKEN, TWILIO_WHATSAPP_FROM en .env

        /* Ejemplo de implementación:
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));

        $mensaje = "Hola {$this->turno->cliente->nombre}, te recordamos tu turno de {$this->turno->servicio->nombre} "
                 . "hoy a las {$this->turno->fecha->format('H:i')} con {$this->turno->empleado->nombre}. "
                 . "¡Te esperamos en Belleza & SPA!";

        $twilio->messages->create(
            "whatsapp:{$this->turno->cliente->telefono}",
            [
                "from" => env('TWILIO_WHATSAPP_FROM'),
                "body" => $mensaje
            ]
        );
        */
    }
}
