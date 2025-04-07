<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // Relación con SubCategory
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    // Relación con Product
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
