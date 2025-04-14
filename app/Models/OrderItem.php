<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'name',
        'price',
        'quantity',
        'total',
    ];

    /**
     * Get the order that owns the item
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant for this item if it exists
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Calculate the total for this item
     */
    public function calculateTotal(): float
    {
        return $this->price * $this->quantity;
    }

    /**
     * Set the total based on price and quantity
     */
    protected static function booted()
    {
        static::creating(function ($item) {
            $item->total = $item->calculateTotal();
        });

        static::updating(function ($item) {
            $item->total = $item->calculateTotal();
        });
    }
}
