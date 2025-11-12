<?php

namespace App\Livewire;

use App\Models\Turno;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use App\Services\WhatsAppService;
use App\Mail\RecordatorioTurno;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.app')]
class TurnosCalendario extends Component
{
    // Propiedades del modal
    public $mostrarModal = false;

    // Propiedades del formulario
    public $turnoId;
    public $fecha;
    public $hora;
    public $cliente_id;
    public $empleado_id;
    public $servicio_id;
    public $estado = 'pendiente';
    public $observaciones;
    public $duracionServicio = 0;

    // Datos para los selects
    public $clientes = [];
    public $empleados = [];
    public $servicios = [];

    // Propiedades para control de roles
    public $esEsteticista = false;
    public $empleadoActual = null;
    public $puedeCrearTurnos = true;

    public function mount()
    {
        $user = Auth::user();

        // Detectar si es Esteticista
        if ($user->hasRole('Esteticista')) {
            $this->esEsteticista = true;
            $this->puedeCrearTurnos = false; // Esteticistas NO pueden crear turnos

            // Buscar su perfil de empleado por email
            $this->empleadoActual = Empleado::where('email', $user->email)->first();
        }

        $this->cargarDatos();
    }

    private function cargarDatos()
    {
        $this->clientes = Cliente::orderBy('nombre')->get();
        // Solo cargar empleados con rol de Esteticista para asignar turnos
        $this->empleados = Empleado::where('rol', 'esteticista')
            ->orderBy('nombre')
            ->get();
        $this->servicios = Servicio::orderBy('nombre')->get();
    }

    public function render()
    {
        return view('livewire.turnos-calendario');
    }

    public function abrirModalCrear($fecha, $hora)
    {
        // Esteticistas no pueden crear turnos
        if ($this->esEsteticista) {
            $this->dispatch('mostrarMensaje',
                mensaje: 'Las esteticistas no pueden crear turnos. Contacte con recepción.',
                tipo: 'error'
            );
            return;
        }

        $this->resetearFormulario();
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->mostrarModal = true;
    }

    public function abrirModalEditar($turnoId)
    {
        $this->resetearFormulario();
        $turno = Turno::find($turnoId);

        // Esteticistas solo pueden editar sus propios turnos
        if ($this->esEsteticista && $this->empleadoActual) {
            if ($turno->empleado_id != $this->empleadoActual->id) {
                $this->dispatch('mostrarMensaje',
                    mensaje: 'Solo puedes editar tus propios turnos.',
                    tipo: 'error'
                );
                return;
            }
        }

        $this->cargarTurno($turnoId);
        $this->mostrarModal = true;
    }

    private function cargarTurno($turnoId)
    {
        $turno = Turno::with(['cliente', 'empleado', 'servicio'])->findOrFail($turnoId);

        $this->turnoId = $turno->id;
        $this->fecha = $turno->fecha->format('Y-m-d');
        $this->hora = $turno->hora;
        $this->cliente_id = $turno->cliente_id;
        $this->empleado_id = $turno->empleado_id;
        $this->servicio_id = $turno->servicio_id;
        $this->estado = $turno->estado;
        $this->observaciones = $turno->observaciones;
        $this->duracionServicio = $turno->servicio->duracion;
    }

    public function updatedServicioId($value)
    {
        if ($value) {
            $servicio = Servicio::find($value);
            $this->duracionServicio = $servicio ? $servicio->duracion : 0;
        } else {
            $this->duracionServicio = 0;
        }
    }

    private function resetearFormulario()
    {
        $this->turnoId = null;
        $this->fecha = null;
        $this->hora = null;
        $this->cliente_id = null;
        $this->empleado_id = null;
        $this->servicio_id = null;
        $this->estado = 'pendiente';
        $this->observaciones = null;
        $this->duracionServicio = 0;
        $this->resetValidation();
    }

