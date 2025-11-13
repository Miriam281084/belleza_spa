<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Turno;
use App\Models\Venta;
use App\Models\Pago;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ClientePerfil extends Component
{
    use WithPagination;

    public $cliente;
    public $mostrarFormulario = false;
    public $tabActiva = 'datos'; // datos, turnos, compras, pagos

    // Datos del cliente
    public $nombre;
    public $apellido;
    public $dni;
    public $telefono;
    public $email;
    public $fecha_nacimiento;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $user = Auth::user();

        // Buscar o crear el perfil de cliente
        $this->cliente = Cliente::where('user_id', $user->id)->first();

        if (!$this->cliente) {
            $this->cliente = Cliente::where('email', $user->email)->first();
            if ($this->cliente) {
                $this->cliente->update(['user_id' => $user->id]);
            }
        }

        if (!$this->cliente) {
            $nombreCompleto = explode(' ', $user->name, 2);
            $this->cliente = Cliente::create([
                'user_id' => $user->id,
                'nombre' => $nombreCompleto[0] ?? $user->name,
                'apellido' => $nombreCompleto[1] ?? '',
                'dni' => null,
                'email' => $user->email,
                'telefono' => '',
            ]);
        }

        $this->cargarDatos();
    }

    private function cargarDatos()
    {
        $this->nombre = $this->cliente->nombre;
        $this->apellido = $this->cliente->apellido;
        $this->dni = $this->cliente->dni;
        $this->telefono = $this->cliente->telefono;
        $this->email = $this->cliente->email;
        $this->fecha_nacimiento = $this->cliente->fecha_nacimiento;
    }

    public function cambiarTab($tab)
    {
        $this->tabActiva = $tab;
        $this->resetPage();
    }

    public function abrirFormulario()
    {
        $this->mostrarFormulario = true;
    }

    public function cerrarFormulario()
    {
        $this->mostrarFormulario = false;
        $this->cargarDatos();
        $this->resetValidation();
    }

    public function guardarDatos()
    {
        $this->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'dni' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|email|max:150|unique:clientes,email,' . $this->cliente->id,
            'fecha_nacimiento' => 'nullable|date|before:today',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy',
        ]);

        $this->cliente->update([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'dni' => $this->dni,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'fecha_nacimiento' => $this->fecha_nacimiento,
        ]);

        session()->flash('success', 'Datos actualizados correctamente');
        $this->cerrarFormulario();
    }

    public function render()
    {
        $turnos = [];
        $compras = [];
        $pagos = [];
        $estadisticas = [
            'total_turnos' => 0,
            'total_compras' => 0,
            'total_gastado' => 0,
            'saldo_pendiente' => 0,
        ];

        if ($this->cliente) {
            // Estadísticas
            $estadisticas['total_turnos'] = Turno::where('cliente_id', $this->cliente->id)->count();
            $estadisticas['total_compras'] = Venta::where('cliente_id', $this->cliente->id)->count();
            $estadisticas['total_gastado'] = Pago::where('cliente_id', $this->cliente->id)
                ->where('estado', 'completado')
                ->sum('monto');

            // Calcular saldo pendiente de turnos y ventas
            $turnosPendientes = Turno::where('cliente_id', $this->cliente->id)
                ->whereIn('estado', ['pendiente', 'confirmado', 'realizado'])
                ->with('pagos')
                ->get();

            $ventasPendientes = Venta::where('cliente_id', $this->cliente->id)
                ->with('pagos')
                ->get();

            foreach ($turnosPendientes as $turno) {
                $estadisticas['saldo_pendiente'] += $turno->saldoPendiente();
            }

            foreach ($ventasPendientes as $venta) {
                $estadisticas['saldo_pendiente'] += $venta->saldoPendiente();
            }

            // Datos según la pestaña activa
            switch ($this->tabActiva) {
                case 'turnos':
                    $turnos = Turno::where('cliente_id', $this->cliente->id)
                        ->with(['servicio', 'empleado'])
                        ->orderBy('fecha', 'desc')
                        ->orderBy('hora', 'desc')
                        ->paginate(10);
                    break;

                case 'compras':
                    $compras = Venta::where('cliente_id', $this->cliente->id)
                        ->with(['productos', 'pagos'])
                        ->orderBy('fecha', 'desc')
                        ->paginate(10);
                    break;

                case 'pagos':
                    $pagos = Pago::where('cliente_id', $this->cliente->id)
                        ->with(['turno', 'venta'])
                        ->orderBy('fecha_pago', 'desc')
                        ->paginate(15);
                    break;
            }
        }

        return view('livewire.cliente-perfil', [
            'turnos' => $turnos,
            'compras' => $compras,
            'pagos' => $pagos,
            'estadisticas' => $estadisticas,
        ]);
    }
}
