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
     * Get all items for this order
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate the total of the order from its items
     */
    public function calculateTotal(): float
    {
        return $this->items->sum('total');
    }

    /**
     * Update the total of the order based on its items
     */
    public function updateTotal(): void
    {
        $this->update([
            'total' => $this->calculateTotal()
        ]);
    }
}
