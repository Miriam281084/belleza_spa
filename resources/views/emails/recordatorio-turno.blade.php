<x-mail::message>
# Recordatorio de Turno

Hola **{{ $turno->cliente->nombre }}**,

Te recordamos que tienes un turno programado en **Belleza & SPA Victoria**.

<x-mail::panel>
**Detalles del Turno:**

- **Servicio:** {{ $turno->servicio->nombre }}
- **Fecha:** {{ $turno->fecha->format('d/m/Y') }}
- **Hora:** {{ $turno->fecha->format('H:i') }}
- **Empleado:** {{ $turno->empleado->nombre }}
- **Duración aproximada:** {{ $turno->servicio->duracion }} minutos
</x-mail::panel>

Por favor, llega con 5 minutos de anticipación.

Si necesitas cancelar o reprogramar tu turno, contáctanos lo antes posible.

Gracias por confiar en nosotros,<br>
**Belleza & SPA Victoria**

---
*Este es un mensaje automático, por favor no respondas a este correo.*
</x-mail::message>
