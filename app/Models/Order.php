<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'document',
        'address',
        'status',
        'total',
    ];

    /**
     * Relación con los ítems de la orden
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calcular el total de la orden desde sus ítems
     */
    public function calculateTotal(): float
    {
        return $this->items->sum('total');
    }

    /**
     * Actualizar el campo total con base en los ítems
     */
    public function updateTotal(): void
    {
        $this->update([
            'total' => $this->calculateTotal()
        ]);
    }

    /**
     * Evento para manejar lógica al actualizar la orden
     */
    protected static function booted()
    {
        static::updating(function (Order $order) {
            if ($order->isDirty('status') && $order->status === 'enviado') {
                foreach ($order->items as $item) {
                    // Si tiene variante, descuenta stock del variant
                    if ($item->variant && $item->variant->stock >= $item->quantity) {
                        $item->variant->decrement('stock', $item->quantity);
                    }
                    // Si no tiene variante, descuenta del producto directamente
                    elseif ($item->product && $item->product->stock >= $item->quantity) {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }
            }
        });
    }
}
