<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    // Relación con ProductImage (si vinculas imágenes a un atributo específico)
    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_product');
    }
}
