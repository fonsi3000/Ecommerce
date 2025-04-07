<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color',
        'tono',
        'stock',
    ];

    // Relación inversa al producto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
