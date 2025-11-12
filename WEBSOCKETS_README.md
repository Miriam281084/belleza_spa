# WebSockets con Laravel Reverb - Belleza Spa Victoria

## Descripción

Este proyecto implementa WebSockets para actualización en tiempo real del stock de productos usando **Laravel Reverb**, la solución oficial de Laravel para WebSockets.

## ¿Qué hace?

Cuando el stock de un producto cambia (por venta, ajuste manual, etc.), todos los usuarios conectados verán la actualización automáticamente sin necesidad de recargar la página.

### Eventos implementados:

1. **StockActualizado** - Se emite cuando cambia el stock de un producto
2. **ProductoAgotado** - Se emite cuando un producto llega a stock 0
3. **ProductoDisponible** - Se emite cuando un producto pasa de 0 a stock > 0

### Componentes afectados:

- **ProductosIndex** (Admin - Gestión de productos)
- **ClienteProductos** (Clientes - Catálogo)
- **VentasCrear** (Admin/Clientes - Carrito de compras)

---

## Configuración inicial (Ya está hecha)

La configuración ya está completa en tu proyecto. Estos son los pasos que se realizaron:

1. ✅ Instalación de Laravel Reverb
2. ✅ Configuración en `.env`
3. ✅ Instalación de dependencias frontend (Laravel Echo + Pusher JS)
4. ✅ Configuración de eventos de Broadcasting
5. ✅ Integración con componentes Livewire

---

## Cómo usar

### 1. Iniciar el servidor de Reverb

Para que los WebSockets funcionen, **debes tener el servidor Reverb ejecutándose**.

Abre una terminal y ejecuta:

```bash
php artisan reverb:start
```

Deberías ver algo como:

```
  INFO  Starting server on 0.0.0.0:8080, tls: http.

  INFO  Listening on port 8080.
```

**IMPORTANTE:** Mantén esta terminal abierta mientras uses la aplicación. Si cierras esta ventana, los WebSockets dejarán de funcionar.

### 2. Iniciar el servidor Laravel (en otra terminal)

En una segunda terminal, inicia tu servidor Laravel:

```bash
php artisan serve
```

O si usas XAMPP, asegúrate de que Apache esté corriendo.

### 3. Probar la funcionalidad

#### Prueba básica (2 navegadores):

1. Abre el navegador 1 en la gestión de productos: `http://127.0.0.1:9000/productos`
2. Abre el navegador 2 en la misma página o en el catálogo de clientes
3. En el navegador 1, incrementa o decrementa el stock de un producto
4. **Resultado esperado:** El navegador 2 debería actualizarse automáticamente mostrando el nuevo stock

#### Prueba avanzada (Venta):

1. Navegador 1: Página de gestión de productos
2. Navegador 2: Página de carrito de compras
3. En el navegador 2, procesa una venta de un producto
4. **Resultado esperado:** El navegador 1 debería mostrar el stock actualizado automáticamente

---

## Verificar que funciona

### 1. Consola del navegador

Abre las DevTools (F12) y ve a la pestaña Console. Deberías ver mensajes como:

```
Stock actualizado: {producto_id: 5, nombre: "Crema Facial", stock_nuevo: 15, ...}
```

### 2. Notificaciones visuales

- En la página de productos (admin), verás notificaciones cuando el stock cambie
- Los badges de stock se actualizarán automáticamente
- Si un producto se agota, verás un alert rojo

---

## Solución de problemas

### ❌ Los WebSockets no funcionan

**Problema:** No se actualiza automáticamente cuando cambia el stock.

**Solución:**

1. Verifica que el servidor Reverb esté corriendo:
   ```bash
   php artisan reverb:start
   ```

2. Verifica que las variables de entorno estén correctas en `.env`:
   ```env
   BROADCAST_CONNECTION=reverb
   REVERB_APP_ID=849009
   REVERB_APP_KEY=2xwquii6lfrjzdnz4m1w
   REVERB_APP_SECRET=vxkfu02uqgklzoqplogq
   REVERB_HOST="localhost"
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

3. Limpia la caché de configuración:
   ```bash
   php artisan config:clear
   ```

4. Verifica que los assets estén compilados:
   ```bash
   npm run build
   ```

### ❌ Error: "Echo is not defined"

**Solución:**

1. Asegúrate de que Vite esté cargando correctamente:
   ```bash
   npm run build
   ```

2. Verifica que el layout `app.blade.php` tenga:
   ```blade
   @vite(['resources/css/app.css', 'resources/js/app.js'])
   ```

### ❌ Error de conexión al puerto 8080

**Problema:** El servidor Reverb no inicia o hay conflicto de puertos.

**Solución:**

1. Verifica que el puerto 8080 no esté en uso:
   ```bash
   netstat -ano | findstr :8080
   ```

2. Si está en uso, cambia el puerto en `.env`:
   ```env
   REVERB_PORT=8081
   ```

3. Reinicia Reverb con el nuevo puerto

---

## Comandos útiles

### Desarrollo

```bash
# Terminal 1: Servidor Reverb
php artisan reverb:start

# Terminal 2: Servidor Laravel
php artisan serve

# Terminal 3 (opcional): Compilar assets en modo watch
npm run dev
```

### Producción

```bash
# Compilar assets para producción
npm run build

# Iniciar Reverb en background (Linux/Mac)
php artisan reverb:start > /dev/null 2>&1 &

# Windows: Usar un gestor de procesos como PM2 o ejecutar como servicio
```

---

## Arquitectura técnica

### Backend (Laravel)

**Eventos de Broadcasting:**
- `App\Events\StockActualizado`
- `App\Events\ProductoAgotado`
- `App\Events\ProductoDisponible`

**Componentes Livewire que emiten eventos:**
- `App\Livewire\ProductosIndex` - Métodos: `guardar()`, `agregarStock()`, `restarStock()`
- `App\Livewire\VentasCrear` - Método: `procesarVenta()`

**Canal público:**
- `stock` - Canal público al que se conectan todos los usuarios

### Frontend (JavaScript)

**Librería:** Laravel Echo + Pusher JS

**Configuración:** `resources/js/bootstrap.js`

**Listeners:** Scripts en cada vista Blade usando `@push('scripts')`

---

## Escalabilidad

### Para producción con más usuarios:

1. **Usar Redis como backend de broadcasting:**
   ```env
   BROADCAST_CONNECTION=redis
   ```

2. **Configurar Reverb con más workers:**
   ```bash
   php artisan reverb:start --workers=4
   ```

3. **Usar supervisor para mantener Reverb corriendo:**
   ```ini
   [program:reverb]
   command=php /path/to/belleza_spa_victoria/artisan reverb:start
   autostart=true
   autorestart=true
   ```

---

## Próximas mejoras (Opcional)

- [ ] Notificaciones de stock bajo (< 5 unidades)
- [ ] Alertas cuando otro usuario está editando un producto
- [ ] Indicadores de "Usuarios conectados"
- [ ] Histórico de cambios de stock en tiempo real

---

## Soporte

Si tienes problemas con WebSockets:

1. Revisa los logs de Laravel: `storage/logs/laravel.log`
2. Revisa la consola del navegador (F12)
3. Verifica que el servidor Reverb esté corriendo
4. Ejecuta `php artisan config:clear` y `php artisan cache:clear`

---

## Créditos

- **Laravel Reverb:** https://laravel.com/docs/11.x/reverb
- **Laravel Echo:** https://laravel.com/docs/11.x/broadcasting#client-side-installation
- **Implementado para:** Belleza Spa Victoria

---

## Licencia

Mismo que el proyecto Laravel principal.
