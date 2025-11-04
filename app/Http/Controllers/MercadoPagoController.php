<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Página de éxito después del pago
     */
    public function success(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $preferenceId = $request->query('preference_id');
        $status = $request->query('status');

        try {
            if ($paymentId) {
                // Obtener información del pago desde Mercado Pago
                $pagoMP = $this->mercadoPagoService->obtenerPago($paymentId);

                if ($pagoMP) {
                    // Buscar el pago en nuestra base de datos por preference_id
                    $pago = Pago::where('mercadopago_preference_id', $preferenceId)->first();

                    if ($pago && $pagoMP->status === 'approved') {
                        // Actualizar el pago a completado
                        $pago->update([
                            'estado' => 'completado',
                            'mercadopago_payment_id' => $paymentId,
                            'fecha_pago' => now(),
                        ]);

                        return view('mercadopago.success', [
                            'pago' => $pago,
                            'mensaje' => 'Pago completado exitosamente'
                        ]);
                    }
                }
            }

            return view('mercadopago.success', [
                'pago' => null,
                'mensaje' => 'Pago en proceso de confirmación'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en success de Mercado Pago', [
                'message' => $e->getMessage(),
                'payment_id' => $paymentId
            ]);

            return view('mercadopago.success', [
                'pago' => null,
                'mensaje' => 'Pago recibido, se confirmará en breve'
            ]);
        }
    }

    /**
     * Página de fallo después del pago
     */
    public function failure(Request $request)
    {
        $preferenceId = $request->query('preference_id');

        try {
            // Buscar el pago y marcarlo como fallido o eliminarlo
            $pago = Pago::where('mercadopago_preference_id', $preferenceId)->first();

            if ($pago && $pago->estado === 'pendiente') {
                // Eliminar el pago pendiente
                $pago->delete();
            }
        } catch (\Exception $e) {
            Log::error('Error en failure de Mercado Pago', [
                'message' => $e->getMessage()
            ]);
        }

        return view('mercadopago.failure');
    }

    /**
     * Página de pago pendiente
     */
    public function pending(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $preferenceId = $request->query('preference_id');

        try {
            if ($paymentId) {
                // Buscar el pago en nuestra base de datos
                $pago = Pago::where('mercadopago_preference_id', $preferenceId)->first();

                if ($pago) {
                    $pago->update([
                        'mercadopago_payment_id' => $paymentId,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error en pending de Mercado Pago', [
                'message' => $e->getMessage()
            ]);
        }

        return view('mercadopago.pending');
    }

    /**
     * Webhook para recibir notificaciones de Mercado Pago
     */
    public function webhook(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('Webhook recibido de Mercado Pago', $data);

            // Mercado Pago envía notificaciones del tipo 'payment'
            if (isset($data['type']) && $data['type'] === 'payment') {
                $paymentId = $data['data']['id'];

                // Obtener información del pago
                $pagoMP = $this->mercadoPagoService->obtenerPago($paymentId);

                if ($pagoMP) {
                    // Buscar el pago en nuestra base de datos por external_reference
                    $pago = Pago::find($pagoMP->external_reference);

                    if ($pago) {
                        // Actualizar según el estado del pago
                        if ($pagoMP->status === 'approved') {
                            $pago->update([
                                'estado' => 'completado',
                                'mercadopago_payment_id' => $paymentId,
                                'fecha_pago' => now(),
                            ]);
                        } elseif ($pagoMP->status === 'rejected' || $pagoMP->status === 'cancelled') {
                            $pago->delete();
                        }

                        Log::info('Pago actualizado desde webhook', [
                            'pago_id' => $pago->id,
                            'estado_mp' => $pagoMP->status,
                            'estado_db' => $pago->estado
                        ]);
                    }
                }
            }

            // Mercado Pago espera un status 200 o 201
            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Error procesando webhook de Mercado Pago', [
                'message' => $e->getMessage(),
                'data' => $request->all()
            ]);

            // Aún así devolver 200 para que Mercado Pago no reintente
            return response()->json(['status' => 'error'], 200);
        }
    }
}