    public function guardar()
    {
        try {
            // Determinar estados permitidos según si es creación o edición
            $estadosPermitidos = $this->turnoId
                ? 'pendiente,confirmado,realizado,cancelado'  // Edición: todos los estados
                : 'pendiente,confirmado';                      // Creación: solo pendiente y confirmado

            // Validar datos básicos
            $validated = $this->validate([
                'fecha' => 'required|date',
                'hora' => 'required',
                'cliente_id' => 'required|exists:clientes,id',
                'empleado_id' => 'required|exists:empleados,id',
                'servicio_id' => 'required|exists:servicios,id',
                'estado' => 'required|in:' . $estadosPermitidos,
                'observaciones' => 'nullable|string',
            ], [
                'fecha.required' => 'La fecha es obligatoria.',
                'fecha.date' => 'La fecha no es válida.',
                'hora.required' => 'La hora es obligatoria.',
                'cliente_id.required' => 'Debe seleccionar un cliente.',
                'cliente_id.exists' => 'El cliente seleccionado no existe.',
                'empleado_id.required' => 'Debe seleccionar un empleado.',
                'empleado_id.exists' => 'El empleado seleccionado no existe.',
                'servicio_id.required' => 'Debe seleccionar un servicio.',
                'servicio_id.exists' => 'El servicio seleccionado no existe.',
                'estado.required' => 'El estado es obligatorio.',
                'estado.in' => $this->turnoId
                    ? 'El estado seleccionado no es válido.'
                    : 'Al crear un turno solo se permite "Pendiente" o "Confirmado".',
            ]);

            // Validar que la fecha no sea en el pasado (excepto si es edición)
            if (!$this->turnoId) {
                $fechaTurno = Carbon::parse($this->fecha);
                $hoy = Carbon::today();

                if ($fechaTurno->lt($hoy)) {
                    $this->addError('fecha', 'No se pueden crear turnos para fechas pasadas.');
                    return;
                }
            }

            // Verificar disponibilidad
            $disponible = $this->verificarDisponibilidad(
                $this->fecha,
                $this->hora,
                $this->empleado_id,
                $this->servicio_id,
                $this->turnoId
            );

            if (!$disponible['disponible']) {
                $this->addError('hora', $disponible['mensaje']);
                return;
            }

            $datos = [
                'fecha' => $this->fecha,
                'hora' => $this->hora,
                'cliente_id' => $this->cliente_id,
                'empleado_id' => $this->empleado_id,
                'servicio_id' => $this->servicio_id,
                'estado' => $this->estado,
                'observaciones' => $this->observaciones,
            ];

            if ($this->turnoId) {
                // Actualización de turno
                $turno = Turno::findOrFail($this->turnoId);
                $estadoAnterior = $turno->estado;
                $turno->update($datos);
                $turno->load(['cliente', 'empleado', 'servicio']); // Recargar relaciones

                $mensaje = 'Turno actualizado exitosamente.';

                // Si cambió a confirmado, enviar notificaciones
                if ($estadoAnterior !== 'confirmado' && $this->estado === 'confirmado') {
                    $this->enviarNotificacionesTurno($turno);
                }
            } else {
                // Creación de turno
                $turno = Turno::create($datos);
                $turno->load(['cliente', 'empleado', 'servicio']); // Cargar relaciones

                $mensaje = 'Turno creado exitosamente.';

                // Si se crea como confirmado, enviar notificaciones
                if ($this->estado === 'confirmado') {
                    $this->enviarNotificacionesTurno($turno);
                }
            }

            // Disparar eventos ANTES de cerrar el modal
            $this->dispatch('recargarCalendario');
            $this->dispatch('mostrarMensaje', mensaje: $mensaje);

            // Cerrar el modal al final
            $this->cerrarModal();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Los errores de validación se manejan automáticamente por Livewire
            throw $e;
        } catch (\Exception $e) {
            $this->addError('general', 'Ocurrió un error al guardar el turno: ' . $e->getMessage());
        }
    }

    private function verificarDisponibilidad($fecha, $hora, $empleadoId, $servicioId, $turnoIdExcluir = null)
    {
        $servicio = Servicio::find($servicioId);
        if (!$servicio) {
            return ['disponible' => false, 'mensaje' => 'Servicio no encontrado.'];
        }

        $duracion = $servicio->duracion;
        $inicioNuevo = Carbon::parse($fecha . ' ' . $hora);
        $finNuevo = $inicioNuevo->copy()->addMinutes($duracion);

        $query = Turno::where('empleado_id', $empleadoId)
            ->where('fecha', $fecha)
            ->where('estado', '!=', 'cancelado');

        if ($turnoIdExcluir) {
            $query->where('id', '!=', $turnoIdExcluir);
        }

        $turnosExistentes = $query->with('servicio')->get();

        foreach ($turnosExistentes as $turno) {
            $inicioExistente = Carbon::parse($turno->fecha->format('Y-m-d') . ' ' . $turno->hora);
            $finExistente = $inicioExistente->copy()->addMinutes($turno->servicio->duracion);

            if ($inicioNuevo->lt($finExistente) && $finNuevo->gt($inicioExistente)) {
                $mensaje = "El empleado ya tiene un turno agendado de {$inicioExistente->format('H:i')} a {$finExistente->format('H:i')}.";
                return ['disponible' => false, 'mensaje' => $mensaje];
            }
        }

        return ['disponible' => true, 'mensaje' => ''];
    }

    public function eliminar()
    {
        if ($this->turnoId) {
            $turno = Turno::findOrFail($this->turnoId);
            $turno->delete();

            $this->dispatch('mostrarMensaje', mensaje: 'Turno eliminado exitosamente.');
            $this->cerrarModal();
            $this->dispatch('recargarCalendario');
        }
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->resetearFormulario();
    }

    /**
     * Enviar notificaciones de turno por WhatsApp y Email
     */
    private function enviarNotificacionesTurno($turno)
    {
        try {
            // Enviar WhatsApp
            $whatsappService = new WhatsAppService();
            if ($whatsappService->estaHabilitado()) {
                $whatsappService->enviarConfirmacionTurno($turno);
            }

            // Enviar Email
            if ($turno->cliente->email) {
                Mail::to($turno->cliente->email)->send(new RecordatorioTurno($turno));
            }

        } catch (\Exception $e) {
            // Log del error pero no interrumpir el flujo
            \Log::error('Error al enviar notificaciones de turno', [
                'turno_id' => $turno->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
