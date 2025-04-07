<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProductoController;
use App\Http\Controllers\Frontend\CategoriaController;

/*
|--------------------------------------------------------------------------
| Rutas del Frontend
|--------------------------------------------------------------------------
*/

// 🏠 Página de inicio con productos destacados
Route::get('/', [ProductoController::class, 'destacados'])->name('inicio');

// 🛍 Listado de todos los productos (opcional si haces una página de catálogo)
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');

// 🔍 Detalle de un producto individual
Route::get('/producto/{slug}', [ProductoController::class, 'show'])->name('producto.show');

// 📂 Productos por categoría
Route::get('/categoria/{slug}', [CategoriaController::class, 'show'])->name('categoria.show');

// 📁 Productos por subcategoría (solo aplica a Maquillaje)
Route::get('/categoria/{categoria_slug}/{subcategoria_slug}', [CategoriaController::class, 'subcategoria'])
    ->name('categoria.subcategoria');
