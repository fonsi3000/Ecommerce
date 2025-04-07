<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;

class CategoriaController extends Controller
{
    public function show(string $slug)
    {
        $categoria = Category::where('slug', $slug)->firstOrFail();
        $productos = $categoria->products()->with('category', 'subCategory')->get();

        return view('categorias.index', compact('categoria', 'productos'));
    }

    public function subcategoria(string $categoria_slug, string $subcategoria_slug)
    {
        $categoria = Category::where('slug', $categoria_slug)->firstOrFail();
        $subcategoria = SubCategory::where('slug', $subcategoria_slug)
            ->where('category_id', $categoria->id)
            ->firstOrFail();

        $productos = $subcategoria->products()->with('category', 'subCategory')->get();

        return view('categorias.subcategoria', compact('categoria', 'subcategoria', 'productos'));
    }
}
