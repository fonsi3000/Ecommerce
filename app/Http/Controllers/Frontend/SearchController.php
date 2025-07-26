<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * API endpoint para búsqueda de productos (para autocompletar)
     */
    public function searchAPI(Request $request)
    {
        $query = $request->input('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $productos = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'slug', 'price', 'image')
            ->orderBy('name')
            ->limit(5)
            ->get();

        return response()->json($productos);
    }

    /**
     * Muestra la página completa de resultados de búsqueda
     */
    public function index(Request $request)
    {
        $query = $request->input('q');

        $productos = collect([]);
        $categoriaCount = [];

        if ($query && strlen($query) >= 2) {
            // Aplicar ordenación si se solicita
            $sort = $request->input('sort', 'nombre_asc');

            $productosQuery = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->with(['category', 'subCategory']); // Carga relaciones

            // Aplicar ordenación
            switch ($sort) {
                case 'precio_asc':
                    $productosQuery->orderBy('price', 'asc');
                    break;
                case 'precio_desc':
                    $productosQuery->orderBy('price', 'desc');
                    break;
                case 'nombre_desc':
                    $productosQuery->orderBy('name', 'desc');
                    break;
                case 'nombre_asc':
                default:
                    $productosQuery->orderBy('name', 'asc');
                    break;
            }

            // Mostrar todos los productos (sin paginación)
            $productos = $productosQuery->get();

            // Contar productos por categoría para filtros
            $categoriaCount = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->select('category_id')
                ->with('category:id,name,slug')
                ->get()
                ->groupBy('category.name')
                ->map(function ($item) {
                    return $item->count();
                });
        }

        // Obtener categorías principales (todas las categorías, sin filtrar por parent_id)
        $menuCategories = Category::withCount('subCategories')
            ->orderBy('name')
            ->get();

        return view('layouts.search', [
            'productos' => $productos,
            'query' => $query,
            'categoriaCount' => $categoriaCount,
            'menuCategories' => $menuCategories,
        ]);
    }
}
