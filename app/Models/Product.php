<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Relación con la categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación con subcategoría
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    // Relación con imágenes del producto
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Relación con variantes del producto (color, tono, stock)
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESADORES Y MUTADORES
    |--------------------------------------------------------------------------
    */

    // Mutador para almacenar solo el nombre del archivo de imagen
    public function setImageAttribute($value)
    {
        if (is_string($value) && str_contains($value, 'storage/app/public/')) {
            $this->attributes['image'] = str_replace('storage/app/public/', '', $value);
        } elseif (is_string($value) && str_contains($value, DIRECTORY_SEPARATOR)) {
            $this->attributes['image'] = basename($value);
        } else {
            $this->attributes['image'] = $value;
        }
    }

    // Accesor para devolver la ruta completa de la imagen
    public function getImageAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // Si no incluye 'products/' ni es URL, se agrega el prefijo
        if (!str_contains($value, 'products/') && !str_starts_with($value, 'http')) {
            return 'products/' . $value;
        }

        return $value;
    }
}
