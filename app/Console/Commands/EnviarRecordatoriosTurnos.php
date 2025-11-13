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

        // Obtener turnos que están entre 30 y 35 minutos en el futuro
        // (margen de 5 minutos para asegurar que el comando capture el turno)
        $ahora = now();
        $treintaMinutosDespues = now()->addMinutes(30);
        $treintaCincoMinutosDespues = now()->addMinutes(35);

        $turnos = Turno::with(['cliente', 'servicio', 'empleado'])
            ->whereDate('fecha', $ahora->toDateString())
            ->whereTime('fecha', '>=', $treintaMinutosDespues->toTimeString())
            ->whereTime('fecha', '<=', $treintaCincoMinutosDespues->toTimeString())
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->get();

        if ($turnos->isEmpty()) {
            $this->info('No hay turnos para enviar recordatorios en este momento.');
            return 0;
        }

        $this->info("Se encontraron {$turnos->count()} turno(s) para recordar.");

        foreach ($turnos as $turno) {
            EnviarRecordatorioTurno::dispatch($turno);
            $this->info("✓ Recordatorio despachado para turno #{$turno->id} - {$turno->cliente->nombre}");
        }

        $this->info('Todos los recordatorios han sido despachados a la cola.');
        return 0;
    }
}
