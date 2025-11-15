<?php

namespace App\Console\Commands;

use App\Jobs\EnviarRecordatorioTurno;
use App\Models\Turno;
use Illuminate\Console\Command;

class EnviarRecordatoriosTurnos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordatorios:turnos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar recordatorios de turnos que están 30 minutos antes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando turnos para enviar recordatorios...');

        $ahora = now();
        $this->info("Hora actual: {$ahora->format('Y-m-d H:i:s')}");

        // Calcular el rango de tiempo: 30 a 35 minutos en el futuro
        $treintaMinutosDespues = $ahora->copy()->addMinutes(30);
        $treintaCincoMinutosDespues = $ahora->copy()->addMinutes(35);

        $this->info("Buscando turnos entre {$treintaMinutosDespues->format('Y-m-d H:i:s')} y {$treintaCincoMinutosDespues->format('Y-m-d H:i:s')}");

        // Obtener turnos de hoy y mañana (por si el turno es cerca de medianoche)
        $turnos = Turno::with(['cliente', 'servicio', 'empleado'])
            ->whereIn('fecha', [
                $treintaMinutosDespues->toDateString(),
                $ahora->toDateString()
            ])
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->get()
            ->filter(function ($turno) use ($treintaMinutosDespues, $treintaCincoMinutosDespues) {
                // Combinar fecha y hora del turno
                $fecha = $turno->fecha instanceof \Carbon\Carbon ? $turno->fecha : \Carbon\Carbon::parse($turno->fecha);
                $fechaHoraTurno = \Carbon\Carbon::parse($fecha->format('Y-m-d') . ' ' . $turno->hora);

                // Verificar si está en la ventana de 30-35 minutos
                return $fechaHoraTurno->between($treintaMinutosDespues, $treintaCincoMinutosDespues);
            });

        if ($turnos->isEmpty()) {
            $this->info('No hay turnos para enviar recordatorios en este momento.');
            return 0;
        }

        $this->info("✓ Se encontraron {$turnos->count()} turno(s) para recordar.");
        $this->newLine();

        foreach ($turnos as $turno) {
            $fecha = $turno->fecha instanceof \Carbon\Carbon ? $turno->fecha : \Carbon\Carbon::parse($turno->fecha);
            $fechaHoraTurno = \Carbon\Carbon::parse($fecha->format('Y-m-d') . ' ' . $turno->hora);

            $this->info("  → Turno #{$turno->id}");
            $this->info("    Cliente: {$turno->cliente->nombre}");
            $this->info("    Fecha/Hora: {$fechaHoraTurno->format('d/m/Y H:i')}");
            $this->info("    Servicio: {$turno->servicio->nombre}");

            // Enviar directamente SIN cola (síncrono)
            try {
                $job = new EnviarRecordatorioTurno($turno);
                $job->handle();
                $this->info("    ✓ Recordatorio enviado exitosamente");
            } catch (\Exception $e) {
                $this->error("    ✗ Error al enviar: " . $e->getMessage());
            }

            $this->newLine();
        }

        $this->info('✓ Todos los recordatorios han sido procesados.');

        return 0;
    }
}
