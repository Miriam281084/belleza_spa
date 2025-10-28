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
    protected $description = 'Enviar recordatorios de turnos que están dentro de una hora';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando turnos para enviar recordatorios...');

        // Obtener turnos de hoy dentro de la próxima hora
        $ahora = now();
        $unaHoraDespues = now()->addHour();

        $turnos = Turno::with(['cliente', 'servicio', 'empleado'])
            ->whereDate('fecha', $ahora->toDateString())
            ->whereTime('fecha', '>=', $ahora->toTimeString())
            ->whereTime('fecha', '<=', $unaHoraDespues->toTimeString())
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
