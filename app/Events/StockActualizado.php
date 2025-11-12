<?php

namespace App\Events;

use App\Models\Producto;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockActualizado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $producto;
    public $stockAnterior;
    public $stockNuevo;

    /**
     * Create a new event instance.
     */
    public function __construct(Producto $producto, $stockAnterior, $stockNuevo)
    {
        $this->producto = $producto;
        $this->stockAnterior = $stockAnterior;
        $this->stockNuevo = $stockNuevo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('stock'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'stock.actualizado';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'producto_id' => $this->producto->id,
            'nombre' => $this->producto->nombre,
            'stock_anterior' => $this->stockAnterior,
            'stock_nuevo' => $this->stockNuevo,
            'categoria' => $this->producto->categoria,
            'precio' => $this->producto->precio,
        ];
    }
}
