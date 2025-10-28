<?php

namespace App\Console\Commands;

use App\Jobs\EnviarFelicitacionCumpleanos;
use App\Models\Cliente;
use Illuminate\Console\Command;

class EnviarCumpleanos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cumpleanos:enviar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar felicitaciones de cumpleaños a los clientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando clientes que cumplen años hoy...');

        $hoy = now();

        // Buscar clientes cuyo mes y día de nacimiento coincidan con hoy
        $clientes = Cliente::whereMonth('fecha_nacimiento', $hoy->month)
            ->whereDay('fecha_nacimiento', $hoy->day)
            ->get();

        if ($clientes->isEmpty()) {
            $this->info('No hay cumpleaños hoy.');
            return 0;
        }

        $this->info("Se encontraron {$clientes->count()} cumpleaños hoy.");

        foreach ($clientes as $cliente) {
            EnviarFelicitacionCumpleanos::dispatch($cliente);
            $this->info("✓ Felicitación despachada para {$cliente->nombre}");
        }

        $this->info('Todas las felicitaciones han sido despachadas a la cola.');
        return 0;
    }
}
