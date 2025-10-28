<?php

namespace App\Jobs;

use App\Mail\FelicitacionCumpleanos;
use App\Models\Cliente;
use App\Models\Notificacion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarFelicitacionCumpleanos implements ShouldQueue
{
    use Queueable;

    public $cliente;

    /**
     * Create a new job instance.
     */
    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Enviar email
            Mail::to($this->cliente->email)->send(new FelicitacionCumpleanos($this->cliente));

            // Registrar notificación exitosa
            Notificacion::create([
                'cliente_id' => $this->cliente->id,
                'tipo' => 'cumpleanos',
                'canal' => 'email',
                'estado' => 'enviado',
                'mensaje' => "Felicitación de cumpleaños enviada",
            ]);

            Log::info("Felicitación de cumpleaños enviada a {$this->cliente->nombre}");

        } catch (\Exception $e) {
            // Registrar error
            Notificacion::create([
                'cliente_id' => $this->cliente->id,
                'tipo' => 'cumpleanos',
                'canal' => 'email',
                'estado' => 'fallido',
                'mensaje' => "Error: " . $e->getMessage(),
            ]);

            Log::error("Error al enviar cumpleaños a {$this->cliente->nombre}: " . $e->getMessage());
        }
    }
}
