<?php

namespace App\Jobs;

use App\Mail\RecordatorioTurno;
use App\Models\Notificacion;
use App\Models\Turno;
use App\Services\WhatsAppService;
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
        $emailEnviado = false;
        $whatsappEnviado = false;

        try {
            // Enviar email si el cliente tiene email
            if ($this->turno->cliente->email) {
                Mail::to($this->turno->cliente->email)->send(new RecordatorioTurno($this->turno));
                $emailEnviado = true;

                Notificacion::create([
                    'cliente_id' => $this->turno->cliente_id,
                    'tipo' => 'recordatorio_turno',
                    'mensaje' => "Recordatorio de turno enviado por email",
                    'fecha_envio' => now(),
                    'estado' => 'enviado',
                ]);

                Log::info("Email de recordatorio enviado para turno #{$this->turno->id}");
            }

        } catch (\Exception $e) {
            Notificacion::create([
                'cliente_id' => $this->turno->cliente_id,
                'tipo' => 'recordatorio_turno',
                'mensaje' => "Error al enviar email: " . $e->getMessage(),
                'fecha_envio' => now(),
                'estado' => 'fallido',
            ]);

            Log::error("Error al enviar email recordatorio turno #{$this->turno->id}: " . $e->getMessage());
        }

        try {
            // Enviar WhatsApp si el cliente tiene teléfono
            if ($this->turno->cliente->telefono) {
                $whatsappService = new WhatsAppService();
                if ($whatsappService->estaHabilitado()) {
                    $enviado = $whatsappService->enviarRecordatorioTurno($this->turno);

                    if ($enviado) {
                        $whatsappEnviado = true;

                        Notificacion::create([
                            'cliente_id' => $this->turno->cliente_id,
                            'tipo' => 'recordatorio_turno',
                            'mensaje' => "Recordatorio de turno enviado por WhatsApp",
                            'fecha_envio' => now(),
                            'estado' => 'enviado',
                        ]);

                        Log::info("WhatsApp de recordatorio enviado para turno #{$this->turno->id}");
                    }
                }
            }

        } catch (\Exception $e) {
            Notificacion::create([
                'cliente_id' => $this->turno->cliente_id,
                'tipo' => 'recordatorio_turno',
                'mensaje' => "Error al enviar WhatsApp: " . $e->getMessage(),
                'fecha_envio' => now(),
                'estado' => 'fallido',
            ]);

            Log::error("Error al enviar WhatsApp recordatorio turno #{$this->turno->id}: " . $e->getMessage());
        }

        if ($emailEnviado || $whatsappEnviado) {
            Log::info("Recordatorio enviado para turno #{$this->turno->id} - Email: " . ($emailEnviado ? 'Sí' : 'No') . ", WhatsApp: " . ($whatsappEnviado ? 'Sí' : 'No'));
        }
    }
}
