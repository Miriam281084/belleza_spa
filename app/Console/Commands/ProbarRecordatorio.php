<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Turno;
use App\Jobs\EnviarRecordatorioTurno;

class ProbarRecordatorio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordatorios:probar {turno_id? : ID del turno a probar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar envío de recordatorio de turno manualmente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $turnoId = $this->argument('turno_id');

        if (!$turnoId) {
            // Mostrar lista de turnos pendientes y confirmados
            $turnos = Turno::with(['cliente', 'servicio', 'empleado'])
                ->whereIn('estado', ['pendiente', 'confirmado'])
                ->orderBy('fecha')
                ->orderBy('hora')
                ->get();

            if ($turnos->isEmpty()) {
                $this->error('No hay turnos pendientes o confirmados en el sistema.');
                return 1;
            }

            $this->info('=== TURNOS DISPONIBLES PARA PROBAR ===');
            $this->newLine();

            foreach ($turnos as $turno) {
                $fecha = $turno->fecha instanceof \Carbon\Carbon ? $turno->fecha : \Carbon\Carbon::parse($turno->fecha);
                $fechaHoraTurno = \Carbon\Carbon::parse($fecha->format('Y-m-d') . ' ' . $turno->hora);

                $this->line("ID: {$turno->id}");
                $this->line("Cliente: {$turno->cliente->nombre} {$turno->cliente->apellido}");
                $this->line("Teléfono: {$turno->cliente->telefono}");
                $this->line("Fecha/Hora: {$fechaHoraTurno->format('d/m/Y H:i')}");
                $this->line("Servicio: {$turno->servicio->nombre}");
                $this->line("Estado: {$turno->estado}");
                $this->newLine();
            }

            $turnoId = $this->ask('Ingresa el ID del turno para probar el recordatorio');
        }

        if (!$turnoId) {
            $this->error('Debes proporcionar un ID de turno');
            return 1;
        }

        // Buscar el turno
        $turno = Turno::with(['cliente', 'servicio', 'empleado'])->find($turnoId);

        if (!$turno) {
            $this->error("No se encontró un turno con ID: {$turnoId}");
            return 1;
        }

        if (!in_array($turno->estado, ['pendiente', 'confirmado'])) {
            $this->warn("ADVERTENCIA: El turno está en estado '{$turno->estado}'. Normalmente solo se envían recordatorios a turnos pendientes o confirmados.");
            if (!$this->confirm('¿Deseas continuar de todos modos?')) {
                return 0;
            }
        }

        $this->newLine();
        $this->info('=== INFORMACIÓN DEL TURNO ===');
        $fecha = $turno->fecha instanceof \Carbon\Carbon ? $turno->fecha : \Carbon\Carbon::parse($turno->fecha);
        $fechaHoraTurno = \Carbon\Carbon::parse($fecha->format('Y-m-d') . ' ' . $turno->hora);

        $this->line("ID: {$turno->id}");
        $this->line("Cliente: {$turno->cliente->nombre} {$turno->cliente->apellido}");
        $this->line("Email: {$turno->cliente->email}");
        $this->line("Teléfono: {$turno->cliente->telefono}");
        $this->line("Fecha/Hora: {$fechaHoraTurno->format('d/m/Y H:i')}");
        $this->line("Servicio: {$turno->servicio->nombre}");
        $this->line("Empleado: {$turno->empleado->nombre} {$turno->empleado->apellido}");
        $this->line("Estado: {$turno->estado}");
        $this->newLine();

        if (!$this->confirm('¿Deseas enviar el recordatorio de este turno?', true)) {
            $this->info('Operación cancelada.');
            return 0;
        }

        // Despachar el job
        EnviarRecordatorioTurno::dispatch($turno);

        $this->newLine();
        $this->info('✅ ¡Recordatorio despachado a la cola exitosamente!');
        $this->newLine();
        $this->info('El recordatorio se enviará por:');
        $this->line('  - Email a: ' . $turno->cliente->email);
        $this->line('  - WhatsApp a: ' . ($turno->cliente->telefono ?: 'No configurado'));
        $this->newLine();
        $this->warn('IMPORTANTE: Asegúrate de que el queue worker esté corriendo:');
        $this->line('  php artisan queue:listen');
        $this->newLine();
        $this->info('Verifica los logs en: storage/logs/laravel.log');

        return 0;
    }
}
