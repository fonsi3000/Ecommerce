<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index');
    }

    /**
     * Devuelve información de productos incluyendo stock disponible
     */
    public function productsInfo(Request $request)
    {
        $ids = $request->input('ids', []);

        $products = Product::with(['variants:id,product_id,name,stock'])
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'slug', 'price', 'image', 'stock']);

        // Transformar la respuesta para que Alpine la entienda mejor
        $transformed = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'image' => $product->image,
                'stock' => $product->stock,
                'variants' => $product->variants->mapWithKeys(function ($variant) {
                    return [$variant->id => $variant->stock];
                })
            ];
        });

        return response()->json($transformed);
    }


    /**
     * Valida el stock de los productos en el carrito
     * Mejorado para proveer información más detallada
     */
    public function validateStock(Request $request)
    {
        $items = $request->input('items', []);
        $errors = [];

        foreach ($items as $item) {
            $product = Product::with('variants')->find($item['id']);

            if (!$product) {
                $errors[] = [
                    'id' => $item['id'],
                    'error' => 'Producto no encontrado',
                    'available_stock' => 0
                ];
                continue;
            }

            if (isset($item['variant_id']) && $item['variant_id']) {
                $variant = $product->variants()->find($item['variant_id']);

                if (!$variant) {
                    $errors[] = [
                        'id' => $item['id'],
                        'variant_id' => $item['variant_id'],
                        'error' => 'Variante no encontrada',
                        'available_stock' => 0
                    ];
                } else if ($variant->stock < $item['quantity']) {
                    $errors[] = [
                        'id' => $item['id'],
                        'variant_id' => $item['variant_id'],
                        'error' => 'Stock insuficiente en variante',
                        'available_stock' => $variant->stock,
                        'requested_quantity' => $item['quantity']
                    ];
                }
            } else {
                $stock = $product->stock;

                if ($stock < $item['quantity']) {
                    $errors[] = [
                        'id' => $item['id'],
                        'error' => 'Stock insuficiente',
                        'available_stock' => $stock,
                        'requested_quantity' => $item['quantity']
                    ];
                }
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'valid' => false,
                'errors' => $errors
            ], 422);
        }

        return response()->json(['valid' => true]);
    }
}
