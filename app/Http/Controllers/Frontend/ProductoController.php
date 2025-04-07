<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductoController extends Controller
{
    /**
     * Muestra la página de inicio con productos destacados.
     */
    public function destacados()
    {
        $productos = Product::latest()->paginate(16);
        return view('inicio', compact('productos'));
    }

    /**
     * Muestra el detalle de un producto específico.
     */
    public function show($slug)
    {
        $producto = Product::with([
            'category',
            'subCategory',
            'images.attribute' // Carga imágenes con sus atributos
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Atributos únicos relacionados con el producto (por medio de sus imágenes)
        $atributos = $producto->images
            ->filter(fn($img) => $img->attribute) // Solo si tiene atributo
            ->pluck('attribute')
            ->unique('id')
            ->values();

        // Productos relacionados de la misma categoría
        $relacionados = Product::where('category_id', $producto->category_id)
            ->where('id', '!=', $producto->id)
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('producto.show', compact('producto', 'relacionados', 'atributos'));
    }
}
