<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'atributo_tipo',
        'color',
        'nombre_color',
        'tono',
        'nombre_tono',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
