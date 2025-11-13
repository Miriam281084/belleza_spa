<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Turno;
use App\Models\Venta;
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PagosRegistrar extends Component
{
    public $filtroTurnos = false;
    public $filtroVentas = false;
    public $tipo = ''; // turno o venta (se mantiene para compatibilidad con la selección)
    public $id_referencia = '';
    public $id_cliente = '';
    public $monto = '';
    public $metodo = 'efectivo';

    // Propiedades para precarga
    public $turnoSeleccionado = null;
    public $ventaSeleccionada = null;

    // Propiedades para Mercado Pago
    public $mercadopago_url = null;
    public $mercadopago_preference_id = null;
    public $mercadopago_qr = null;
    public $mostrarQR = false;

    // Propiedades para Cliente
    public $esCliente = false;
    public $clienteActual = null;

    public function mount($tipo = '', $id = null)
    {
        $user = Auth::user();

        // Verificar si el usuario autenticado tiene rol Cliente
        if ($user->hasRole('Cliente')) {
            $this->esCliente = true;

            // Buscar o crear el perfil de cliente
            $cliente = Cliente::where('user_id', $user->id)->first();

            if (!$cliente) {
                $cliente = Cliente::where('email', $user->email)->first();
                if ($cliente) {
                    $cliente->update(['user_id' => $user->id]);
                }
            }

            if (!$cliente) {
                $nombreCompleto = explode(' ', $user->name, 2);
                $cliente = Cliente::create([
                    'user_id' => $user->id,
                    'nombre' => $nombreCompleto[0] ?? $user->name,
                    'apellido' => $nombreCompleto[1] ?? '',
                    'dni' => null,
                    'email' => $user->email,
                    'telefono' => '',
                ]);
            }

            $this->clienteActual = $cliente;
            $this->id_cliente = $cliente->id;
        }

        // Inicializar filtros según el tipo
        if ($tipo === 'turno') {
            $this->filtroTurnos = true;
            $this->tipo = 'turno';
        } elseif ($tipo === 'venta') {
            $this->filtroVentas = true;
            $this->tipo = 'venta';
        }

        if ($id) {
            $this->id_referencia = $id;
            $this->cargarDatos();
        }
    }

    public function updatedIdReferencia()
    {
        $this->cargarDatos();
        // Limpiar TODOS los errores de validación cuando se selecciona una referencia
        $this->resetValidation();
    }

    public function updatedTipo()
    {
        $this->id_referencia = '';
        $this->id_cliente = '';
        $this->monto = '';
        $this->turnoSeleccionado = null;
        $this->ventaSeleccionada = null;
        $this->resetValidation();
    }

    public function updatedMetodo()
    {
        // Limpiar errores de validación cuando cambia el método de pago
        $this->resetValidation();
    }

    public function cargarDatos()
    {
        if (!$this->id_referencia) {
            return;
        }

        // Parsear el valor: "turno-ID" o "venta-ID"
        $partes = explode('-', $this->id_referencia);
        if (count($partes) !== 2) {
            return;
        }

        $tipo = $partes[0]; // "turno" o "venta"
        $id = $partes[1];   // ID numérico

        // Actualizar el tipo para compatibilidad
        $this->tipo = $tipo;

        if ($tipo === 'turno') {
            $this->turnoSeleccionado = Turno::with(['servicio', 'cliente', 'pagos'])->find($id);
            $this->ventaSeleccionada = null;

            if ($this->turnoSeleccionado) {
                $this->id_cliente = $this->turnoSeleccionado->cliente_id;
                $this->monto = round($this->turnoSeleccionado->saldoPendiente(), 2);
            }
        } elseif ($tipo === 'venta') {
            $this->ventaSeleccionada = Venta::with(['cliente', 'pagos'])->find($id);
            $this->turnoSeleccionado = null;

            if ($this->ventaSeleccionada) {
                $this->id_cliente = $this->ventaSeleccionada->cliente_id;
                $this->monto = round($this->ventaSeleccionada->saldoPendiente(), 2);
            }
        }
    }

    public function render()
    {
        $clientes = Cliente::orderBy('nombre', 'asc')->get();

        // Filtrar turnos según si es cliente o empleado
        $turnosQuery = Turno::with(['servicio', 'cliente', 'pagos'])
            ->whereIn('estado', ['pendiente', 'confirmado', 'realizado'])
            ->orderBy('fecha', 'desc');

        // Si es cliente, filtrar solo sus turnos
        if ($this->esCliente && $this->clienteActual) {
            $turnosQuery->where('cliente_id', $this->clienteActual->id);
        }

        $turnos = $turnosQuery->get()
            ->filter(function($turno) {
                return $turno->saldoPendiente() > 0;
            });

        // Filtrar ventas según si es cliente o empleado
        $ventasQuery = Venta::with(['cliente', 'pagos'])
            ->orderBy('fecha', 'desc')
            ->limit(100);

        // Si es cliente, filtrar solo sus ventas
        if ($this->esCliente && $this->clienteActual) {
            $ventasQuery->where('cliente_id', $this->clienteActual->id);
        }

        $ventas = $ventasQuery->get()
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
        // Parsear id_referencia para obtener el tipo y el ID real
        $idReal = null;
        $tipoReal = null;
        if ($this->id_referencia) {
            $partes = explode('-', $this->id_referencia);
            if (count($partes) === 2) {
                $tipoReal = $partes[0]; // 'turno' o 'venta'
                $idReal = $partes[1];    // ID numérico
            }
        }

        $rules = [
            'id_cliente' => 'required|exists:clientes,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo' => 'required|in:efectivo,tarjeta,transferencia,mercadopago',
            'id_referencia' => 'required',
        ];

        $this->validate($rules, [
            'id_cliente.required' => 'Debe seleccionar un cliente.',
            'monto.required' => 'El monto es requerido.',
            'monto.min' => 'El monto debe ser mayor a 0.',
            'id_referencia.required' => 'Debe seleccionar un turno o venta.',
        ]);

        // Validar que el monto no supere el saldo pendiente

        if ($tipoReal === 'turno' && $this->turnoSeleccionado) {
            $saldoPendiente = $this->turnoSeleccionado->saldoPendiente();
            $montoFloat = (float) $this->monto;

            // Redondear ambos valores a 2 decimales para evitar problemas de precisión
            $montoRedondeado = round($montoFloat, 2);
            $saldoRedondeado = round($saldoPendiente, 2);

            // Comparar valores redondeados a 2 decimales
            if ($montoRedondeado > $saldoRedondeado) {
                $this->dispatch('mostrarMensaje',
                    mensaje: "El monto ingresado ($" . number_format($montoRedondeado, 2) . ") supera el saldo pendiente ($" . number_format($saldoRedondeado, 2) . ").",
                    tipo: 'error'
                );
                return;
            }
        } elseif ($tipoReal === 'venta' && $this->ventaSeleccionada) {
            $saldoPendiente = $this->ventaSeleccionada->saldoPendiente();
            $montoFloat = (float) $this->monto;

            // Redondear ambos valores a 2 decimales para evitar problemas de precisión
            $montoRedondeado = round($montoFloat, 2);
            $saldoRedondeado = round($saldoPendiente, 2);

            // Comparar valores redondeados a 2 decimales
            if ($montoRedondeado > $saldoRedondeado) {
                $this->dispatch('mostrarMensaje',
                    mensaje: "El monto ingresado ($" . number_format($montoRedondeado, 2) . ") supera el saldo pendiente ($" . number_format($saldoRedondeado, 2) . ").",
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

            // Usar el tipo parseado directamente del id_referencia
            if ($tipoReal === 'turno') {
                $datosPago['turno_id'] = $idReal;
            } elseif ($tipoReal === 'venta') {
                $datosPago['venta_id'] = $idReal;
            }

            $pagoCreado = Pago::create($datosPago);

            // Recargar el turno/venta para calcular el nuevo saldo
            $mensaje = 'Pago registrado exitosamente.';
            if ($tipoReal === 'turno' && $this->turnoSeleccionado) {
                $this->turnoSeleccionado->refresh();
                $saldoRestante = $this->turnoSeleccionado->saldoPendiente();
                if ($saldoRestante <= 0) {
                    $mensaje = 'Pago registrado exitosamente. El turno ha sido pagado completamente.';
                } else {
                    $mensaje = 'Pago registrado exitosamente. Saldo pendiente: $' . number_format($saldoRestante, 0);
                }
            } elseif ($tipoReal === 'venta' && $this->ventaSeleccionada) {
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
            $this->reset(['id_referencia', 'id_cliente', 'monto', 'tipo', 'turnoSeleccionado', 'ventaSeleccionada']);

        } catch (\Exception $e) {
            $this->dispatch('mostrarMensaje',
                mensaje: 'Error al registrar el pago: ' . $e->getMessage(),
                tipo: 'error'
            );
        }
    }

    public function generarPreferenciaMercadoPago()
    {
        Log::info('=== INICIO generarPreferenciaMercadoPago ===', [
            'id_referencia' => $this->id_referencia,
            'id_cliente' => $this->id_cliente,
            'monto' => $this->monto,
            'tipo' => $this->tipo
        ]);

        // Parsear id_referencia para obtener el ID real
        $idReal = null;
        if ($this->id_referencia) {
            $partes = explode('-', $this->id_referencia);
            if (count($partes) === 2) {
                $idReal = $partes[1];
            }
        }

        Log::info('ID parseado', ['idReal' => $idReal]);

        // Validar datos antes de generar preferencia
        $rules = [
            'id_cliente' => 'required|exists:clientes,id',
            'monto' => 'required|numeric|min:0.01',
            'id_referencia' => 'required',
        ];

        try {
            $this->validate($rules, [
                'id_cliente.required' => 'Debe seleccionar un cliente.',
                'monto.required' => 'El monto es requerido.',
                'monto.min' => 'El monto debe ser mayor a 0.',
                'id_referencia.required' => 'Debe seleccionar un turno o venta antes de continuar.',
            ]);
            Log::info('Validación exitosa');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación', ['errors' => $e->errors()]);
            // Mostrar mensaje adicional cuando hay errores de validación
            $this->dispatch('mostrarMensaje',
                mensaje: 'Por favor complete todos los campos requeridos antes de continuar.',
                tipo: 'error'
            );
            throw $e; // Re-lanzar la excepción para que Livewire maneje los errores en los campos
        }

        // Validar que el monto sea mayor a 0
        if ($this->monto <= 0) {
            $this->dispatch('mostrarMensaje',
                mensaje: 'El monto debe ser mayor a cero.',
                tipo: 'error'
            );
            return;
        }

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
            // Crear el pago en estado pendiente
            $datosPago = [
                'cliente_id' => $this->id_cliente,
                'monto' => $this->monto,
                'metodo_pago' => 'mercadopago',
                'fecha_pago' => now(),
                'estado' => 'pendiente',
            ];

            if ($this->tipo === 'turno') {
                $datosPago['turno_id'] = $idReal; // Usar ID real parseado
            } elseif ($this->tipo === 'venta') {
                $datosPago['venta_id'] = $idReal; // Usar ID real parseado
            }

            $pago = Pago::create($datosPago);

            // Obtener información del cliente
            $cliente = Cliente::find($this->id_cliente);

            // Preparar items para Mercado Pago
            $items = [];

            Log::info('Preparando items', ['tipo' => $this->tipo, 'idReal' => $idReal]);

            if ($this->tipo === 'venta' && $idReal) {
                // Cargar la venta con sus productos desde la BD usando el ID parseado
                $venta = Venta::with('productos')->find($idReal);

                Log::info('Venta cargada', [
                    'venta_id' => $venta ? $venta->id : 'null',
                    'productos_count' => $venta ? $venta->productos->count() : 0
                ]);

                if ($venta && $venta->productos->count() > 0) {
                    // Crear un item por cada producto
                    foreach ($venta->productos as $producto) {
                        $items[] = [
                            'title' => $producto->nombre,
                            'quantity' => (int) $producto->pivot->cantidad,
                            'unit_price' => (float) $producto->pivot->precio_unitario,
                            'currency_id' => 'ARS',
                        ];
                    }
                    Log::info('Items de productos creados', ['items_count' => count($items)]);
                } else {
                    // Si no hay productos, crear un item genérico
                    $items[] = [
                        'title' => "Pago de venta #{$idReal}",
                        'quantity' => 1,
                        'unit_price' => (float) $this->monto,
                        'currency_id' => 'ARS',
                    ];
                    Log::info('Item genérico de venta creado');
                }
            } else {
                // Para turnos o ventas sin productos, crear un item genérico
                $descripcion = $this->tipo === 'turno'
                    ? "Pago de turno #{$idReal}"
                    : "Pago de venta #{$idReal}";

                $items[] = [
                    'title' => $descripcion,
                    'quantity' => 1,
                    'unit_price' => (float) $this->monto,
                    'currency_id' => 'ARS',
                ];
                Log::info('Item genérico creado', ['descripcion' => $descripcion]);
            }

            // Validar que los items no estén vacíos
            if (empty($items)) {
                throw new \Exception('No se pudieron generar los items para el pago.');
            }

            $data = [
                'items' => $items,
                'payer' => [
                    'name' => $cliente->nombre,
                    'email' => $cliente->email ?? 'cliente@bellezaspa.com',
                    'phone' => [
                        'number' => $cliente->telefono ?? ''
                    ]
                ],
                'external_reference' => (string) $pago->id, // ID del pago en nuestra DB
            ];

            // Generar preferencia usando el servicio
            $mercadoPagoService = new MercadoPagoService();
            $preferencia = $mercadoPagoService->crearPreferencia($data);

            if ($preferencia && isset($preferencia['preference_id'])) {
                // Guardar el preference_id en el pago
                $pago->update([
                    'mercadopago_preference_id' => $preferencia['preference_id']
                ]);

                // Usar init_point para producción o sandbox_init_point para testing
                $checkoutUrl = $preferencia['init_point'] ?? $preferencia['sandbox_init_point'] ?? null;

                if (!$checkoutUrl) {
                    throw new \Exception('No se pudo obtener la URL de pago de Mercado Pago.');
                }

                $this->mercadopago_url = $checkoutUrl;
                $this->mercadopago_preference_id = $preferencia['preference_id'];

                // Generar URL del código QR usando API gratuita
                $this->mercadopago_qr = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($checkoutUrl);
                $this->mostrarQR = true;

                // Log para debugging
                Log::info('Preferencia de Mercado Pago creada exitosamente', [
                    'pago_id' => $pago->id,
                    'preference_id' => $preferencia['preference_id'],
                    'url' => $checkoutUrl,
                    'qr' => $this->mercadopago_qr,
                    'mostrarQR' => $this->mostrarQR,
                    'monto' => $this->monto,
                    'items' => $items
                ]);

                // Mostrar mensaje de éxito
                $this->dispatch('mostrarMensaje',
                    mensaje: '✅ Link de pago generado exitosamente! Haz clic en el botón o escanea el QR.',
                    tipo: 'success'
                );

                Log::info('=== FIN generarPreferenciaMercadoPago EXITOSO ===');

                // No redirigir automáticamente, dejar que el usuario haga clic en el botón o escanee el QR
                // El botón y QR aparecerán en la vista

            } else {
                // Si falla, eliminar el pago pendiente
                $pago->delete();

                $this->dispatch('mostrarMensaje',
                    mensaje: 'Error al generar el link de pago de Mercado Pago. Verifique su configuración.',
                    tipo: 'error'
                );
            }

        } catch (\Exception $e) {
            // Log detallado del error
            Log::error('=== ERROR en generarPreferenciaMercadoPago ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tipo' => $this->tipo,
                'id_referencia' => $this->id_referencia,
                'id_cliente' => $this->id_cliente,
                'monto' => $this->monto
            ]);

            $this->dispatch('mostrarMensaje',
                mensaje: 'Error al generar preferencia de Mercado Pago: ' . $e->getMessage(),
                tipo: 'error'
            );

            Log::info('=== FIN generarPreferenciaMercadoPago CON ERROR ===');
        }
    }
}
