<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    protected $client;

    public function __construct()
    {
        // Configurar las credenciales de Mercado Pago
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    /**
     * Crear una preferencia de pago
     *
     * @param array $data
     * @return array|null
     */
    public function crearPreferencia(array $data)
    {
        try {
            // Validar que existan los items
            if (empty($data['items'])) {
                throw new \Exception('No se proporcionaron items para la preferencia.');
            }

            // Validar que cada item tenga los campos requeridos
            foreach ($data['items'] as $item) {
                if (!isset($item['title']) || !isset($item['quantity']) || !isset($item['unit_price'])) {
                    throw new \Exception('Los items deben tener title, quantity y unit_price.');
                }
            }

            $client = new PreferenceClient();

            // Construir la preferencia
            $preferenceData = [
                "items" => $data['items'],
                "payer" => $data['payer'] ?? null,
                "back_urls" => [
                    "success" => url('/mercadopago/success'),
                    "failure" => url('/mercadopago/failure'),
                    "pending" => url('/mercadopago/pending')
                ],
                // "auto_return" => "approved", // Solo funciona con HTTPS - descomentar en producción
                "external_reference" => $data['external_reference'],
                "statement_descriptor" => "Belleza Spa",
                // "notification_url" => url('/mercadopago/webhook'), // Solo funciona con dominio público
            ];

            Log::info('Creando preferencia de Mercado Pago', [
                'items' => $data['items'],
                'external_reference' => $data['external_reference']
            ]);

            $preference = $client->create($preferenceData);

            // Verificar que la preferencia se creó correctamente
            if (!$preference || !isset($preference->id)) {
                throw new \Exception('La respuesta de Mercado Pago no contiene el ID de preferencia.');
            }

            $result = [
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point ?? null, // Para web (producción)
                'sandbox_init_point' => $preference->sandbox_init_point ?? null, // Para sandbox (testing)
            ];

            Log::info('Preferencia de Mercado Pago creada exitosamente', $result);

            return $result;

        } catch (MPApiException $e) {
            Log::error('Error de API al crear preferencia de Mercado Pago', [
                'message' => $e->getMessage(),
                'status_code' => $e->getApiResponse() ? $e->getApiResponse()->getStatusCode() : 'N/A',
                'content' => $e->getApiResponse() ? $e->getApiResponse()->getContent() : 'N/A',
                'items' => $data['items'] ?? []
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error general al crear preferencia de Mercado Pago', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'items' => $data['items'] ?? []
            ]);
            return null;
        }
    }

    /**
     * Obtener información de un pago
     *
     * @param string $paymentId
     * @return object|null
     */
    public function obtenerPago($paymentId)
    {
        try {
            $client = new \MercadoPago\Client\Payment\PaymentClient();
            return $client->get($paymentId);
        } catch (\Exception $e) {
            Log::error('Error al obtener pago de Mercado Pago', [
                'payment_id' => $paymentId,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Procesar notificación de webhook
     *
     * @param array $data
     * @return object|null
     */
    public function procesarWebhook(array $data)
    {
        try {
            if (isset($data['type']) && $data['type'] === 'payment') {
                $paymentId = $data['data']['id'];
                return $this->obtenerPago($paymentId);
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Error al procesar webhook de Mercado Pago', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }
}
