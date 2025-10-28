<?php

namespace Database\Seeders;

use App\Models\Notificacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notificaciones = [
            [
                'cliente_id' => 1,
                'mensaje' => 'Recordatorio: Tiene un turno mañana a las 10:00 para Facial Hidratante',
                'tipo' => 'recordatorio',
                'fecha_envio' => '2025-10-07 18:00:00',
                'estado' => 'enviado',
            ],
            [
                'cliente_id' => 2,
                'mensaje' => 'Recordatorio: Tiene un turno mañana a las 14:00 para Masaje Relajante',
                'tipo' => 'recordatorio',
                'fecha_envio' => '2025-10-07 18:00:00',
                'estado' => 'enviado',
            ],
            [
                'cliente_id' => 3,
                'mensaje' => 'Su turno ha sido confirmado para el 09/10/2025 a las 11:00',
                'tipo' => 'confirmacion',
                'fecha_envio' => '2025-10-08 09:00:00',
                'estado' => 'enviado',
            ],
            [
                'cliente_id' => 4,
                'mensaje' => '¡Bienvenida! Estamos muy contentos de recibirla en nuestro spa',
                'tipo' => 'bienvenida',
                'fecha_envio' => '2025-10-09 10:00:00',
                'estado' => 'enviado',
            ],
            [
                'cliente_id' => 5,
                'mensaje' => 'Recordatorio: Tiene un turno mañana a las 09:00 para Lifting de Pestañas',
                'tipo' => 'recordatorio',
                'fecha_envio' => '2025-10-09 18:00:00',
                'estado' => 'enviado',
            ],
            [
                'cliente_id' => 1,
                'mensaje' => 'Gracias por su visita. ¡Esperamos verla pronto!',
                'tipo' => 'agradecimiento',
                'fecha_envio' => '2025-10-08 11:30:00',
                'estado' => 'enviado',
            ],
            [
                'cliente_id' => 3,
                'mensaje' => '¡Oferta especial! 20% de descuento en Pedicura Spa este mes',
                'tipo' => 'promocion',
                'fecha_envio' => '2025-10-10 10:00:00',
                'estado' => 'pendiente',
            ],
        ];

        foreach ($notificaciones as $notificacion) {
            Notificacion::create($notificacion);
        }
    }
}
