<?php

use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProductoController;
use App\Http\Controllers\Frontend\CategoriaController;
use App\Http\Controllers\Frontend\SearchController;

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

// Ruta API para búsqueda AJAX (autocompletar)
Route::get('/api/buscar-productos', [SearchController::class, 'searchAPI']);

// Página de resultados de búsqueda
Route::get('/buscar', [SearchController::class, 'index'])->name('productos.buscar');

// Carrito 
Route::get('/carrito', [CartController::class, 'index'])->name('carrito');
Route::post('/carrito/info-productos', [CartController::class, 'productsInfo'])->name('cart.products.info');
Route::post('/carrito/validar-stock', [CartController::class, 'validateStock'])->name('cart.validate.stock');

// Checkout - nuevas rutas
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/confirmacion/{orderId}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
