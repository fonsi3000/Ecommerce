<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_id',
        'image_path',
    ];

    // Relación con Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relación con Attribute (opcional)
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
