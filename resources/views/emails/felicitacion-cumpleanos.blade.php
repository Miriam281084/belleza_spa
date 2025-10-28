<x-mail::message>
# ¡Feliz Cumpleaños {{ $cliente->nombre }}!

En **Belleza & SPA Victoria** queremos desearte un día muy especial lleno de alegría y felicidad.

<x-mail::panel>
**Regalo Especial de Cumpleaños**

Como regalo, te ofrecemos un **15% de descuento** en tu próxima visita.

**Código:** CUMPLE{{ date('Y') }}

*Válido hasta {{ now()->addDays(30)->format('d/m/Y') }}*
</x-mail::panel>

Aprovecha este día especial para consentirte y disfrutar de nuestros servicios.

Esperamos verte pronto,<br>
**Todo el equipo de Belleza & SPA Victoria**

---
*Este es un mensaje automático, por favor no respondas a este correo.*
</x-mail::message>
