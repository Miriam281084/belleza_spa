<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Turno;
use App\Models\Venta;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PagosRegistrar extends Component
{
    public $tipo = 'turno'; // turno o venta
    public $id_referencia = '';
    public $id_cliente = '';
    public $monto = '';
    public $metodo = 'efectivo';

    // Propiedades para precarga
    public $turnoSeleccionado = null;
    public $ventaSeleccionada = null;

    public function mount($tipo = 'turno', $id = null)
    {
        $this->tipo = $tipo;

        if ($id) {
            $this->id_referencia = $id;
            $this->cargarDatos();
        }
    }

    public function updatedIdReferencia()
    {
        $this->cargarDatos();
    }

    public function updatedTipo()
    {
        $this->id_referencia = '';
        $this->id_cliente = '';
        $this->monto = '';
        $this->turnoSeleccionado = null;
        $this->ventaSeleccionada = null;
    }

    public function cargarDatos()
    {
        if (!$this->id_referencia) {
            return;
        }

        if ($this->tipo === 'turno') {
            $this->turnoSeleccionado = Turno::with(['servicio', 'cliente', 'pagos'])->find($this->id_referencia);

            if ($this->turnoSeleccionado) {
                $this->id_cliente = $this->turnoSeleccionado->cliente_id;
                // Cargar el saldo pendiente en lugar del precio total
                $this->monto = $this->turnoSeleccionado->saldoPendiente();
            }
        } elseif ($this->tipo === 'venta') {
            $this->ventaSeleccionada = Venta::with(['cliente', 'pagos'])->find($this->id_referencia);

            if ($this->ventaSeleccionada) {
                $this->id_cliente = $this->ventaSeleccionada->cliente_id;
                // Cargar el saldo pendiente en lugar del monto total
                $this->monto = $this->ventaSeleccionada->saldoPendiente();
            }
        }
    }

    public function render()
    {
        $clientes = Cliente::orderBy('nombre', 'asc')->get();

        // Filtrar solo turnos con saldo pendiente
        $turnos = Turno::with(['servicio', 'cliente', 'pagos'])
            ->whereIn('estado', ['pendiente', 'confirmado', 'realizado'])
            ->orderBy('fecha', 'desc')
            ->get()
            ->filter(function($turno) {
                return $turno->saldoPendiente() > 0;
            });

        // Filtrar solo ventas con saldo pendiente
        $ventas = Venta::with(['cliente', 'pagos'])
            ->orderBy('fecha', 'desc')
            ->limit(100)
            ->get()
            ->filter(function($venta) {
                return $venta->saldoPendiente() > 0;
            });

        return view('livewire.pagos-registrar', [
            'clientes' => $clientes,
            'turnos' => $turnos,
            'ventas' => $ventas,
        ]);
    }

    public function registrarPago()
    {
        $rules = [
            'id_cliente' => 'required|exists:clientes,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo' => 'required|in:efectivo,tarjeta,transferencia,mercadopago',
        ];

        if ($this->tipo === 'turno') {
            $rules['id_referencia'] = 'required|exists:turnos,id';
        } elseif ($this->tipo === 'venta') {
            $rules['id_referencia'] = 'required|exists:ventas,id';
        }

        $this->validate($rules, [
            'id_cliente.required' => 'Debe seleccionar un cliente.',
            'monto.required' => 'El monto es requerido.',
            'monto.min' => 'El monto debe ser mayor a 0.',
            'id_referencia.required' => 'Debe seleccionar un ' . $this->tipo . '.',
        ]);

        // Validar que el monto no supere el saldo pendiente
        if ($this->tipo === 'turno' && $this->turnoSeleccionado) {
            $saldoPendiente = $this->turnoSeleccionado->saldoPendiente();
            if ($this->monto > $saldoPendiente) {
                $this->dispatch('mostrarMensaje',
                    mensaje: "El monto ingresado ($" . number_format($this->monto, 0) . ") supera el saldo pendiente ($" . number_format($saldoPendiente, 0) . ").",
                    tipo: 'error'
                );
                return;
            }
        } elseif ($this->tipo === 'venta' && $this->ventaSeleccionada) {
            $saldoPendiente = $this->ventaSeleccionada->saldoPendiente();
            if ($this->monto > $saldoPendiente) {
                $this->dispatch('mostrarMensaje',
                    mensaje: "El monto ingresado ($" . number_format($this->monto, 0) . ") supera el saldo pendiente ($" . number_format($saldoPendiente, 0) . ").",
                    tipo: 'error'
                );
                return;
            }
        }

        try {
            $datosPago = [
                'cliente_id' => $this->id_cliente,
                'monto' => $this->monto,
                'metodo_pago' => $this->metodo,
                'fecha_pago' => now(),
                'estado' => 'completado',
            ];

            if ($this->tipo === 'turno') {
                $datosPago['turno_id'] = $this->id_referencia;
            } elseif ($this->tipo === 'venta') {
                $datosPago['venta_id'] = $this->id_referencia;
            }

            Pago::create($datosPago);

            // Recargar el turno/venta para calcular el nuevo saldo
            $mensaje = 'Pago registrado exitosamente.';
            if ($this->tipo === 'turno' && $this->turnoSeleccionado) {
                $this->turnoSeleccionado->refresh();
                $saldoRestante = $this->turnoSeleccionado->saldoPendiente();
                if ($saldoRestante <= 0) {
                    $mensaje = 'Pago registrado exitosamente. El turno ha sido pagado completamente.';
                } else {
                    $mensaje = 'Pago registrado exitosamente. Saldo pendiente: $' . number_format($saldoRestante, 0);
                }
            } elseif ($this->tipo === 'venta' && $this->ventaSeleccionada) {
                $this->ventaSeleccionada->refresh();
                $saldoRestante = $this->ventaSeleccionada->saldoPendiente();
                if ($saldoRestante <= 0) {
                    $mensaje = 'Pago registrado exitosamente. La venta ha sido pagada completamente.';
                } else {
                    $mensaje = 'Pago registrado exitosamente. Saldo pendiente: $' . number_format($saldoRestante, 0);
                }
            }

            $this->dispatch('mostrarMensaje', mensaje: $mensaje);

            // Limpiar formulario
            $this->reset(['id_referencia', 'id_cliente', 'monto', 'turnoSeleccionado', 'ventaSeleccionada']);

        } catch (\Exception $e) {
            $this->dispatch('mostrarMensaje',
                mensaje: 'Error al registrar el pago: ' . $e->getMessage(),
                tipo: 'error'
            );
        }
    }

    // Método para generar preferencia de Mercado Pago (preparado para futuro)
    public function generarPreferenciaMercadoPago()
    {
        // TODO: Implementar cuando se tengan las credenciales de Mercado Pago
        // Requiere: composer require mercadopago/dx-php
        // Y configurar MERCADOPAGO_PUBLIC_KEY y MERCADOPAGO_ACCESS_TOKEN en .env

        $this->dispatch('mostrarMensaje',
            mensaje: 'Mercado Pago no está configurado. Configure las credenciales en .env',
            tipo: 'error'
        );
    }
}
